<?php
/**
 * templates/pages/search.php
 * URL: /arama?search=...
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$query = sanitizeText(
    filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '',
    200
);

if ($query === '' || !isset($_GET['submit'])) {
    header('Location: ' . ROOT_URL . 'blog');
    exit;
}

$seo_title   = '"' . $query . '" için Arama Sonuçları | Psikolog Doğukan Kopuk';
$seo_noindex = true;

require_once BASE_PATH . '/templates/partials/header.php';

$like  = '%' . $query . '%';
$stmt  = $connection->prepare("
    SELECT p.title, p.slug, p.body, p.thumbnail, p.views, p.published_at,
           c.title AS category_title, c.slug AS category_slug,
           u.first_name, u.last_name
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    LEFT JOIN admins u ON p.author_id = u.id
    WHERE p.is_published = 1 AND (p.title LIKE ? OR p.body LIKE ?)
    ORDER BY p.published_at DESC
    LIMIT 30
");
$stmt->bind_param('ss', $like, $like);
$stmt->execute();
$posts = $stmt->get_result();

$cats = mysqli_query($connection,
    "SELECT title, slug FROM post_categories ORDER BY title ASC"
);
?>

<!-- Arama Çubuğu -->
<section class="search__bar">
    <form class="container search__bar-container"
          action="<?= ROOT_URL ?>arama"
          method="GET"
          role="search">
        <div>
            <i class="uil uil-search" aria-hidden="true"></i>
            <input type="search"
                   name="search"
                   value="<?= e($query) ?>"
                   placeholder="Yazılarda ara..."
                   aria-label="Arama">
            <button type="submit" name="submit" class="btn">Ara</button>
        </div>
    </form>
</section>

<?php if ($posts->num_rows > 0): ?>
<section class="posts" aria-label="Arama sonuçları">
    <div class="container">
        <p class="search__result-info">
            "<strong><?= e($query) ?></strong>" için
            <strong><?= $posts->num_rows ?></strong> sonuç bulundu.
        </p>
    </div>
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
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
</section>
<?php else: ?>
<div class="alert__message error lg section__extra-margin container">
    <p>
        "<strong><?= e($query) ?></strong>" için herhangi bir yazı bulunamadı.
    </p>
</div>
<?php endif; ?>

<!-- Kategoriler -->
<section class="category__buttons" aria-label="Kategoriler">
    <div class="container category__buttons-container">
        <?php while ($cat = mysqli_fetch_assoc($cats)): ?>
        <a href="<?= ROOT_URL ?>kategori/<?= urlencode($cat['slug']) ?>"
           class="category__button">
            <?= e($cat['title']) ?>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
