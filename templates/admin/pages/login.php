<?php
/**
 * templates/admin/pages/login.php
 * URL: /admin/giris
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/middleware/auth.php';

requireAdminGuest();

$savedUsername = $_SESSION['old_input']['username'] ?? '';
unset($_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Girişi — <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/themes.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
</head>
<body>
<script>
(function () {
    try {
        var t = localStorage.getItem('theme');
        if (!t) t = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        if (t === 'light') document.body.classList.add('light-mode');
    } catch (e) {}
})();
</script>

<section class="form__section">
    <div class="container form__section-container">

        <div class="text-center" style="margin-bottom:var(--space-6);">
            <a href="<?= ROOT_URL ?>"
               class="text-muted" style="font-size:0.85rem;text-decoration:none;">
                ← Siteye Dön
            </a>
        </div>

        <h2 class="text-center">Admin Girişi</h2>

        <?php flashRender(); ?>

        <form action="<?= ROOT_URL ?>admin/giris"
              method="POST"
              autocomplete="on">

            <?= csrfField() ?>

            <div class="form__control">
                <label for="username">Kullanıcı Adı</label>
                <input type="text"
                       id="username"
                       name="username"
                       value="<?= e($savedUsername) ?>"
                       placeholder="Kullanıcı adı"
                       required
                       autocomplete="username">
            </div>

            <div class="form__control">
                <label for="password">Şifre</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Şifre"
                       required
                       autocomplete="current-password">
            </div>

            <button type="submit" name="submit" class="btn btn--full">
                Giriş Yap
            </button>

        </form>

    </div>
</section>

<script src="<?= ROOT_URL ?>js/main.js"></script>
</body>
</html>
