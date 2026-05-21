<?php
/**
 * templates/pages/contact.php
 * URL: /iletisim
 *
 * Unified layout: Hero → CTA → Form — tek akış
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/settings.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'İletişim | Psikolog Doğukan Kopuk — İzmit';
$seo_description = 'Psikolog Doğukan Kopuk ile iletişime geçin. Randevu talebi ve sorularınız için form, telefon veya WhatsApp ile ulaşın. İzmit, Kocaeli.';
$seo_canonical   = SITE_URL . '/iletisim';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa', 'url' => SITE_URL],
    ['name' => 'İletişim', 'url' => ''],
];

$seo_schemas = [schemaLocalBusiness()];

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

require_once BASE_PATH . '/templates/partials/header.php';

$subjectOptions = [
    ''                                 => 'Konu seçin *',
    'Randevu bilgisi almak istiyorum'  => 'Randevu bilgisi almak istiyorum',
    'Ücret ve seans hakkında bilgi'    => 'Ücret ve seans hakkında bilgi',
    'Online terapi hakkında'           => 'Online terapi hakkında',
    'Genel bilgi'                      => 'Genel bilgi',
    'Diğer'                            => 'Diğer',
];
?>

<!-- ═══════════════════════════════════════════════════════════
     İLETİŞİM — 2 Kolon: Sol info + Sağ form
     ═══════════════════════════════════════════════════════════ -->
<section class="contact__hero">
    <div class="container contact__layout">

        <!-- SOL: Bilgi + CTA'lar -->
        <div class="contact__info-col">

            <?php $breadcrumbs = $seo_breadcrumbs;
            require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

            <h1>İletişime Geçin</h1>
            <p class="contact__desc">
                Sorularınız veya randevu talepleriniz için aşağıdaki formu doldurabilir,
                telefon ya da WhatsApp ile doğrudan ulaşabilirsiniz.
            </p>

            <!-- Info Kartları -->
            <div class="contact__info-cards">
                <div class="contact__info-card">
                    <i class="uil uil-map-marker" aria-hidden="true"></i>
                    <div>
                        <h4>Adres</h4>
                        <p><?= e(siteSetting('address_district', ADDRESS_DISTRICT)) ?>, <?= e(siteSetting('address_city', ADDRESS_CITY)) ?>, Türkiye</p>
                    </div>
                </div>
                <div class="contact__info-card">
                    <i class="uil uil-phone" aria-hidden="true"></i>
                    <div>
                        <h4>Telefon</h4>
                        <p><a href="tel:<?= e(siteSetting('contact_phone_href', CONTACT_PHONE_HREF)) ?>" style="color:inherit;">
                            <?= e(siteSetting('contact_phone', CONTACT_PHONE)) ?>
                        </a></p>
                    </div>
                </div>
                <div class="contact__info-card">
                    <i class="uil uil-whatsapp" aria-hidden="true"></i>
                    <div>
                        <h4>WhatsApp</h4>
                        <p><a href="https://wa.me/<?= siteSetting('contact_whatsapp', CONTACT_WHATSAPP) ?>?text=<?= urlencode('Merhaba, bilgi almak istiyorum.') ?>"
                              target="_blank" rel="noopener noreferrer" style="color:inherit;">
                            Mesaj Gönder
                        </a></p>
                    </div>
                </div>
                <div class="contact__info-card">
                    <i class="uil uil-clock" aria-hidden="true"></i>
                    <div>
                        <h4>Çalışma Saatleri</h4>
                        <p><?= e(siteSetting('work_hours_weekday', WORK_HOURS_WEEKDAY)) ?><br><?= e(siteSetting('work_hours_saturday', WORK_HOURS_SATURDAY)) ?></p>
                    </div>
                </div>
            </div>

            <!-- CTA Butonları -->
            <div class="contact__cta-group">
                <a href="<?= ROOT_URL ?>randevu" class="btn">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevu Al
                </a>
                <a href="tel:<?= e(siteSetting('contact_phone_href', CONTACT_PHONE_HREF)) ?>" class="btn btn--outline">
                    <i class="uil uil-phone" aria-hidden="true"></i>
                    Hemen Ara
                </a>
                <a href="https://wa.me/<?= siteSetting('contact_whatsapp', CONTACT_WHATSAPP) ?>?text=<?= urlencode('Merhaba, bilgi almak istiyorum.') ?>"
                   class="btn btn--outline" target="_blank" rel="noopener noreferrer">
                    <i class="uil uil-whatsapp" aria-hidden="true"></i>
                    WhatsApp
                </a>
            </div>

            <p class="contact__response-note">
                <i class="uil uil-clock" aria-hidden="true"></i>
                Genellikle 24 saat içinde geri dönüş yapılmaktadır.
            </p>
        </div>

        <!-- SAĞ: Form -->
        <div class="contact__form-col">

            <div class="contact__form-wrapper">
                <h2>Mesaj Gönderin</h2>

                <?php flashRender(); ?>

                <form action="<?= ROOT_URL ?>iletisim/gonder"
                      method="POST"
                      class="contact__form"
                      novalidate>

                    <?= csrfField() ?>

                    <div class="contact__form-row">
                        <div class="form__control">
                            <label for="full_name">Ad Soyad <span aria-hidden="true">*</span></label>
                            <input type="text" id="full_name" name="full_name"
                                   value="<?= e($old['full_name'] ?? '') ?>"
                                   placeholder="Adınız Soyadınız"
                                   required autocomplete="name" maxlength="100">
                        </div>
                        <div class="form__control">
                            <label for="phone">Telefon <span aria-hidden="true">*</span></label>
                            <input type="tel" id="phone" name="phone"
                                   value="<?= e($old['phone'] ?? '') ?>"
                                   placeholder="0500 000 00 00"
                                   required autocomplete="tel" maxlength="20">
                        </div>
                    </div>

                    <div class="contact__form-row">
                        <div class="form__control">
                            <label for="email">
                                E-posta
                                <small style="font-weight:400;color:var(--color-text-faint);">(opsiyonel)</small>
                            </label>
                            <input type="email" id="email" name="email"
                                   value="<?= e($old['email'] ?? '') ?>"
                                   placeholder="ornek@email.com"
                                   autocomplete="email" maxlength="150">
                        </div>
                        <div class="form__control">
                            <label for="subject">Konu <span aria-hidden="true">*</span></label>
                            <select id="subject" name="subject" required>
                                <?php foreach ($subjectOptions as $val => $label): ?>
                                <option value="<?= e($val) ?>"
                                    <?= ($old['subject'] ?? '') === $val ? 'selected' : '' ?>
                                    <?= $val === '' ? 'disabled' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="contact__privacy-notice" role="note">
                        <i class="uil uil-info-circle" aria-hidden="true"></i>
                        <p>
                            <strong>Önemli:</strong>
                            Bu forma sağlık durumunuza, tanınıza veya tedavinize ilişkin
                            kişisel sağlık bilgisi yazmayınız.
                            Form yalnızca genel iletişim amaçlıdır.
                        </p>
                    </div>

                    <div class="form__control">
                        <label for="message">
                            Mesajınız <span aria-hidden="true">*</span>
                            <small style="font-weight:400;color:var(--color-text-faint);">(max 300 karakter)</small>
                        </label>
                        <textarea id="message" name="message"
                                  rows="4"
                                  placeholder="Mesajınızı buraya yazın..."
                                  required maxlength="300"><?= e($old['message'] ?? '') ?></textarea>
                        <small id="message-count" style="color:var(--color-text-faint);">0 / 300</small>
                    </div>

                    <div class="contact__consent">
                        <div class="form__control inline">
                            <input type="checkbox" id="privacy_notice_accepted"
                                   name="privacy_notice_accepted" value="1" required>
                            <label for="privacy_notice_accepted">
                                <a href="<?= ROOT_URL ?>kvkk-aydinlatma" target="_blank" rel="noopener">
                                    KVKK Aydınlatma Metnini
                                </a>
                                okudum ve kişisel verilerimin belirtilen amaçlarla işlenmesini anlıyorum. *
                            </label>
                        </div>
                        <!-- <div class="form__control inline">
                            <input type="checkbox" id="commercial_consent_given"
                                   name="commercial_consent_given" value="1">
                            <label for="commercial_consent_given">
                                Ticari elektronik ileti almak istiyorum.
                                <small style="display:block;color:var(--color-text-faint);margin-top:2px;">
                                    Opsiyonel — onay vermemeniz iletişim sürecini etkilemez.
                                </small>
                            </label>
                        </div> -->
                    </div>

                    <button type="submit" name="submit" class="btn">
                        Gönder <i class="uil uil-message" aria-hidden="true"></i>
                    </button>

                </form>
            </div>
        </div>

    </div>
</section>

<script>
(function () {
    var textarea = document.getElementById('message');
    var counter  = document.getElementById('message-count');
    if (!textarea || !counter) return;
    function update() {
        var len = textarea.value.length;
        counter.textContent = len + ' / 300';
        counter.style.color = len > 270 ? 'var(--color-danger)' : 'var(--color-text-faint)';
    }
    textarea.addEventListener('input', update);
    update();
})();
</script>

<!-- ── HARİTA ────────────────────────────────────────────── -->
<?php
$showMapsContact = siteSetting('maps_show_contact', '1') === '1';
$contactMapsEmbed = siteSetting('maps_embed_url', '');
$contactMapsDir   = siteSetting('maps_directions_url', '');

if ($showMapsContact && $contactMapsEmbed !== ''):
?>
<section class="contact-map-section">
    <div class="container">
        <div class="contact-map__wrap">
            <iframe src="<?= e($contactMapsEmbed) ?>"
                    width="100%" height="400"
                    class="contact-map__iframe"
                    allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Psikolog Doğukan Kopuk — Konum"></iframe>
        </div>
        <?php if ($contactMapsDir !== ''): ?>
        <div class="text-center" style="margin-top:var(--space-4);">
            <a href="<?= e($contactMapsDir) ?>" class="btn btn--outline"
               target="_blank" rel="noopener noreferrer">
                <i class="uil uil-directions" aria-hidden="true"></i>
                Google Maps'te Yol Tarifi Al
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
