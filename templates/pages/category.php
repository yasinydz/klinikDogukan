<?php
/**
 * templates/pages/category.php
 * URL: /kategori/{slug}
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/paginator.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$categorySlug = trim($_GET['slug'] ?? '');

if ($categorySlug === '') {
    header('Location: ' . ROOT_URL . 'blog');
    exit;
}

$catStmt = $connection->prepare(
    "SELECT id, title, slug, description, meta_desc, is_noindex
     FROM post_categories WHERE slug = ? LIMIT 1"
);
$catStmt->bind_param('s', $categorySlug);
$catStmt->execute();
$catResult = $catStmt->get_result();

if ($catResult->num_rows !== 1) {
    header('Location: ' . ROOT_URL . 'blog');
    exit;
}

$category   = $catResult->fetch_assoc();
$categoryId = (int) $category['id'];

$perPage     = 6;
$currentPage = max(1, (int) ($_GET['sayfa'] ?? 1));

$countStmt = $connection->prepare(
    "SELECT COUNT(*) AS c FROM posts WHERE category_id = ? AND is_published = 1"
);
$countStmt->bind_param('i', $categoryId);
$countStmt->execute();
$totalPosts = (int) $countStmt->get_result()->fetch_assoc()['c'];
$paginator  = new Paginator($totalPosts, $perPage, $currentPage);

$seo_title = $category['title'] . ' Yazıları | Psikolog Doğukan Kopuk';

$seo_description = !empty($category['meta_desc'])
    ? $category['meta_desc']
    : ($category['description']
        ? mb_substr($category['description'], 0, 155, 'UTF-8')
        : $category['title'] . ' kategorisindeki blog yazıları.');

$seo_canonical = SITE_URL . '/kategori/' . $category['slug']
    . ($currentPage > 1 ? '?sayfa=' . $currentPage : '');

$seo_noindex = (bool) $category['is_noindex'] || $currentPage > 1;

$seo_breadcrumbs = [
    ['name' => 'Anasayfa', 'url' => SITE_URL],
    ['name' => 'Blog',     'url' => SITE_URL . '/blog'],
    ['name' => $category['title'], 'url' => ''],
];

require_once BASE_PATH . '/templates/partials/header.php';

$posts = $connection->prepare("
    SELECT p.title, p.slug, p.body, p.thumbnail, p.views, p.published_at,
           c.title AS category_title, c.slug AS category_slug,
           u.first_name, u.last_name, u.avatar
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    LEFT JOIN admins u ON p.author_id = u.id
    WHERE p.category_id = ? AND p.is_published = 1
    ORDER BY p.published_at DESC
    LIMIT {$perPage} OFFSET {$paginator->offset}
");
$posts->bind_param('i', $categoryId);
$posts->execute();
$posts = $posts->get_result();

$allCats = mysqli_query($connection,
    "SELECT title, slug FROM post_categories ORDER BY title ASC"
);
?>

<!-- Kategori Başlığı -->
<header class="blog-arsiv-baslik">
    <div class="container" style="text-align:center;">
        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <h1><?= e($category['title']) ?></h1>
        <?php if (!empty($category['description'])): ?>
        <p style="color:var(--color-text-muted);max-width:560px;margin:var(--space-3) auto 0;line-height:1.7;">
            <?= e($category['description']) ?>
        </p>
        <?php endif; ?>
    </div>
</header>

<?php if ($totalPosts > 0): ?>
<section class="posts" aria-label="<?= e($category['title']) ?> yazıları">
    <div class="container posts__container">
        <?php while ($post = $posts->fetch_assoc()): ?>
        <article class="post">
            <div class="post__thumbnail">
                <a href="<?= ROOT_URL ?>blog/<?= urlencode($post['slug']) ?>"
                   tabindex="-1" aria-hidden="true">
                    <img src="<?= getImageUrl($post['thumbnail'] ?? '') ?>"
                         alt="<?= e($post['title']) ?>"
                         width="400" height="225" loading="lazy">
                </a>
            </div>
            <div class="post__info">
                <a href="<?= ROOT_URL ?>kategori/<?= urlencode($post['category_slug'] ?? '') ?>"
                   class="category__button">
                    <?= e($post['category_title'] ?? 'Genel') ?>
                </a>
                <h2 class="post__title">
                    <a href="<?= ROOT_URL ?>blog/<?= urlencode($post['slug']) ?>">
                        <?= e($post['title']) ?>
                    </a>
                </h2>
                <p class="post__body">
                    <?= e(mb_substr(strip_tags($post['body']), 0, 120, 'UTF-8')) ?>...
                </p>
                <div class="post__meta">
                    <span>
                        <i class="uil uil-clock" aria-hidden="true"></i>
                        <?= readingTime($post['body']) ?>
                    </span>
                    <span>
                        <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                        <?= turkceTarih($post['published_at'], 'd F Y') ?>
                    </span>
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>

    <?= $paginator->render(SITE_URL . '/kategori/' . $categorySlug) ?>
</section>

<?php else: ?>
<div class="alert__message error lg section__extra-margin container">
    <p>Bu kategoride henüz yazı bulunmuyor.</p>
</div>
<?php endif; ?>

<!-- Tüm Kategoriler -->
<section class="category__buttons" aria-label="Tüm kategoriler">
    <div class="container category__buttons-container">
        <?php while ($cat = mysqli_fetch_assoc($allCats)): ?>
        <a href="<?= ROOT_URL ?>kategori/<?= urlencode($cat['slug']) ?>"
           class="category__button <?= $cat['slug'] === $categorySlug ? 'active' : '' ?>">
            <?= e($cat['title']) ?>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
