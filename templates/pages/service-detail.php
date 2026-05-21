<?php
/**
 * templates/pages/service-detail.php
 * URL: /hizmetler/{slug}
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/public_profile.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: ' . ROOT_URL . 'hizmetler');
    exit;
}

$stmt = $connection->prepare(
    "SELECT * FROM services WHERE slug = ? AND is_active = 1 LIMIT 1"
);
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    http_response_code(404);
    require BASE_PATH . '/templates/pages/404.php';
    exit;
}

$service = $result->fetch_assoc();

// İlgili blog yazıları
$relatedPosts = $connection->prepare("
    SELECT title, slug, published_at
    FROM posts
    WHERE related_service_slug = ? AND is_published = 1
    ORDER BY published_at DESC
    LIMIT 3
");
$relatedPosts->bind_param('s', $slug);
$relatedPosts->execute();
$relatedPosts = $relatedPosts->get_result();

// Diğer hizmetler
$otherServices = $connection->prepare("
    SELECT slug, title, icon_class
    FROM services
    WHERE is_active = 1 AND slug != ?
    ORDER BY display_order ASC
    LIMIT 5
");
$otherServices->bind_param('s', $slug);
$otherServices->execute();
$otherServices = $otherServices->get_result();

// ── SEO ───────────────────────────────────────────────────────
$seo_title = !empty($service['meta_title'])
    ? $service['meta_title']
    : $service['title'] . ' | Psikolog Doğukan Kopuk — İzmit';

$seo_description = !empty($service['meta_desc'])
    ? $service['meta_desc']
    : mb_substr(strip_tags($service['summary'] ?? $service['title']), 0, 155, 'UTF-8');

$seo_canonical = SITE_URL . '/hizmetler/' . $service['slug'];
$seo_og_type   = 'website';
// OG: hizmet görseli varsa kullan, yoksa default 1200x630
$seo_og_image  = !empty($service['og_image'])
    ? $service['og_image']
    : (!empty($service['image'])
        ? getImageUrl($service['image'])
        : SITE_URL . '/images/og-default.png');

$seo_breadcrumbs = [
    ['name' => 'Anasayfa',  'url' => SITE_URL],
    ['name' => 'Hizmetler', 'url' => SITE_URL . '/hizmetler'],
    ['name' => $service['title'], 'url' => ''],
];

$seo_schemas = [
    schemaService([
        'title'    => $service['title'],
        'meta_desc' => $seo_description,
        'summary'  => $service['summary'],
        'slug'     => $service['slug'],
    ]),
    schemaLocalBusiness(),
];

require_once BASE_PATH . '/templates/partials/header.php';

$waMessage = urlencode($service['title'] . ' hakkında bilgi almak istiyorum.');
?>

<article class="service-detail">
    <div class="container">

        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <div class="service-detail__layout">

            <!-- Ana İçerik -->
            <div class="service-detail__main">

                <!-- Başlık -->
                <div class="service-detail__header">
                    <div class="hizmet-karti__ikon" aria-hidden="true">
                        <i class="<?= e($service['icon_class'] ?? 'uil uil-heart') ?>"></i>
                    </div>
                    <div>
                        <span class="category__button">Hizmet</span>
                        <h1><?= e($service['title']) ?></h1>
                        <?php if (!empty($service['summary'])): ?>
                        <p class="service-detail__lead">
                            <?= e($service['summary']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Fold Üstü CTA -->
                <div class="service-detail__top-cta">
                    <a href="<?= ROOT_URL ?>randevu" class="btn">
                        <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                        15 dk Ücretsiz Ön Görüşme
                    </a>
                    <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= $waMessage ?>"
                       class="btn btn--outline"
                       target="_blank" rel="noopener noreferrer">
                        <i class="uil uil-whatsapp" aria-hidden="true"></i>
                        WhatsApp
                    </a>
                </div>

                <!-- TinyMCE İçerik -->
                <?php if (!empty($service['content'])): ?>
                <div class="singlepost__body service-detail__body">
                    <?= $service['content'] /* TinyMCE WYSIWYG — admin-only input */ ?>
                </div>
                <?php endif; ?>

                <!-- İçerik Sonrası CTA -->
                <div class="post-cta">
                    <div class="post-cta__icon" aria-hidden="true">
                        <i class="uil uil-calendar-alt"></i>
                    </div>
                    <div class="post-cta__text">
                        <h3><?= e($service['title']) ?> İçin Randevu Alın</h3>
                        <p>
                            Ön görüşme ücretsizdir. En kısa sürede sizinle
                            iletişime geçeceğim.
                        </p>
                    </div>
                    <div class="post-cta__actions">
                        <a href="<?= ROOT_URL ?>randevu" class="btn">
                            Randevu Al
                        </a>
                        <a href="<?= ROOT_URL ?>hakkimda" class="btn btn--outline">
                            Hakkımda
                        </a>
                    </div>
                </div>

                <!-- İlgili Blog Yazıları -->
                <?php if ($relatedPosts && mysqli_num_rows($relatedPosts) > 0): ?>
                <div class="service-detail__related-posts">
                    <h2>Bu Konuyla İlgili Yazılar</h2>
                    <ul>
                        <?php while ($rp = mysqli_fetch_assoc($relatedPosts)): ?>
                        <li>
                            <i class="uil uil-file-alt" aria-hidden="true"></i>
                            <a href="<?= ROOT_URL ?>blog/<?= urlencode($rp['slug']) ?>">
                                <?= e($rp['title']) ?>
                            </a>
                            <small>
                                <?= turkceTarih($rp['published_at'], 'd F Y') ?>
                            </small>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div><!-- /.service-detail__main -->

            <!-- Sidebar -->
            <aside class="service-detail__sidebar">

                <!-- Hızlı İletişim -->
                <div class="service-sidebar__contact">
                    <h3>Hızlı İletişim</h3>
                    <p>Sorularınız için arayabilir veya mesaj gönderebilirsiniz.</p>
                    <a href="tel:<?= e(CONTACT_PHONE_HREF) ?>" class="btn btn--full">
                        <i class="uil uil-phone" aria-hidden="true"></i>
                        <?= e(CONTACT_PHONE) ?>
                    </a>
                    <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= $waMessage ?>"
                       class="btn btn--full btn--whatsapp"
                       target="_blank" rel="noopener noreferrer">
                        <i class="uil uil-whatsapp" aria-hidden="true"></i>
                        WhatsApp
                    </a>
                    <a href="<?= ROOT_URL ?>randevu" class="btn btn--full btn--outline">
                        <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                        Randevu Formu
                    </a>
                </div>

                <!-- Diğer Hizmetler -->
                <?php if ($otherServices && mysqli_num_rows($otherServices) > 0): ?>
                <div class="service-sidebar__others">
                    <h3>Diğer Hizmetler</h3>
                    <ul>
                        <?php while ($os = mysqli_fetch_assoc($otherServices)): ?>
                        <li>
                            <a href="<?= ROOT_URL ?>hizmetler/<?= urlencode($os['slug']) ?>">
                                <i class="<?= e($os['icon_class'] ?? 'uil uil-heart') ?>"
                                   aria-hidden="true"></i>
                                <?= e($os['title']) ?>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Uzman Bilgisi — E-E-A-T -->
                <?php $sdp = getPublicProfile(); ?>
                <div class="service-sidebar__expert">
                    <img src="<?= e(publicAvatarUrl()) ?>"
                         alt="<?= e($sdp['full_name']) ?> — <?= e($sdp['title']) ?>"
                         width="80" height="80" loading="lazy">
                    <h4><?= e($sdp['full_name']) ?></h4>
                    <p><?= e($sdp['title']) ?></p>
                    <a href="<?= ROOT_URL ?>hakkimda" class="service-sidebar__expert-link">
                        Hakkımda →
                    </a>
                </div>

            </aside>

        </div><!-- /.service-detail__layout -->
    </div>
</article>

<?php
// Trust block — service-detail alt bölüm
$trustVariant = 'compact';
$trustHeading = 'Uzman Hakkında';
?>
<section style="padding:var(--space-8) 0;">
    <div class="container" style="max-width:900px;">
        <?php require BASE_PATH . '/templates/partials/trust-block.php'; ?>
    </div>
</section>
<?php unset($trustVariant, $trustHeading); ?>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
