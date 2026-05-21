<?php
/**
 * templates/partials/cta-block.php
 *
 * Yeniden kullanılabilir CTA bloğu.
 * Blog yazısı, hizmet detay, şehir sayfaları vb. sonunda kullanılır.
 *
 * Değişkenler (include eden sayfadan):
 *   $cta_title        string  — Başlık (opsiyonel)
 *   $cta_description  string  — Açıklama (opsiyonel)
 *   $cta_service      array   — ['title' => '', 'slug' => '', 'summary' => ''] (opsiyonel)
 *   $cta_variant      string  — 'default' | 'service' | 'minimal' (varsayılan: default)
 *
 * Değişken tanımlı değilse varsayılan içerik kullanılır.
 */

$ctaVariant     = $cta_variant     ?? 'default';
$ctaTitle       = $cta_title       ?? 'Bu Konuda Destek Almak İster Misiniz?';
$ctaDescription = $cta_description ?? 'Profesyonel psikolojik destek için benimle iletişime geçebilirsiniz. Ön görüşme ücretsizdir.';
$ctaService     = $cta_service     ?? null;

// Hizmet verisi varsa başlık ve açıklamayı override et
if ($ctaService && !empty($ctaService['title'])) {
    $ctaTitle       = 'Bu Konuda Profesyonel Destek Almak İster Misiniz?';
    $ctaDescription = $ctaService['summary'] ?? $ctaDescription;
}

$waText = $ctaService
    ? urlencode($ctaService['title'] . ' hakkında bilgi almak istiyorum.')
    : urlencode('Randevu almak istiyorum.');
?>

<div class="post-cta">
    <div class="post-cta__icon" aria-hidden="true">
        <?php if ($ctaVariant === 'service' && $ctaService): ?>
        <i class="uil uil-heart-rate"></i>
        <?php else: ?>
        <i class="uil uil-comment-dots"></i>
        <?php endif; ?>
    </div>

    <div class="post-cta__text">
        <h3><?= e($ctaTitle) ?></h3>
        <p><?= e($ctaDescription) ?></p>
    </div>

    <div class="post-cta__actions">
        <?php if ($ctaService && !empty($ctaService['slug'])): ?>
        <a href="<?= ROOT_URL ?>hizmetler/<?= e($ctaService['slug']) ?>"
           class="btn btn--outline">
            <?= e($ctaService['title']) ?> Hakkında
        </a>
        <?php endif; ?>

        <a href="<?= ROOT_URL ?>randevu" class="btn">
            <i class="uil uil-calendar-alt" aria-hidden="true"></i>
            Randevu Al
        </a>

        <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= $waText ?>"
           class="btn btn--outline"
           target="_blank"
           rel="noopener noreferrer">
            <i class="uil uil-whatsapp" aria-hidden="true"></i>
            WhatsApp
        </a>
    </div>
</div>
