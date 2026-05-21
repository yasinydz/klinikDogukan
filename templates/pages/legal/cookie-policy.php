<?php
/**
 * templates/pages/legal/cookie-policy.php
 * URL: /cerez-politikasi
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'Çerez Politikası | Psikolog Doğukan Kopuk';
$seo_description = 'Bu web sitesinde kullanılan çerezler ve tercihlerinizi nasıl yönetebileceğiniz hakkında bilgi.';
$seo_canonical   = SITE_URL . '/cerez-politikasi';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa',       'url' => SITE_URL],
    ['name' => 'Çerez Politikası', 'url' => ''],
];

require_once BASE_PATH . '/templates/partials/header.php';
?>

<section style="margin-top:70px;padding:var(--space-9) 0;">
    <div class="container" style="max-width:820px;">

        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <h1>Çerez Politikası</h1>
        <p style="color:var(--color-text-muted);margin-bottom:var(--space-7);">
            Son güncelleme: 01.01.2025
        </p>

        <div class="singlepost__body">

            <h2>1. Çerez Nedir?</h2>
            <p>
                Çerezler, web sitelerinin tarayıcınıza yerleştirdiği küçük metin dosyalarıdır.
                Sitenin düzgün çalışması, tercihlerinizin hatırlanması ve kullanım istatistiklerinin
                toplanması gibi amaçlarla kullanılır.
            </p>

            <h2>2. Kullandığımız Çerez Türleri</h2>

            <h3>2.1 Zorunlu Çerezler (Her Zaman Aktif)</h3>
            <p>
                Bu çerezler sitenin temel işlevleri için gereklidir ve devre dışı bırakılamaz.
                Kişisel bilgi saklamazlar.
            </p>
            <table>
                <thead>
                    <tr>
                        <th>Çerez Adı</th>
                        <th>Amaç</th>
                        <th>Süre</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PHPSESSID</td>
                        <td>Oturum yönetimi (form güvenliği, mesajlar)</td>
                        <td>Oturum süresi</td>
                    </tr>
                    <tr>
                        <td>cookie_consent</td>
                        <td>Çerez tercih kaydı</td>
                        <td>1 yıl</td>
                    </tr>
                    <tr>
                        <td>theme</td>
                        <td>Koyu/açık tema tercihi</td>
                        <td>1 yıl</td>
                    </tr>
                </tbody>
            </table>

            <h3>2.2 Analitik Çerezler (Onayınıza Bağlı)</h3>
            <p>
                Bu çerezler, sitenin nasıl kullanıldığını anlamamıza yardımcı olur.
                Onayınız alınmadan etkinleştirilmezler.
            </p>
            <table>
                <thead>
                    <tr>
                        <th>Çerez</th>
                        <th>Sağlayıcı</th>
                        <th>Amaç</th>
                        <th>Süre</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>_ga, _ga_*</td>
                        <td>Google Analytics</td>
                        <td>Ziyaretçi istatistikleri</td>
                        <td>2 yıl</td>
                    </tr>
                </tbody>
            </table>

            <h2>3. Tercihlerinizi Yönetme</h2>
            <p>
                Sayfanın alt kısmındaki çerez banner'ı üzerinden tercihlerinizi
                dilediğiniz zaman değiştirebilirsiniz.
                Tarayıcı ayarlarından da çerezleri silebilir veya engelleyebilirsiniz.
            </p>

            <p>
                <button onclick="localStorage.removeItem('cookie_consent'); location.reload();"
                        class="btn btn--outline btn--sm"
                        type="button">
                    Çerez Tercihlerimi Sıfırla
                </button>
            </p>

            <h2>4. İletişim</h2>
            <p>
                <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
            </p>

        </div>

    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
