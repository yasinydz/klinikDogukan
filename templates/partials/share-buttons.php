<?php
/**
 * templates/partials/share-buttons.php
 *
 * Blog paylaşım butonları — başlık altında ve içerik sonunda kullanılır.
 * Gerekli değişkenler:
 *   $shareTitle   string — Yazı başlığı (encode edilmiş)
 *   $shareUrl     string — Canonical URL
 *   $shareDesc    string — Kısa açıklama
 *   $shareVariant string — 'top' | 'bottom' (opsiyonel, CSS için)
 */

$_shareTitle = urlencode($shareTitle ?? '');
$_shareUrl   = urlencode($shareUrl   ?? '');
$_shareDesc  = urlencode(mb_substr($shareDesc ?? '', 0, 100, 'UTF-8'));
$_variant    = $shareVariant ?? 'bottom';

$_waText  = urlencode(($shareTitle ?? '') . ' — ' . ($shareUrl ?? ''));
$_tgText  = urlencode(($shareTitle ?? '') . "\n" . ($shareUrl ?? ''));
?>
<div class="share-bar share-bar--<?= $_variant ?>" role="region" aria-label="Yazıyı paylaş">
    <span class="share-bar__label">
        <i class="uil uil-share-alt" aria-hidden="true"></i>
        Paylaş
    </span>

    <div class="share-bar__buttons">

        <!-- WhatsApp -->
        <a href="https://wa.me/?text=<?= $_waText ?>"
           class="share-btn share-btn--whatsapp"
           target="_blank" rel="noopener noreferrer"
           aria-label="WhatsApp ile paylaş"
           title="WhatsApp">
            <i class="uil uil-whatsapp" aria-hidden="true"></i>
            <span class="share-btn__label">WhatsApp</span>
        </a>

        <!-- Twitter / X -->
        <a href="https://twitter.com/intent/tweet?text=<?= $_shareTitle ?>&url=<?= $_shareUrl ?>"
           class="share-btn share-btn--twitter"
           target="_blank" rel="noopener noreferrer"
           aria-label="Twitter / X ile paylaş"
           title="Twitter / X">
            <svg class="share-btn__icon-svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" aria-hidden="true">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.852L1.254 2.25H8.08l4.259 5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            <span class="share-btn__label">X</span>
        </a>

        <!-- LinkedIn -->
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $_shareUrl ?>"
           class="share-btn share-btn--linkedin"
           target="_blank" rel="noopener noreferrer"
           aria-label="LinkedIn ile paylaş"
           title="LinkedIn">
            <i class="uil uil-linkedin" aria-hidden="true"></i>
            <span class="share-btn__label">LinkedIn</span>
        </a>

        <!-- Telegram -->
        <a href="https://t.me/share/url?url=<?= $_shareUrl ?>&text=<?= $_tgText ?>"
           class="share-btn share-btn--telegram"
           target="_blank" rel="noopener noreferrer"
           aria-label="Telegram ile paylaş"
           title="Telegram">
            <i class="uil uil-telegram-alt" aria-hidden="true"></i>
            <span class="share-btn__label">Telegram</span>
        </a>

        <!-- Copy Link -->
        <button class="share-btn share-btn--copy"
                data-url="<?= htmlspecialchars($shareUrl ?? '', ENT_QUOTES) ?>"
                onclick="shareCopyLink(this)"
                aria-label="Bağlantıyı kopyala"
                title="Bağlantıyı kopyala"
                type="button">
            <i class="uil uil-copy" aria-hidden="true"></i>
            <span class="share-btn__label">Kopyala</span>
        </button>

    </div>
</div>
