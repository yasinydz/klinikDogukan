<?php
/**
 * templates/pages/legal/kvkk.php
 * URL: /kvkk-aydinlatma
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'KVKK Aydınlatma Metni | Psikolog Doğukan Kopuk';
$seo_description = '6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında kişisel verilerinizin işlenmesine ilişkin aydınlatma metni.';
$seo_canonical   = SITE_URL . '/kvkk-aydinlatma';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa',        'url' => SITE_URL],
    ['name' => 'KVKK Aydınlatma', 'url' => ''],
];

require_once BASE_PATH . '/templates/partials/header.php';

$effectiveDate = '01.01.2025';
?>

<section style="margin-top:70px;padding:var(--space-9) 0;">
    <div class="container" style="max-width:820px;">

        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <h1>Kişisel Verilerin Korunması Kanunu Aydınlatma Metni</h1>
        <p style="color:var(--color-text-muted);margin-bottom:var(--space-7);">
            Son güncelleme: <?= e($effectiveDate) ?>
        </p>

        <div class="singlepost__body">

            <h2>1. Veri Sorumlusu</h2>
            <p>
                6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında
                veri sorumlusu sıfatıyla hareket eden kişi:
                <strong><?= e(PSYCHOLOGIST_NAME) ?></strong>,
                <?= e(PSYCHOLOGIST_TITLE) ?>,
                <?= e(ADDRESS_DISTRICT) ?>, <?= e(ADDRESS_CITY) ?>, Türkiye.
            </p>

            <h2>2. İşlenen Kişisel Veriler</h2>
            <p>
                İletişim ve randevu formları aracılığıyla aşağıdaki kişisel veriler işlenmektedir:
            </p>
            <ul>
                <li>Ad ve soyad</li>
                <li>Telefon numarası</li>
                <li>E-posta adresi (opsiyonel olarak alınmaktadır)</li>
                <li>Tercih edilen randevu tarihi ve saati</li>
                <li>Görüşme tipi tercihi (yüz yüze / online)</li>
                <li>IP adresi ve kullanıcı ajanı (güvenlik ve doğrulama amacıyla)</li>
            </ul>
            <p>
                <strong>Önemli:</strong> Sağlık verileri, tanı bilgileri, semptom
                açıklamaları veya tedavi bilgileri web formları aracılığıyla talep
                edilmemektedir. Bu tür bilgiler yalnızca terapi süreci içinde ve
                sözlü/yazılı terapi seansı kapsamında alınmaktadır.
            </p>

            <h2>3. Kişisel Verilerin İşlenme Amaçları</h2>
            <ul>
                <li>Randevu taleplerinin yönetimi ve geri dönüş yapılması</li>
                <li>İletişim taleplerinin karşılanması</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi</li>
                <li>Hizmet kalitesinin değerlendirilmesi</li>
            </ul>

            <h2>4. Hukuki Sebepler</h2>
            <p>
                Kişisel verileriniz, KVKK'nın 5. maddesi kapsamında aşağıdaki
                hukuki sebeplere dayanılarak işlenmektedir:
            </p>
            <ul>
                <li>
                    <strong>Sözleşmenin kurulması veya ifası:</strong>
                    Randevu talebinin alınması ve hizmetin sunulması amacıyla.
                </li>
                <li>
                    <strong>Meşru menfaat:</strong>
                    İletişim taleplerinin karşılanması ve hizmet kalitesinin
                    değerlendirilmesi amacıyla.
                </li>
                <li>
                    <strong>Hukuki yükümlülük:</strong>
                    Yasal saklama ve raporlama yükümlülükleri kapsamında.
                </li>
            </ul>

            <h2>5. Verilerin Aktarılması</h2>
            <p>
                Kişisel verileriniz, yasal zorunluluk bulunmadıkça üçüncü taraflarla
                paylaşılmamaktadır. Teknik altyapı hizmetleri kapsamında yurt içindeki
                hosting sağlayıcılarıyla sınırlı ölçüde paylaşılabilir.
            </p>

            <h2>6. Saklama Süreleri</h2>
            <ul>
                <li>İletişim formu verileri: 6 ay</li>
                <li>Randevu talep verileri: 2 yıl</li>
                <li>Onay kayıtları (consent log): 3 yıl (hukuki kanıt)</li>
            </ul>
            <p>
                Saklama süresi dolan veriler anonim hale getirilmekte veya silinmektedir.
            </p>

            <h2>7. Veri Sahibi Hakları</h2>
            <p>KVKK'nın 11. maddesi kapsamında aşağıdaki haklara sahipsiniz:</p>
            <ul>
                <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                <li>İşlenmişse buna ilişkin bilgi talep etme</li>
                <li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                <li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme</li>
                <li>Eksik veya yanlış işlenmiş olması halinde düzeltilmesini isteme</li>
                <li>Silinmesini veya yok edilmesini isteme</li>
                <li>İşlenmesine itiraz etme</li>
                <li>Otomatik sistemler vasıtasıyla aleyhinize bir sonucun ortaya çıkmasına itiraz etme</li>
                <li>Zararın giderilmesini talep etme</li>
            </ul>
            <p>
                Bu haklarınızı kullanmak için
                <a href="<?= ROOT_URL ?>veri-sahibi-basvuru">Veri Sahibi Başvuru Formu</a>nu
                kullanabilir ya da
                <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
                adresine yazabilirsiniz.
            </p>

            <h2>8. İletişim</h2>
            <p>
                Bu aydınlatma metniyle ilgili sorularınız için:<br>
                <strong><?= e(PSYCHOLOGIST_NAME) ?></strong><br>
                <?= e(ADDRESS_DISTRICT) ?>, <?= e(ADDRESS_CITY) ?><br>
                <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a><br>
                <a href="tel:<?= e(CONTACT_PHONE_HREF) ?>"><?= e(CONTACT_PHONE) ?></a>
            </p>

        </div><!-- /.singlepost__body -->

        <div style="margin-top:var(--space-7);padding-top:var(--space-6);border-top:1px solid var(--color-border);">
            <div style="display:flex;gap:var(--space-3);flex-wrap:wrap;">
                <a href="<?= ROOT_URL ?>gizlilik-politikasi" class="btn btn--outline btn--sm">
                    Gizlilik Politikası
                </a>
                <a href="<?= ROOT_URL ?>cerez-politikasi" class="btn btn--outline btn--sm">
                    Çerez Politikası
                </a>
                <a href="<?= ROOT_URL ?>veri-sahibi-basvuru" class="btn btn--outline btn--sm">
                    Veri Sahibi Başvurusu
                </a>
            </div>
        </div>

    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
