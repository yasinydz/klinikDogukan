<?php
/**
 * templates/admin/partials/header.php
 *
 * Admin panel HTML head ve navbar.
 * Tüm admin sayfalarında include edilir.
 * requireAdminAuth() bu dosyadan önce çağrılmış olmalı.
 *
 * Değişkenler (include eden sayfadan):
 *   $activeMenu  string  — Aktif sidebar menü key'i
 *   $pageTitle   string  — Sayfa başlığı (opsiyonel)
 */

ob_start();

$adminPageTitle = isset($pageTitle)
    ? e($pageTitle) . ' — Admin Panel'
    : 'Admin Panel — ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">

    <title><?= $adminPageTitle ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&display=swap"
          rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet"
          href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/themes.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
</head>
<body>

<!-- Tema -->
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

<!-- Admin Navbar -->
<nav id="main-nav" role="navigation" aria-label="Admin navigasyon">
    <div class="container nav__container">

        <a href="<?= ROOT_URL ?>admin" class="nav__logo">
            Admin Panel
        </a>

        <div class="nav__actions">

            <button class="theme-toggle"
                    id="theme-toggle"
                    aria-label="Tema değiştir">
                <i class="uil uil-moon" id="theme-icon" aria-hidden="true"></i>
            </button>

            <a href="<?= ROOT_URL ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="btn sm outline">
                <i class="uil uil-external-link-alt" aria-hidden="true"></i>
                Siteyi Gör
            </a>

            <a href="<?= ROOT_URL ?>admin/cikis"
               class="btn sm danger">
                <i class="uil uil-signout" aria-hidden="true"></i>
                Çıkış
            </a>

        </div>

    </div>
</nav>
