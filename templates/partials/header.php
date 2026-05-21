<?php
/**
 * templates/partials/header.php
 *
 * Her public sayfada include edilir.
 * Sayfada tanımlanması beklenen değişkenler:
 *   $seo_title       string  — Sayfa başlığı
 *   $seo_description string  — Meta description
 *   $seo_canonical   string  — Canonical URL
 *   $seo_noindex     bool    — noindex ise true
 *   $seo_og_image    string  — OG görseli (opsiyonel)
 *   $seo_og_type     string  — OG tipi (varsayılan: website)
 *   $seo_breadcrumbs array   — Breadcrumb dizisi
 *   $seo_schemas     array   — Ek schema dizileri
 */

ob_start();

// ── Settings Helper (DB'den okunur, constant'a fallback) ──────
require_once (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/app/helpers/settings.php';

// ── SEO Defaults ──────────────────────────────────────────────
$defaultImage    = SITE_URL . '/images/og-default.png'; // 1200x630 OG default
$defaultDesc     = 'Klinik Psikolog Doğukan Kopuk — Anksiyete, depresyon, travma ve ilişki sorunları için bilişsel davranışçı terapi. İzmit · Kocaeli · Online.';

// Title oluşturma — brand duplication'ı önle
if (!isset($seo_title) || $seo_title === '') {
    // Sayfa title tanımlanmamış → default
    $pageTitle = SITE_NAME . ' | Klinik Psikolog · İzmit · Kocaeli';
} elseif (
    str_contains($seo_title, SITE_NAME)
    || str_contains($seo_title, PSYCHOLOGIST_NAME)
    || str_contains($seo_title, 'Doğukan Kopuk')
) {
    // seo_title zaten brand/isim içeriyor → tekrar ekleme
    $pageTitle = e($seo_title);
} else {
    // Normal sayfa → "Sayfa Başlığı — Brand"
    $pageTitle = e($seo_title) . ' — ' . SITE_NAME;
}

$pageDesc = e($seo_description ?? $defaultDesc);

$pageCanonical = isset($seo_canonical) && $seo_canonical !== ''
    ? $seo_canonical
    : SITE_URL . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

// OG fallback zinciri: seo_og_image → default 1200x630
$pageOgImage = (isset($seo_og_image) && $seo_og_image !== '')
    ? $seo_og_image
    : $defaultImage;
$pageOgType  = $seo_og_type  ?? 'website';
// OG görsel boyutu — og-default.png gerçekten 1200x630
$pageOgIsDefault = ($pageOgImage === $defaultImage);

// Otomatik noindex kontrolü
$requestPath   = $_SERVER['REQUEST_URI'] ?? '';
$autoNoindex   = str_contains($requestPath, '/admin');
$pageNoindex   = !empty($seo_noindex) || $autoNoindex;

// Admin oturum kontrolü (nav için)
$isAdminLoggedIn   = isAdminLoggedIn();
$adminUsername     = $_SESSION['admin_username'] ?? null;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDesc ?>">
    <link rel="canonical" href="<?= e($pageCanonical) ?>">

    <?php if ($pageNoindex): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type"        content="<?= e($pageOgType) ?>">
    <meta property="og:title"       content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDesc ?>">
    <meta property="og:url"         content="<?= e($pageCanonical) ?>">
    <meta property="og:image"        content="<?= e($pageOgImage) ?>">
    <meta property="og:image:type"    content="<?php
        if (str_contains($pageOgImage, '.webp')) { echo 'image/webp'; }
        elseif (str_contains($pageOgImage, '.png')) { echo 'image/png'; }
        else { echo 'image/jpeg'; }
    ?>">
    <?php if ($pageOgIsDefault): ?>
    <meta property="og:image:width"   content="1200">
    <meta property="og:image:height"  content="630">
    <?php endif; ?>
    <meta property="og:site_name"   content="<?= e(SITE_NAME) ?>">
    <meta property="og:locale"      content="tr_TR">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDesc ?>">
    <meta name="twitter:image"       content="<?= e($pageOgImage) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= ROOT_URL ?>icons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="<?= ROOT_URL ?>icons/favicon.svg">
    <link rel="shortcut icon" href="<?= ROOT_URL ?>icons/favicon.ico">

    <!-- PWA — Android / Chrome -->
    <meta name="theme-color" content="#000000">

    <!-- PWA — iOS / Safari -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Psk DK">
    <?php if (file_exists(PUBLIC_PATH . '/icons/apple-touch-icon.png')): ?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= ROOT_URL ?>icons/apple-touch-icon.png">
    <?php endif; ?>
    <?php if (file_exists(PUBLIC_PATH . '/icons/site.webmanifest')): ?>
    <link rel="manifest" href="<?= ROOT_URL ?>icons/site.webmanifest">
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap"
          rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet"
          href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/themes.css">
    <?php
    // Production: tek dosya (33 @import → 0 HTTP round-trip)
    // Development: @import zinciri (hot-reload dostu)
    $cssFile = (!APP_DEBUG && file_exists(BASE_PATH . '/public/css/style.min.css'))
        ? 'css/style.min.css'
        : 'css/style.css';
    ?>
    <link rel="stylesheet" href="<?= ROOT_URL . $cssFile ?>">

    <!-- Google Analytics -->
    <?php if (GA4_ID !== ''): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(GA4_ID) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= e(GA4_ID) ?>');
        <?php if (AW_ID !== ''): ?>
        gtag('config', '<?= e(AW_ID) ?>');
        <?php endif; ?>
    </script>
    <?php endif; ?>

    <!-- Schema: WebSite (her sayfada) -->
    <?php renderSchema(schemaWebSite()); ?>

    <!-- Schema: Breadcrumb -->
    <?php if (!empty($seo_breadcrumbs)): ?>
    <?php renderSchema(schemaBreadcrumb($seo_breadcrumbs)); ?>
    <?php endif; ?>

    <!-- Schema: Ek (sayfa özel) -->
    <?php if (!empty($seo_schemas) && is_array($seo_schemas)): ?>
    <?php foreach ($seo_schemas as $schema): ?>
    <?php renderSchema($schema); ?>
    <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

<!-- Tema: Sayfa yüklenmeden önce localStorage'dan uygula -->
<script>
(function () {
    try {
        var t = localStorage.getItem('theme');
        if (!t) {
            t = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        }
        if (t === 'light') document.body.classList.add('light-mode');
    } catch (e) {}
})();
</script>

<!-- Navbar -->
<?php require __DIR__ . '/nav.php'; ?>

<!-- Cookie Banner -->
<?php require __DIR__ . '/cookie-banner.php'; ?>

<main id="main-content">
