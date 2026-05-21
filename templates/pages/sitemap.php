<?php
/**
 * templates/pages/sitemap.php
 * URL: /sitemap.xml
 */

header('Content-Type: application/xml; charset=utf-8');
// Sitemap endpoint'e noindex vermek gereksiz — kaldırıldı

$base = SITE_URL;

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <!-- Statik Sayfalar -->
    <url>
        <loc><?= $base ?>/</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= $base ?>/hakkimda</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= $base ?>/hizmetler</loc>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= $base ?>/blog</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= $base ?>/iletisim</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc><?= $base ?>/randevu</loc>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= $base ?>/sss</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <!-- Şehir Landing Pages -->
    <url>
        <loc><?= $base ?>/izmit-psikolog</loc>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= $base ?>/kocaeli-psikolog</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= $base ?>/gebze-psikolog</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <!-- Hukuki Sayfalar -->
    <url>
        <loc><?= $base ?>/kvkk-aydinlatma</loc>
        <changefreq>yearly</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc><?= $base ?>/gizlilik-politikasi</loc>
        <changefreq>yearly</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc><?= $base ?>/cerez-politikasi</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>

    <!-- Hizmet Sayfaları -->
    <?php
    $checkServices = mysqli_query($connection, "SHOW TABLES LIKE 'services'");
    if ($checkServices && mysqli_num_rows($checkServices) > 0):
        $services = mysqli_query($connection,
            "SELECT slug, updated_at FROM services WHERE is_active = 1 ORDER BY display_order ASC"
        );
        while ($s = mysqli_fetch_assoc($services)):
            $lastmod = date('Y-m-d', strtotime($s['updated_at']));
    ?>
    <url>
        <loc><?= $base ?>/hizmetler/<?= htmlspecialchars($s['slug']) ?></loc>
        <lastmod><?= $lastmod ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <?php endwhile; endif; ?>

    <!-- Blog Kategorileri -->
    <?php
    $cats = mysqli_query($connection,
        "SELECT slug FROM post_categories WHERE is_noindex = 0 ORDER BY title ASC"
    );
    while ($cat = mysqli_fetch_assoc($cats)):
    ?>
    <url>
        <loc><?= $base ?>/kategori/<?= htmlspecialchars($cat['slug']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endwhile; ?>

    <!-- Blog Yazıları -->
    <?php
    $posts = mysqli_query($connection,
        "SELECT slug, published_at FROM posts WHERE is_published = 1 ORDER BY published_at DESC"
    );
    while ($post = mysqli_fetch_assoc($posts)):
        $lastmod = date('Y-m-d', strtotime($post['published_at']));
    ?>
    <url>
        <loc><?= $base ?>/blog/<?= htmlspecialchars($post['slug']) ?></loc>
        <lastmod><?= $lastmod ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endwhile; ?>

</urlset>
