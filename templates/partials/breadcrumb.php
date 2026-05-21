<?php
/**
 * templates/partials/breadcrumb.php
 *
 * Breadcrumb navigasyon bileşeni.
 *
 * Kullanım:
 *   $breadcrumbs = [
 *     ['name' => 'Anasayfa',  'url' => SITE_URL],
 *     ['name' => 'Blog',      'url' => SITE_URL . '/blog'],
 *     ['name' => 'Yazı Adı',  'url' => ''],  // Son öğe — URL boş
 *   ];
 *   require 'breadcrumb.php';
 *
 * $breadcrumbs değişkeni include eden sayfadan gelir.
 */

if (empty($breadcrumbs) || !is_array($breadcrumbs)) {
    return;
}

$lastIndex = count($breadcrumbs) - 1;
?>
<nav class="breadcrumb" aria-label="Sayfa konumu">
    <ol itemscope itemtype="https://schema.org/BreadcrumbList">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
        <?php
            $isLast  = ($index === $lastIndex);
            $name    = e($crumb['name'] ?? '');
            $url     = $crumb['url']  ?? '';
            $pos     = $index + 1;
        ?>
        <li itemprop="itemListElement"
            itemscope
            itemtype="https://schema.org/ListItem">

            <?php if (!$isLast && $url !== ''): ?>
                <a href="<?= e($url) ?>"
                   itemprop="item">
                    <span itemprop="name"><?= $name ?></span>
                </a>
            <?php else: ?>
                <span itemprop="name"
                      aria-current="page"><?= $name ?></span>
            <?php endif; ?>

            <meta itemprop="position" content="<?= $pos ?>">
        </li>
        <?php endforeach; ?>
    </ol>
</nav>
