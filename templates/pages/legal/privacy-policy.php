<?php
/**
 * templates/pages/legal/privacy-policy.php
 * URL: /gizlilik-politikasi
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'Gizlilik Politikası | Psikolog Doğukan Kopuk';
$seo_description = 'Psikolog Doğukan Kopuk web sitesi gizlilik politikası. Kişisel verilerinizin nasıl toplandığı, kullanıldığı ve korunduğu hakkında bilgi.';
$seo_canonical   = SITE_URL . '/gizlilik-politikasi';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa',          'url' => SITE_URL],
    ['name' => 'Gizlilik Politikası', 'url' => ''],
];

require_once BASE_PATH . '/templates/partials/header.php';
?>

<section style="margin-top:70px;padding:var(--space-9) 0;">
    <div class="container" style="max-width:820px;">

        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <h1>Gizlilik Politikası</h1>
        <p style="color:var(--color-text-muted);margin-bottom:var(--space-7);">
            Son güncelleme: 01.01.2025
        </p>

        <div class="singlepost__body">

            <h2>1. Genel Bilgi</h2>
            <p>
                Bu gizlilik politikası, <?= e(SITE_URL) ?> adresinde faaliyet gösteren
                <?= e(PSYCHOLOGIST_NAME) ?> tarafından işletilen web sitesinin
                ziyaretçi ve kullanıcılarına yönelik veri işleme pratiklerini açıklamaktadır.
            </p>

            <h2>2. Toplanan Veriler</h2>

            <h3>2.1 Form Aracılığıyla Toplanan Veriler</h3>
            <p>
                İletişim ve randevu formlarını doldurduğunuzda ad soyad, telefon,
                opsiyonel olarak e-posta adresiniz toplanmaktadır.
                Sağlık verisi, tanı veya semptom bilgisi talep edilmemektedir.
            </p>

            <h3>2.2 Otomatik Toplanan Veriler</h3>
            <p>
                Site ziyareti sırasında IP adresi, tarayıcı türü ve sayfa gezinme
                bilgileri teknik güvenlik amacıyla otomatik olarak kaydedilebilir.
            </p>

            <h3>2.3 Çerezler</h3>
            <p>
                Site, oturum çerezleri ve tercih çerezleri kullanmaktadır.
                Analitik çerezler (Google Analytics) yalnızca onayınız halinde etkinleştirilir.
                Detaylar için <a href="<?= ROOT_URL ?>cerez-politikasi">Çerez Politikası</a>nı
                inceleyebilirsiniz.
            </p>

            <h2>3. Verilerin Kullanım Amacı</h2>
            <ul>
                <li>Randevu ve iletişim taleplerini karşılamak</li>
                <li>Site güvenliğini sağlamak</li>
                <li>Yasal yükümlülükleri yerine getirmek</li>
            </ul>

            <h2>4. Veri Güvenliği</h2>
            <p>
                Verileriniz HTTPS şifreli bağlantı üzerinden iletilmekte,
                güvenli sunucularda saklanmaktadır. Yetkisiz erişimi önlemek için
                teknik ve idari tedbirler alınmaktadır.
            </p>

            <h2>5. Haklarınız</h2>
            <p>
                KVKK kapsamındaki haklarınız için
                <a href="<?= ROOT_URL ?>kvkk-aydinlatma">KVKK Aydınlatma Metni</a>ni
                inceleyebilir veya
                <a href="<?= ROOT_URL ?>veri-sahibi-basvuru">Veri Sahibi Başvuru Formu</a>nu
                kullanabilirsiniz.
            </p>

            <h2>6. İletişim</h2>
            <p>
                <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
            </p>

        </div>

    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
