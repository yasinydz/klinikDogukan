<?php
/**
 * templates/pages/blog.php
 * URL: /blog
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/paginator.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$perPage     = 6;
$currentPage = max(1, (int) ($_GET['sayfa'] ?? 1));

$totalResult = mysqli_query($connection, "SELECT COUNT(*) AS c FROM posts WHERE is_published = 1");
$totalPosts  = (int) mysqli_fetch_assoc($totalResult)['c'];
$paginator   = new Paginator($totalPosts, $perPage, $currentPage);

$seo_title       = $currentPage > 1
    ? 'Blog — Sayfa ' . $currentPage . ' | Psikolog Doğukan Kopuk'
    : 'Blog | Psikolog Doğukan Kopuk — Psikoloji Yazıları';
$seo_description = 'Psikolog Doğukan Kopuk\'un blog yazıları — Anksiyete, depresyon, ilişkiler ve kişisel gelişim üzerine uzman içerikler.';
$seo_canonical   = SITE_URL . '/blog' . ($currentPage > 1 ? '?sayfa=' . $currentPage : '');
$seo_noindex     = $currentPage > 1;

$seo_breadcrumbs = [
    ['name' => 'Anasayfa', 'url' => SITE_URL],
    ['name' => 'Blog',     'url' => ''],
];

// Blog listesi schema yok — breadcrumb zaten header.php'den render ediliyor

require_once BASE_PATH . '/templates/partials/header.php';

$posts = mysqli_query($connection, "
    SELECT p.title, p.slug, p.body, p.thumbnail, p.views, p.published_at,
           c.title AS category_title, c.slug AS category_slug,
           u.first_name, u.last_name, u.avatar
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    LEFT JOIN admins u ON p.author_id = u.id
    WHERE p.is_published = 1
    ORDER BY p.published_at DESC
    LIMIT {$perPage} OFFSET {$paginator->offset}
");

$categories = mysqli_query($connection,
    "SELECT title, slug FROM post_categories ORDER BY title ASC"
);

?>

<!-- Arama -->
<section class="search__bar">
    <form class="container search__bar-container"
          action="<?= ROOT_URL ?>arama"
          method="GET"
          role="search">
        <div>
            <i class="uil uil-search" aria-hidden="true"></i>
            <input type="search"
                   name="search"
                   placeholder="Yazılarda ara..."
                   aria-label="Blog yazılarında ara">
            <button type="submit" name="submit" class="btn">Ara</button>
        </div>
    </form>
</section>

<!-- Yazı Listesi -->
<section class="posts" aria-label="Blog yazıları">
    <div class="container posts__container">
        <?php if ($posts && mysqli_num_rows($posts) > 0):
            while ($post = mysqli_fetch_assoc($posts)): ?>
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
                <?php if (!empty($post['category_slug'])): ?>
                <a href="<?= ROOT_URL ?>kategori/<?= urlencode($post['category_slug']) ?>"
                   class="category__button">
                    <?= e($post['category_title'] ?? 'Genel') ?>
                </a>
                <?php endif; ?>

                <h3 class="post__title">
                    <a href="<?= ROOT_URL ?>blog/<?= urlencode($post['slug']) ?>">
                        <?= e($post['title']) ?>
                    </a>
                </h3>

                <p class="post__body">
                    <?= e(mb_substr(strip_tags($post['body']), 0, 120, 'UTF-8')) ?>...
                </p>

                <div class="post__meta">
                    <span>
                        <i class="uil uil-clock" aria-hidden="true"></i>
                        <?= readingTime($post['body']) ?>
                    </span>
                    <span>
                        <i class="uil uil-eye" aria-hidden="true"></i>
                        <?= number_format((int) $post['views']) ?>
                    </span>
                </div>

                <div class="post__author">
                    <div class="post__author-avatar">
                        <img src="<?= ROOT_URL ?>images/<?= e($post['avatar'] ?? 'default.png') ?>"
                             alt="" width="36" height="36" loading="lazy">
                    </div>
                    <div>
                        <h5><?= e(trim($post['first_name'] . ' ' . $post['last_name'])) ?></h5>
                        <small><?= turkceTarih($post['published_at'], 'd F Y') ?></small>
                    </div>
                </div>
            </div>
        </article>
        <?php endwhile;
        else: ?>
        <div class="alert__message error lg">
            <p>Henüz yazı bulunmuyor.</p>
        </div>
        <?php endif; ?>
    </div>

    <?= $paginator->render(SITE_URL . '/blog') ?>
</section>

<!-- Kategoriler -->
<section class="category__buttons" aria-label="Blog kategorileri">
    <div class="container category__buttons-container">
        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
        <a href="<?= ROOT_URL ?>kategori/<?= urlencode($cat['slug']) ?>"
           class="category__button">
            <?= e($cat['title']) ?>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
