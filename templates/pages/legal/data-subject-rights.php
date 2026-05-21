<?php
/**
 * templates/pages/legal/data-subject-rights.php
 * URL: /veri-sahibi-basvuru
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'Veri Sahibi Başvuru Formu | Psikolog Doğukan Kopuk';
$seo_description = 'KVKK kapsamındaki haklarınızı kullanmak için veri sahibi başvuru formu.';
$seo_canonical   = SITE_URL . '/veri-sahibi-basvuru';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa',             'url' => SITE_URL],
    ['name' => 'Veri Sahibi Başvurusu', 'url' => ''],
];

require_once BASE_PATH . '/templates/partials/header.php';
?>

<section style="margin-top:70px;padding:var(--space-9) 0;">
    <div class="container" style="max-width:820px;">

        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <h1>Veri Sahibi Başvuru Formu</h1>
        <p style="color:var(--color-text-muted);margin-bottom:var(--space-7);">
            6698 sayılı KVKK'nın 11. maddesi kapsamındaki haklarınızı kullanmak için
            bu formu doldurabilir veya aşağıdaki iletişim bilgilerine yazabilirsiniz.
        </p>

        <div class="singlepost__body" style="margin-bottom:var(--space-8);">
            <h2>Başvuru Yöntemleri</h2>
            <ul>
                <li>
                    <strong>E-posta:</strong>
                    <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
                    (Konu: KVKK Veri Sahibi Başvurusu)
                </li>
                <li>
                    <strong>Posta:</strong>
                    <?= e(PSYCHOLOGIST_NAME) ?>,
                    <?= e(ADDRESS_DISTRICT) ?>, <?= e(ADDRESS_CITY) ?>, Türkiye
                </li>
            </ul>
            <p>
                Başvurularınız, kimliğiniz doğrulandıktan sonra en geç
                <strong>30 gün</strong> içinde yanıtlanacaktır.
                Başvurunuzda aşağıdaki bilgileri belirtmeniz işlemi hızlandıracaktır.
            </p>
        </div>

        <!-- Başvuru Formu -->
        <div class="contact__form-wrapper">
            <h2>Başvuru Formu</h2>
            <p style="color:var(--color-text-muted);margin-bottom:var(--space-5);font-size:0.9rem;">
                Bu form, KVKK kapsamındaki haklarınızı kullanmak amacıyla
                e-posta olarak tarafımıza iletilecektir.
            </p>

            <form action="mailto:<?= e(CONTACT_EMAIL) ?>"
                  method="GET"
                  enctype="text/plain"
                  style="display:flex;flex-direction:column;gap:var(--space-4);">

                <div class="form__control">
                    <label for="req_name">Ad Soyad *</label>
                    <input type="text" id="req_name" name="subject"
                           placeholder="KVKK Başvurusu - Adınız Soyadınız"
                           required>
                </div>

                <div class="form__control">
                    <label>Kullanmak İstediğiniz Hak</label>
                    <div style="display:flex;flex-direction:column;gap:var(--space-2);">
                        <?php
                        $rights = [
                            'Kişisel verilerimin işlenip işlenmediğini öğrenmek istiyorum.',
                            'İşlenen kişisel verilerim hakkında bilgi talep ediyorum.',
                            'Kişisel verilerimin düzeltilmesini talep ediyorum.',
                            'Kişisel verilerimin silinmesini / yok edilmesini talep ediyorum.',
                            'Kişisel verilerimin işlenmesine itiraz ediyorum.',
                            'Kişisel verilerimin aktarıldığı tarafları öğrenmek istiyorum.',
                            'Otomatik karar verme süreçlerine itiraz ediyorum.',
                            'Zararın giderilmesini talep ediyorum.',
                        ];
                        foreach ($rights as $right): ?>
                        <label class="form__control inline" style="margin-bottom:0;">
                            <input type="checkbox" name="body" value="<?= e($right) ?>">
                            <span><?= e($right) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form__control">
                    <label for="req_detail">Açıklama (opsiyonel)</label>
                    <textarea id="req_detail" name="body"
                              rows="4"
                              placeholder="Başvurunuzla ilgili ek bilgi..."
                              maxlength="1000"></textarea>
                </div>

                <div class="contact__privacy-notice" role="note">
                    <i class="uil uil-info-circle" aria-hidden="true"></i>
                    <p>
                        Bu form, e-posta istemcinizi açacaktır. Kimliğinizi doğrulamak için
                        nüfus cüzdanı veya pasaport kopyası e-postanıza eklenmelidir.
                    </p>
                </div>

                <button type="submit" class="btn">
                    <i class="uil uil-envelope" aria-hidden="true"></i>
                    E-posta Oluştur
                </button>

            </form>
        </div>

    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
