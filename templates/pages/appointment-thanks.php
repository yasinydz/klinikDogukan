<?php
/**
 * templates/pages/appointment-thanks.php
 * URL: /randevu/tesekkur
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

// Session flag olmadan direkt erişimi engelle
if (empty($_SESSION['appointment_completed'])) {
    header('Location: ' . ROOT_URL . 'randevu');
    exit;
}

unset($_SESSION['appointment_completed']);

$seo_title   = 'Randevu Talebiniz Alındı | Psikolog Doğukan Kopuk';
$seo_noindex = true;

require_once BASE_PATH . '/templates/partials/header.php';
?>

<!-- GA4 Conversion Event -->
<?php if (GA4_ID !== ''): ?>
<script>
    if (typeof gtag === 'function') {
        gtag('event', 'appointment_completed', {
            'event_category': 'lead',
            'event_label':    'appointment_form'
        });
        <?php if (AW_ID !== ''): ?>
        gtag('event', 'conversion', {
            'send_to': '<?= e(AW_ID) ?>/conversion_label',
            'value':   1.0,
            'currency':'TRY'
        });
        <?php endif; ?>
    }
</script>
<?php endif; ?>

<section class="tesekkur">
    <div class="container tesekkur__container">

        <div class="tesekkur__icon" aria-hidden="true">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none"
                 stroke="var(--color-success)" stroke-width="1.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>

        <h1>Randevu Talebiniz Alındı!</h1>

        <p class="text-muted">
            Teşekkürler. En kısa sürede — genellikle
            <strong>24 saat içinde</strong> — sizi arayarak
            uygun seans saatini belirleyeceğim.
        </p>

        <!-- Beklenti Adımları -->
        <div class="tesekkur__steps">
            <div class="tesekkur__step">
                <div class="tesekkur__step-num">1</div>
                <div class="tesekkur__step-text">
                    <strong>Sizi Arıyorum</strong>
                    <span>24 saat içinde kayıtlı telefonunuzdan dönüş yapacağım.</span>
                </div>
            </div>
            <div class="tesekkur__step">
                <div class="tesekkur__step-num">2</div>
                <div class="tesekkur__step-text">
                    <strong>Saati Belirliyoruz</strong>
                    <span>Size en uygun gün ve saati birlikte seçiyoruz.</span>
                </div>
            </div>
            <div class="tesekkur__step">
                <div class="tesekkur__step-num">3</div>
                <div class="tesekkur__step-text">
                    <strong>İlk Görüşme</strong>
                    <span>Ön değerlendirme ile başlıyoruz.</span>
                </div>
            </div>
        </div>

        <!-- Acil Durum -->
        <div class="tesekkur__trust-box">
            <p style="margin-bottom:var(--space-4);color:var(--color-text-muted);font-size:0.9rem;">
                Acil bir durumunuz varsa hemen ulaşabilirsiniz:
            </p>
            <div class="cta-group cta-group--center">
                <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode('Merhaba, randevu talebim var.') ?>"
                   class="btn btn--whatsapp"
                   target="_blank" rel="noopener noreferrer">
                    <i class="uil uil-whatsapp" aria-hidden="true"></i>
                    WhatsApp
                </a>
                <a href="tel:<?= e(CONTACT_PHONE_HREF) ?>" class="btn btn--outline">
                    <i class="uil uil-phone" aria-hidden="true"></i>
                    <?= e(CONTACT_PHONE) ?>
                </a>
            </div>
        </div>

        <!-- Son Yazılar -->
        <div style="margin-top:var(--space-8);">
            <p style="color:var(--color-text-muted);margin-bottom:var(--space-4);">
                Beklerken okumak isteyebilirsiniz:
            </p>
            <?php
            $recentPosts = mysqli_query($connection, "
                SELECT title, slug FROM posts
                WHERE is_published = 1
                ORDER BY published_at DESC LIMIT 3
            ");
            if ($recentPosts && mysqli_num_rows($recentPosts) > 0):
                while ($p = mysqli_fetch_assoc($recentPosts)):
            ?>
            <a href="<?= ROOT_URL ?>blog/<?= urlencode($p['slug']) ?>"
               style="display:flex;align-items:center;gap:var(--space-3);padding:var(--space-3) var(--space-4);background:var(--color-bg-2);border:1.5px solid var(--color-border);border-radius:var(--radius-md);margin-bottom:var(--space-2);text-decoration:none;color:var(--color-text);transition:border-color var(--transition-fast);"
               onmouseover="this.style.borderColor='var(--color-primary)'"
               onmouseout="this.style.borderColor='var(--color-border)'">
                <i class="uil uil-file-alt" style="color:var(--color-accent);" aria-hidden="true"></i>
                <span style="font-size:0.9rem;"><?= e($p['title']) ?></span>
            </a>
            <?php endwhile; endif; ?>
        </div>

        <a href="<?= ROOT_URL ?>"
           class="btn btn--outline"
           style="margin-top:var(--space-6);">
            <i class="uil uil-home" aria-hidden="true"></i>
            Ana Sayfaya Dön
        </a>

    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
