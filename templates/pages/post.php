<?php
/**
 * templates/pages/post.php
 * URL: /blog/{slug}
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/public_profile.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: ' . ROOT_URL . 'blog');
    exit;
}

$stmt = $connection->prepare("
    SELECT p.*,
           c.title AS category_title, c.slug AS category_slug,
           u.first_name, u.last_name, u.avatar
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    LEFT JOIN admins u ON p.author_id = u.id
    WHERE p.slug = ? AND p.is_published = 1
    LIMIT 1
");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    http_response_code(404);
    require BASE_PATH . '/templates/pages/404.php';
    exit;
}

$post = $result->fetch_assoc();

// Görüntülenme sayacı
$vs = $connection->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
$vs->bind_param('i', $post['id']);
$vs->execute();

$readingTime = max(1, (int) ceil(str_word_count(strip_tags($post['body'])) / 200));

// İlgili hizmet
$relatedService = null;
if (!empty($post['related_service_slug'])) {
    $hs = $connection->prepare(
        "SELECT slug, title, summary FROM services WHERE slug = ? AND is_active = 1 LIMIT 1"
    );
    $hs->bind_param('s', $post['related_service_slug']);
    $hs->execute();
    $hr = $hs->get_result();
    if ($hr->num_rows === 1) {
        $relatedService = $hr->fetch_assoc();
    }
}

// ── SEO ───────────────────────────────────────────────────────
$seo_title = !empty($post['meta_title'])
    ? $post['meta_title']
    : $post['title'];

$seo_description = !empty($post['meta_desc'])
    ? $post['meta_desc']
    : mb_substr(trim(strip_tags($post['body'])), 0, 155, 'UTF-8') . '...';

$seo_canonical = SITE_URL . '/blog/' . $post['slug'];
// OG fallback: og_image → thumbnail → default 1200x630
$seo_og_image  = !empty($post['og_image'])
    ? $post['og_image']
    : (!empty($post['thumbnail'])
        ? SITE_URL . '/images/uploads/' . $post['thumbnail']
        : SITE_URL . '/images/og-default.png');
$seo_og_type   = 'article';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa', 'url' => SITE_URL],
    ['name' => 'Blog',     'url' => SITE_URL . '/blog'],
];
if (!empty($post['category_title'])) {
    $seo_breadcrumbs[] = [
        'name' => $post['category_title'],
        'url'  => SITE_URL . '/kategori/' . $post['category_slug'],
    ];
}
$seo_breadcrumbs[] = ['name' => $post['title'], 'url' => ''];

$seo_schemas = [
    schemaBlogPosting([
        'title'        => $post['title'],
        'meta_desc'    => $seo_description,
        'thumbnail'    => $post['thumbnail'],
        'published_at' => $post['published_at'] ?? null,
        'author_name'  => trim($post['first_name'] . ' ' . $post['last_name']),
        'slug'         => $post['slug'],
    ]),
];

// ── Share değişkenleri ────────────────────────────────────────
$shareTitle   = $post['title'];
$shareUrl     = SITE_URL . '/blog/' . $post['slug'];
$shareDesc    = $seo_description;

require_once BASE_PATH . '/templates/partials/header.php';
?>

<article class="singlepost" itemscope itemtype="https://schema.org/BlogPosting">
    <div class="container singlepost__container">

        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <!-- Kategori -->
        <div class="singlepost__category">
            <?php if (!empty($post['category_slug'])): ?>
            <a href="<?= ROOT_URL ?>kategori/<?= urlencode($post['category_slug']) ?>"
               class="category__button">
                <?= e($post['category_title']) ?>
            </a>
            <?php else: ?>
            <span class="category__button">Genel</span>
            <?php endif; ?>
        </div>

        <h1 itemprop="headline"><?= e($post['title']) ?></h1>

        <?php
        $shareVariant = 'top';
        require BASE_PATH . '/templates/partials/share-buttons.php';
        ?>

        <div class="post__meta">
            <span>
                <i class="uil uil-clock" aria-hidden="true"></i>
                <?= $readingTime ?> dk okuma
            </span>
            <span>
                <i class="uil uil-eye" aria-hidden="true"></i>
                <?= number_format((int) $post['views']) ?> görüntülenme
            </span>
            <span>
                <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                <time itemprop="datePublished"
                      datetime="<?= date('Y-m-d', strtotime($post['published_at'] ?? '')) ?>">
                    <?= turkceTarih($post['published_at'] ?? '', 'd F Y') ?>
                </time>
            </span>
        </div>

        <!-- Kapak Görseli -->
        <div class="singlepost__thumbnail">
            <img src="<?= getImageUrl($post['thumbnail'] ?? '') ?>"
                 alt="<?= e($post['title']) ?>"
                 width="780" height="440"
                 loading="eager"
                 itemprop="image">
        </div>

        <!-- İçerik -->
        <div class="singlepost__body" itemprop="articleBody">
            <?= $post['body'] /* TinyMCE WYSIWYG çıktısı — sadece admin girebilir */ ?>
        </div>

        <?php
        $shareVariant = 'bottom';
        require BASE_PATH . '/templates/partials/share-buttons.php';
        ?>

        <!-- CTA Bloğu -->
        <?php
        if ($relatedService) {
            $cta_variant     = 'service';
            $cta_service     = [
                'title'   => $relatedService['title'],
                'slug'    => $relatedService['slug'],
                'summary' => $relatedService['summary'],
            ];
        }
        require BASE_PATH . '/templates/partials/cta-block.php';
        ?>

        <!-- Yazar Bio — E-E-A-T -->
        <div class="post-author-bio" itemprop="author" itemscope itemtype="https://schema.org/Person">
            <div class="post-author-bio__avatar">
                <img src="<?= e(adminAvatarUrl($post['avatar'] ?? 'default.png')) ?>"
                     alt="<?= e(trim($post['first_name'] . ' ' . $post['last_name'])) ?>"
                     width="72" height="72" loading="lazy">
            </div>
            <div class="post-author-bio__info">
                <p class="post-author-bio__label">Yazar</p>
                <h4 itemprop="name">
                    <?= e(trim($post['first_name'] . ' ' . $post['last_name'])) ?>
                </h4>
                <p class="post-author-bio__title">
                    Klinik Psikolog
                </p>
                <p class="post-author-bio__desc">
                    Anksiyete, depresyon, travma ve ilişkisel sorunlar alanında
                    bireysel terapi sunmaktadır.
                </p>
                <a href="<?= ROOT_URL ?>hakkimda" class="post-author-bio__link">
                    Hakkımda Daha Fazla
                    <i class="uil uil-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

    </div>
</article>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
