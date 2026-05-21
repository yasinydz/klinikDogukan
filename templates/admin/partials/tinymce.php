<?php
/**
 * templates/admin/partials/tinymce.php
 *
 * Tüm admin editör sayfalarında include edilir.
 * .env'den TINYMCE_MODE ve TINYMCE_API_KEY okunur.
 *
 * Kullanım:
 *   <?php require BASE_PATH . '/templates/admin/partials/tinymce.php'; ?>
 *   <script>document.addEventListener('DOMContentLoaded', function(){ initTinyMCE('#tinymce-editor'); });</script>
 */

$_tinymceMode = env('TINYMCE_MODE', 'cloud');
$_tinymceKey  = env('TINYMCE_API_KEY', '');

if ($_tinymceMode === 'cloud' && $_tinymceKey !== '') {
    $_tinymceSrc = 'https://cdn.tiny.cloud/1/' . urlencode($_tinymceKey) . '/tinymce/7/tinymce.min.js';
} elseif ($_tinymceMode === 'selfhost' || $_tinymceKey === '') {
    // Self-host modu VEYA key boşsa: local dosyadan yükle
    $_tinymceSelfPath = PUBLIC_PATH . '/vendor/tinymce/tinymce.min.js';
    if (file_exists($_tinymceSelfPath)) {
        $_tinymceSrc = ROOT_URL . 'vendor/tinymce/tinymce.min.js';
    } else {
        // Hiçbir kaynak yoksa script hiç yüklenmeyecek
        $_tinymceSrc = '';
        if (APP_DEBUG) {
            echo '<!-- ⚠️ UYARI: TINYMCE_API_KEY boş ve self-host dosyası bulunamadı. Editör düz textarea olarak çalışacak. -->' . PHP_EOL;
        }
    }
} else {
    $_tinymceSrc = ROOT_URL . 'vendor/tinymce/tinymce.min.js';
}
?>
<?php if ($_tinymceSrc !== ''): ?>
<script src="<?= e($_tinymceSrc) ?>" referrerpolicy="origin" crossorigin="anonymous"></script>
<?php endif; ?>
<script>
/**
 * TinyMCE merkezi init fonksiyonu.
 * @param {string} selector  CSS selector (default: '#tinymce-editor')
 * @param {object} overrides Ek veya override config
 */
function initTinyMCE(selector, overrides) {
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE yüklenemedi. API key veya CDN bağlantısını kontrol edin.');
        var el = document.querySelector(selector || '#tinymce-editor');
        if (el) {
            el.style.display = 'block';
            el.style.minHeight = '300px';
            el.placeholder = 'TinyMCE yüklenemedi — düz metin olarak yazabilirsiniz.';
        }
        return;
    }

    var isLight = document.body.classList.contains('light-mode');

    var defaults = {
        selector: selector || '#tinymce-editor',
        height: 500,
        menubar: true,
        language: 'tr',
        skin: isLight ? 'oxide' : 'oxide-dark',
        content_css: isLight ? 'default' : 'dark',
        plugins: 'anchor autolink charmap codesample emoticons link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        content_style: isLight
            ? 'body{font-family:Montserrat,sans-serif;font-size:16px;line-height:1.75;color:#1a2138;background:#fff;}'
            : 'body{font-family:Montserrat,sans-serif;font-size:16px;line-height:1.75;color:#e2e8f0;background:#111827;}',
        invalid_elements: 'script,iframe,object,embed',
        entity_encoding: 'raw',
        setup: function (editor) {
            editor.on('change', function () { tinymce.triggerSave(); });
        }
    };

    // Override merge
    if (overrides && typeof overrides === 'object') {
        for (var key in overrides) {
            if (overrides.hasOwnProperty(key)) {
                defaults[key] = overrides[key];
            }
        }
    }

    tinymce.init(defaults);
}
</script>
<?php unset($_tinymceMode, $_tinymceKey, $_tinymceSrc); ?>
