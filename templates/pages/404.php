<?php
/**
 * templates/pages/404.php
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'Sayfa Bulunamadı | Psikolog Doğukan Kopuk';
$seo_description = 'Aradığınız sayfa bulunamadı. Ana sayfaya veya hizmetler sayfasına gidin.';
$seo_noindex     = true;

require_once BASE_PATH . '/templates/partials/header.php';
?>

<section style="margin-top:70px;padding:var(--space-10) 0;text-align:center;min-height:60vh;display:flex;align-items:center;">
    <div class="container">
        <p style="font-size:6rem;font-weight:800;color:var(--color-primary);line-height:1;margin-bottom:var(--space-4);">
            404
        </p>
        <h1 style="margin-bottom:var(--space-4);">Sayfa Bulunamadı</h1>
        <p style="color:var(--color-text-muted);max-width:480px;margin:0 auto var(--space-7);">
            Aradığınız sayfa taşınmış, silinmiş ya da hiç var olmamış olabilir.
            Aşağıdaki bağlantılardan devam edebilirsiniz.
        </p>
        <div style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap;">
            <a href="<?= ROOT_URL ?>" class="btn">
                <i class="uil uil-home" aria-hidden="true"></i>
                Ana Sayfa
            </a>
            <a href="<?= ROOT_URL ?>hizmetler" class="btn btn--outline">
                Hizmetler
            </a>
            <a href="<?= ROOT_URL ?>randevu" class="btn btn--outline">
                Randevu Al
            </a>
        </div>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
