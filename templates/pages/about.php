<?php
/**
 * templates/pages/about.php
 * URL: /hakkimda
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/public_profile.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'Hakkımda | Klinik Psikolog Doğukan Kopuk — İzmit';
$seo_description = 'Klinik Psikolog Doğukan Kopuk hakkında — Eğitim geçmişi ve çalışma yaklaşımı. İzmit ve Kocaeli\'de yüz yüze, Türkiye geneli online seans.';
$seo_canonical   = SITE_URL . '/hakkimda';
$seo_og_image    = publicAvatarUrl();
$seo_og_type     = 'profile';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa', 'url' => SITE_URL],
    ['name' => 'Hakkımda', 'url' => ''],
];

$seo_schemas = [schemaPerson(), schemaLocalBusiness()];

require_once BASE_PATH . '/templates/partials/header.php';
?>

<!-- Hero -->
<section class="about__hero">
    <div class="container about__hero-container">

        <div class="about__hero-image">
            <img src="<?= e(publicAvatarUrl()) ?>"
                 alt="Klinik Psikolog Doğukan Kopuk — İzmit"
                 width="380" height="460"
                 loading="eager">
        </div>

        <div class="about__hero-text">
            <?php $breadcrumbs = $seo_breadcrumbs;
            require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

            <span class="category__button">Hakkımda</span>
            <h1>Merhaba, ben Doğukan Kopuk</h1>
            <p class="about__tagline">Klinik Psikolog</p>

            <p>
                Üsküdar Üniversitesi Psikoloji lisans eğitimimin ardından, yine Üsküdar Üniversitesi’nde Klinik Psikoloji yüksek lisans eğitimimi tamamladım. 
                Yüksek lisans bitirme projemi “Antisosyal Kişilik Bozukluğuna Sahip Bireylerde Suç Eğilimi” konusu üzerine hazırladım. 
                Ergen ve yetişkinlerle psikolojik destek süreçleri yürütüyor, çocuklarla ise psikolojik test uygulamaları gerçekleştiriyorum.
                Çalışmalarımda özellikle kaygı, depresyon ve travma alanlarına odaklanıyorum.
            </p>
            <p>
                Daha eklektik bir çalışma tarzını benimsesem de, Bilişsel Davranışçı Terapi, Şema Terapi psikanalitik ve psikodinamik yaklaşımlardan beslenen bir bakış açısıyla, her danışanın kendi yaşam öyküsüne ve ihtiyaçlarına özgü bir süreç oluşturmayı önemsiyorum. 
                Klinik uygulamalarıma hem bilimsel bir titizlikle hem de insani bir duyarlılıkla yaklaşarak, bireyin ruhsal dünyasını anlamaya yönelik bütüncül bir bakış açısı geliştirmeye çalışıyorum. 
                Danışanlarıma yüz yüze ve online olarak hizmet veriyorum.
            </p>
             <p>
                Klinik çalışmalarımın yanı sıra, alanda yetkin meslektaşlarımla birlikte yazı ve yayın çalışmalarımı sürdürmekte; Rehber Klinik bünyesinde ruh sağlığı alanındaki meslektaşlarıma eğitim vermekteyim.
            </p>

            <div class="cta-group">
                <a href="<?= ROOT_URL ?>randevu" class="btn">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevu Al
                </a>
                <a href="<?= ROOT_URL ?>iletisim" class="btn btn--outline">
                    İletişime Geçin
                </a>
            </div>
        </div>

    </div>
</section>

<!-- İstatistikler -->
<!-- <section class="about__stats">
    <div class="container about__stats-container">
        <div class="about__stat-card">
            <h2>8+</h2>
            <p>Yıllık Klinik Deneyim</p>
        </div>
        <div class="about__stat-card">
            <h2>500+</h2>
            <p>Desteklenen Danışan</p>
        </div>
        <div class="about__stat-card">
            <h2>BDT</h2>
            <p>Uzmanı</p>
        </div>
        <div class="about__stat-card">
            <h2>Online</h2>
            <p>Türkiye Geneli Seans</p>
        </div>
    </div>
</section> -->
<section class="trust-ribbon" aria-label="Uzmanlık bilgileri">
    <div class="container trust-ribbon__inner">
        <div class="trust-ribbon__item">
            <i class="uil uil-award" aria-hidden="true"></i>
            <span>Lisanslı Klinik Psikolog</span>
        </div>
        <div class="trust-ribbon__item">
            <i class="uil uil-shield-check" aria-hidden="true"></i>
            <span>KVKK Uyumlu</span>
        </div>
        <div class="trust-ribbon__item">
            <i class="uil uil-video" aria-hidden="true"></i>
            <span>Online &amp; Yüz Yüze</span>
        </div>
        <div class="trust-ribbon__item">
            <i class="uil uil-map-marker" aria-hidden="true"></i>
            <span>İzmit, Kocaeli</span>
        </div>
    </div>
</section>

<!-- Eğitim & Sertifikalar — E-E-A-T için kritik -->
<section class="about__education">
    <div class="container about__education-container">
        <h2>Eğitim &amp; Sertifikalar</h2>
        <div class="about__timeline">
            <?php
            $timeline = [
                ['year' => '2022', 'title' => 'Üsküdar Üniversitesi',             'desc' => 'Psikoloji Lisans'],
                ['year' => '2022', 'title' => 'DATEM', 'desc' => 'İleri Düzey Bilişsel Davranışçı Terapi Eğitimi'],
                ['year' => '2022', 'title' => 'Üsküdar Üniversitesi-AİLEMER',                  'desc' => 'Çocuk Psikometrik Testleri Eğitimi'],
                ['year' => '2024', 'title' => 'Üsküdar Üniversitesi',              'desc' => 'Klinik Psikoloji Yüksek Lisans'],
                ['year' => '2025', 'title' => 'Rehber Klinik',              'desc' => 'Bilişsel Davranışçı Terapi, Sanat Terapisi, Spor Psikolojisi, EMDR Eğitimleri ve Stajları'],
                ['year' => '2025', 'title' => 'Human Ports-Rehber',              'desc' => ' Klinik Bilişsel Davranışçı Terapi Eğitimi ve Süpervizyonu -Belgrad, Sırbistan'],
            ];
            foreach ($timeline as $item): ?>
            <div class="about__timeline-item">
                <div class="about__timeline-year"><?= e($item['year']) ?></div>
                <div class="about__timeline-content">
                    <h4><?= e($item['title']) ?></h4>
                    <p><?= e($item['desc']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Çalışma Yaklaşımı -->
<section class="about__values">
    <div class="container about__values-container">
        <h2>Çalışma Yaklaşımım</h2>
        <div class="about__values-grid">
            <?php
            $values = [
                ['icon' => 'uil-heart',        'title' => 'Empati ve Yargısız Kabul',  'desc' => 'Her danışanın yaşam öyküsünü, duygusal deneyimini ve bireysel ihtiyaçlarını yargıdan uzak, saygılı ve kapsayıcı bir anlayışla ele alırız.'],
                ['icon' => 'uil-shield-check', 'title' => 'Güven ve Gizlilik',   'desc' => 'Terapi sürecinde paylaşılan tüm bilgi ve deneyimler, mesleki etik ilkeler ve gizlilik esasları doğrultusunda titizlikle korunur.'],
                ['icon' => 'uil-chart-line',   'title' => 'Gelişim Odaklı Yaklaşım', 'desc' => 'Psikoterapiyi, bireyin iç görü kazanmasını, duygusal farkındalığını güçlendirmesini ve kalıcı psikolojik gelişim sağlamasını destekleyen bir süreç olarak görürüz.'],
                ['icon' => 'uil-book-open',    'title' => 'Bilimsel ve Etik Çerçeve',   'desc' => 'Uygulamalarımızda, bilimsel araştırmalarla desteklenen, kanıta dayalı ve etik standartlara uygun yöntemleri esas alırız.'],
            ];
            foreach ($values as $v): ?>
            <div class="about__value-card">
                <i class="uil <?= e($v['icon']) ?>" aria-hidden="true"></i>
                <h4><?= e($v['title']) ?></h4>
                <p><?= e($v['desc']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Uzmanlık Alanları -->
<section>
    <div class="container">
        <div class="section__header">
            <h2>Uzmanlık Alanlarım</h2>
            <p class="text-muted">
                Aşağıdaki konularda terapi sunuyorum.
            </p>
        </div>
        <div class="home-hizmetler__grid">
            <?php
            $specialties = [
                ['slug' => 'anksiyete-terapisi', 'icon' => 'uil-heart-rate', 'title' => 'Anksiyete Terapisi',  'desc' => 'Kaygı bozuklukları ve panik atak için BDT tabanlı destek.'],
                ['slug' => 'depresyon-terapisi',  'icon' => 'uil-sad',        'title' => 'Depresyon Terapisi',  'desc' => 'Kanıta dayalı yöntemlerle bireysel destek.'],
                ['slug' => 'travma-terapisi',     'icon' => 'uil-shield',     'title' => 'Travma Terapisi',     'desc' => 'EMDR ve travma odaklı terapi yöntemleri.'],
                ['slug' => 'okb-terapisi',        'icon' => 'uil-redo',       'title' => 'OKB Terapisi',        'desc' => 'ERP yöntemiyle obsesif kompulsif bozukluk tedavisi.'],
                ['slug' => 'iliskisel-sorunlar',  'icon' => 'uil-users-alt',  'title' => 'İlişkisel Sorunlar',  'desc' => 'Çift terapisi ve ilişki danışmanlığı.'],
                ['slug' => 'online-terapi',       'icon' => 'uil-laptop',     'title' => 'Online Terapi',       'desc' => 'Türkiye\'nin her yerinden güvenli seans.'],
            ];
            foreach ($specialties as $sp): ?>
            <a href="<?= ROOT_URL ?>hizmetler/<?= urlencode($sp['slug']) ?>" class="hizmet-karti">
                <div class="hizmet-karti__ikon" aria-hidden="true">
                    <i class="uil <?= e($sp['icon']) ?>"></i>
                </div>
                <h3><?= e($sp['title']) ?></h3>
                <p><?= e($sp['desc']) ?></p>
                <span class="hizmet-karti__link">
                    Detaylı İncele
                    <i class="uil uil-arrow-right" aria-hidden="true"></i>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:var(--space-7);">
            <a href="<?= ROOT_URL ?>hizmetler" class="btn btn--outline">
                Tüm Hizmetleri Gör
            </a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="home-cta">
    <div class="container home-cta__container">
        <h2>Birlikte Çalışmaya Hazır Mısınız?</h2>
        <p class="text-muted">
            Size uygun plan için bugün iletişime geçin.
        </p>
        <div class="cta-group cta-group--center">
            <a href="<?= ROOT_URL ?>randevu" class="btn">
                <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                Randevu Al
            </a>
            <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode('Merhaba, randevu almak istiyorum.') ?>"
               class="btn btn--outline"
               target="_blank" rel="noopener noreferrer">
                <i class="uil uil-whatsapp" aria-hidden="true"></i>
                WhatsApp
            </a>
        </div>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
