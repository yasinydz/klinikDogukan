<?php
/**
 * templates/pages/city.php
 * URL: /izmit-psikolog, /kocaeli-psikolog, /gebze-psikolog
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/public_profile.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$cityKey = trim($_GET['city'] ?? '');
$allowedCities = ['izmit', 'kocaeli', 'gebze'];

if (!in_array($cityKey, $allowedCities, true)) {
    header('Location: ' . ROOT_URL);
    exit;
}

// DB'den şehir verisini çek
$cityData = null;
$checkTable = mysqli_query($connection, "SHOW TABLES LIKE 'city_pages'");
if ($checkTable && mysqli_num_rows($checkTable) > 0) {
    $stmt = $connection->prepare(
        "SELECT * FROM city_pages WHERE city_key = ? AND is_active = 1 LIMIT 1"
    );
    $stmt->bind_param('s', $cityKey);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $cityData = $result->fetch_assoc();
    }
}

// Fallback veriler
$defaults = [
    'izmit'   => [
        'city_name'  => 'İzmit',
        'meta_title' => 'İzmit Psikolog | Doğukan Kopuk — Klinik Psikolog Randevu',
        'meta_desc'  => 'İzmit\'te klinik psikolog Doğukan Kopuk. Anksiyete ve depresyon için randevu alın. Ön görüşme ücretsizdir.',
        'h1_text'    => 'İzmit\'te Klinik Psikolog — Doğukan Kopuk',
        'content'    => '',
    ],
    'kocaeli' => [
        'city_name'  => 'Kocaeli',
        'meta_title' => 'Kocaeli Psikolog | Doğukan Kopuk — Klinik Psikolog Randevu',
        'meta_desc'  => 'Kocaeli\'de klinik psikolog Doğukan Kopuk. Bireysel terapi ve online seans seçeneği mevcuttur.',
        'h1_text'    => 'Kocaeli\'de Klinik Psikolog — Doğukan Kopuk',
        'content'    => '',
    ],
    'gebze'   => [
        'city_name'  => 'Gebze',
        'meta_title' => 'Gebze Psikolog | Doğukan Kopuk — Klinik Psikolog Randevu',
        'meta_desc'  => 'Gebze\'de klinik psikolog Doğukan Kopuk. Yüz yüze ve online terapi seçenekleri mevcuttur.',
        'h1_text'    => 'Gebze\'de Klinik Psikolog — Doğukan Kopuk',
        'content'    => '',
    ],
];

$d        = $cityData ?? $defaults[$cityKey];
$cityName = $d['city_name'] ?? ucfirst($cityKey);

$seo_title       = $d['meta_title'];
$seo_description = $d['meta_desc'];
$seo_canonical   = SITE_URL . '/' . $cityKey . '-psikolog';
$seo_og_type     = 'website';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa',    'url' => SITE_URL],
    ['name' => $cityName . ' Psikolog', 'url' => ''],
];

$seo_schemas = [
    schemaLocalBusiness($cityName),
    schemaPerson(),
];

// Aktif hizmetler
$services = mysqli_query($connection, "
    SELECT slug, title, summary, icon_class
    FROM services WHERE is_active = 1
    ORDER BY display_order ASC LIMIT 6
");

// Son 3 blog yazısı
$recentPosts = mysqli_query($connection, "
    SELECT title, slug, thumbnail, published_at
    FROM posts WHERE is_published = 1
    ORDER BY published_at DESC LIMIT 3
");

require_once BASE_PATH . '/templates/partials/header.php';
?>

<!-- Hero -->
<section class="sehir-hero">
    <div class="container sehir-hero__container">
        <div class="sehir-hero__text">

            <?php $breadcrumbs = $seo_breadcrumbs;
            require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

            <span class="category__button">
                <?= e($cityName) ?> · Klinik Psikolog
            </span>
            <h1><?= e($d['h1_text']) ?></h1>
            <p class="sehir-hero__desc">
                <?= e($cityName) ?>'de psikolojik destek arıyorsanız doğru yerdesiniz.
                Klinik Psikolog Doğukan Kopuk olarak anksiyete, depresyon, travma ve
                ilişkisel sorunlar başta olmak üzere pek çok alanda bilimsel ve
                insancıl bir yaklaşımla yanınızdayım.
            </p>

            <ul class="randevu-hero__trust">
                <li>
                    <i class="uil uil-map-marker" aria-hidden="true"></i>
                    <?= e($cityName) ?>'de yüz yüze seans
                </li>
                <li>
                    <i class="uil uil-video" aria-hidden="true"></i>
                    Online seans — Türkiye geneli
                </li>
                <li>
                    <i class="uil uil-check-circle" aria-hidden="true"></i>
                    Ön görüşme ücretsizdir
                </li>
                <!-- <li>
                    <i class="uil uil-shield-check" aria-hidden="true"></i>
                    8+ yıl klinik deneyim
                </li> -->
            </ul>

            <div class="sehir-hero__cta" style="margin-top:var(--space-6);">
                <a href="<?= ROOT_URL ?>randevu" class="btn btn--lg">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevu Al
                </a>
                <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode($cityName . '\'den randevu almak istiyorum.') ?>"
                   class="btn btn--lg btn--whatsapp"
                   target="_blank" rel="noopener noreferrer">
                    <i class="uil uil-whatsapp" aria-hidden="true"></i>
                    WhatsApp
                </a>
            </div>
        </div>

        <div class="sehir-hero__image">
            <img src="<?= e(publicAvatarUrl()) ?>"
                 alt="<?= e($cityName) ?> Klinik Psikolog Doğukan Kopuk"
                 width="380" height="460"
                 loading="eager">
        </div>
    </div>
</section>

<!-- Hizmetler -->
<section class="sehir-hizmetler">
    <div class="container">
        <div class="section__header text-center" style="margin-bottom:var(--space-7);">
            <h2><?= e($cityName) ?>'de Sunduğum Terapi Hizmetleri</h2>
            <p class="text-muted">
                Her danışanın ihtiyacı farklıdır. Bilimsel yöntemlerle size özel plan oluştururuz.
            </p>
        </div>
        <div class="home-hizmetler__grid">
            <?php if ($services && mysqli_num_rows($services) > 0):
                while ($s = mysqli_fetch_assoc($services)): ?>
            <article class="hizmet-karti">
                <div class="hizmet-karti__ikon" aria-hidden="true">
                    <i class="<?= e($s['icon_class'] ?? 'uil uil-heart') ?>"></i>
                </div>
                <h3><?= e($s['title']) ?></h3>
                <p><?= e($s['summary'] ?? '') ?></p>
                <a href="<?= ROOT_URL ?>hizmetler/<?= urlencode($s['slug']) ?>"
                   class="hizmet-karti__link">
                    Detaylı İncele
                    <i class="uil uil-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<!-- Neden Ben? -->
<section class="sehir-neden">
    <div class="container sehir-neden__container">
        <div class="sehir-neden__image">
            <img src="<?= e(publicAvatarUrl()) ?>"
                 alt="Klinik Psikolog Doğukan Kopuk"
                 width="380" height="420"
                 loading="lazy">
        </div>
        <div class="sehir-neden__text">
            <span class="category__button">Uzman Psikolog</span>
            <h2>Neden <?= e($cityName) ?>'de Doğukan Kopuk ile Çalışmalısınız?</h2>
            <ul class="sehir-neden__list">
                <?php
                $whyItems = [
                    ['icon' => 'uil-graduation-cap', 'title' => 'Akademik Altyapı',    'desc' => 'Klinik Psikoloji yüksek lisansı ve Bilişsel Davranışçı Terapi sertifikası.'],
                    // ['icon' => 'uil-chart-line',     'title' => '8+ Yıl Deneyim',      'desc' => '500\'den fazla danışanla çalışma deneyimi, çeşitli klinik ortamlarda hizmet.'],
                    ['icon' => 'uil-book-open',      'title' => 'Kanıta Dayalı',        'desc' => 'Bilimsel araştırmalarla desteklenmiş, etkinliği kanıtlanmış terapi yöntemleri.'],
                    ['icon' => 'uil-lock',           'title' => 'Tam Gizlilik',         'desc' => 'Tüm seans bilgileri etik kurallar çerçevesinde kesinlikle gizli tutulur.'],
                ];
                foreach ($whyItems as $wi): ?>
                <li>
                    <i class="uil <?= e($wi['icon']) ?>" aria-hidden="true"></i>
                    <div>
                        <strong><?= e($wi['title']) ?></strong>
                        <p><?= e($wi['desc']) ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="<?= ROOT_URL ?>hakkimda" class="btn btn--outline">
                Hakkımda Daha Fazla
            </a>
        </div>
    </div>
</section>

<?php if (!empty($d['content'])): ?>
<!-- Şehir Özel İçerik (admin panelinden düzenlenebilir) -->
<section style="padding: var(--space-9) 0;">
    <div class="container" style="max-width:800px;">
        <div class="singlepost__body">
            <?= $d['content'] /* Admin'den HTML veya metin girilir, e() kullanılmaz */ ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog Önizleme -->
<?php if ($recentPosts && mysqli_num_rows($recentPosts) > 0): ?>
<section class="blog-preview" style="background:var(--color-bg-2);border-top:1px solid var(--color-border);">
    <div class="container">
        <div class="section__header section__header--flex">
            <h2>Psikoloji Üzerine Yazılar</h2>
            <a href="<?= ROOT_URL ?>blog" class="btn btn--outline btn--sm">Tüm Yazılar</a>
        </div>
        <div class="posts__container">
            <?php while ($p = mysqli_fetch_assoc($recentPosts)): ?>
            <article class="post">
                <div class="post__thumbnail">
                    <a href="<?= ROOT_URL ?>blog/<?= urlencode($p['slug']) ?>"
                       tabindex="-1" aria-hidden="true">
                        <img src="<?= getImageUrl($p['thumbnail'] ?? '') ?>"
                             alt="<?= e($p['title']) ?>"
                             width="400" height="225" loading="lazy">
                    </a>
                </div>
                <div class="post__info">
                    <h3 class="post__title">
                        <a href="<?= ROOT_URL ?>blog/<?= urlencode($p['slug']) ?>">
                            <?= e($p['title']) ?>
                        </a>
                    </h3>
                    <div class="post__meta">
                        <span>
                            <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                            <?= turkceTarih($p['published_at'], 'd F Y') ?>
                        </span>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="home-cta">
    <div class="container home-cta__container">
        <h2><?= e($cityName) ?>'de Psikolojik Destek Almaya Hazır Mısınız?</h2>
        <p class="text-muted">
            İlk görüşme ücretsizdir. Yüz yüze veya online seans seçeneğiyle
            size en uygun şekilde destek sunuyorum.
        </p>
        <div class="cta-group cta-group--center">
            <a href="<?= ROOT_URL ?>randevu" class="btn">
                <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                Randevu Al
            </a>
            <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode($cityName . '\'den randevu almak istiyorum.') ?>"
               class="btn btn--outline"
               target="_blank" rel="noopener noreferrer">
                <i class="uil uil-whatsapp" aria-hidden="true"></i>
                WhatsApp
            </a>
        </div>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
