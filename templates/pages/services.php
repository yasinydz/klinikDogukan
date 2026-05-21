<?php
/**
 * templates/pages/services.php
 * URL: /hizmetler
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'Terapi Hizmetleri | Psikolog Doğukan Kopuk — İzmit';
$seo_description = 'Psikolog Doğukan Kopuk terapi hizmetleri — Anksiyete, depresyon, OKB, travma, ilişkisel sorunlar ve online terapi. İlk görüşme ücretsizdir.';
$seo_canonical   = SITE_URL . '/hizmetler';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa',  'url' => SITE_URL],
    ['name' => 'Hizmetler', 'url' => ''],
];

$seo_schemas = [schemaLocalBusiness(), schemaPerson()];

require_once BASE_PATH . '/templates/partials/header.php';

$services = mysqli_query($connection, "
    SELECT slug, title, summary, icon_class, image
    FROM services
    WHERE is_active = 1
    ORDER BY display_order ASC
");

$faqItems = [
    [
        'question' => 'Terapiye nasıl başlarım?',
        'answer'   => 'İletişim formu, telefon veya WhatsApp ile ulaşmanız yeterlidir. İlk görüşmede ihtiyaçlarınızı değerlendirip size uygun bir plan oluştururuz. İlk seans ücretsizdir ve bağlayıcı değildir.',
    ],
    [
        'question' => 'Seans ücretleri nedir?',
        'answer'   => 'Seans ücretleri hizmet türüne göre değişmektedir. Detaylı bilgi için lütfen iletişime geçin. İlk değerlendirme seansı ücretsizdir.',
    ],
    [
        'question' => 'Kaç seans gerekir?',
        'answer'   => 'Seans sayısı bireyin ihtiyacına ve hedeflerine göre değişir. Ortalama 8–20 seans arasında değişen süreçler yaygındır.',
    ],
    [
        'question' => 'Bilgilerim gizli tutulur mu?',
        'answer'   => 'Evet. Terapi sürecinde paylaşılan tüm bilgiler etik kurallar çerçevesinde kesinlikle gizli tutulur.',
    ],
];
?>

<!-- Hero -->
<section class="services__hero">
    <div class="container services__hero-container">
        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <span class="category__button">Hizmetler</span>
        <h1>Size Nasıl Yardımcı Olabilirim?</h1>
        <p>
            Her bireyin ihtiyacı farklıdır. Aşağıdaki hizmetleri inceleyebilir,
            sorularınız için benimle iletişime geçebilirsiniz.
            İlk görüşme ücretsizdir.
        </p>
    </div>
</section>

<!-- Hizmet Kartları -->
<section class="services__list">
    <div class="container services__list-container">
        <?php if ($services && mysqli_num_rows($services) > 0):
            while ($s = mysqli_fetch_assoc($services)): ?>
        <article class="hizmet-karti">
            <?php if (!empty($s['image'])): ?>
            <div class="hizmet-karti__media">
                <img src="<?= getImageUrl($s['image'], 'thumb') ?>"
                     alt="<?= e($s['title']) ?>"
                     width="400" height="225" loading="lazy">
            </div>
            <?php endif; ?>
            <div class="hizmet-karti__ikon" aria-hidden="true">
                <i class="<?= e($s['icon_class'] ?? 'uil uil-heart') ?>"></i>
            </div>
            <h2><?= e($s['title']) ?></h2>
            <p><?= e($s['summary'] ?? '') ?></p>
            <div class="service__card-actions">
                <a href="<?= ROOT_URL ?>hizmetler/<?= urlencode($s['slug']) ?>"
                   class="btn btn--outline">
                    Detaylı İncele
                </a>
                <a href="<?= ROOT_URL ?>randevu" class="btn">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevu Al
                </a>
            </div>
            <a href="<?= ROOT_URL ?>hizmetler/<?= urlencode($s['slug']) ?>"
               class="hizmet-karti__link">
                Detaylı İncele
                <i class="uil uil-arrow-right" aria-hidden="true"></i>
            </a>
        </article>
        <?php endwhile;
        else: ?>
        <!-- Statik fallback -->
        <?php foreach ([
            ['slug' => 'anksiyete-terapisi', 'icon' => 'uil uil-heart-rate', 'title' => 'Anksiyete Terapisi',  'summary' => 'Kaygı bozuklukları ve panik atak için bilimsel temelli bilişsel davranışçı terapi.'],
            ['slug' => 'depresyon-terapisi',  'icon' => 'uil uil-cloud-sad',  'title' => 'Depresyon Terapisi',  'summary' => 'Depresyon tedavisinde kanıta dayalı terapi yöntemleriyle bireysel destek.'],
            ['slug' => 'okb-terapisi',        'icon' => 'uil uil-redo',       'title' => 'OKB Terapisi',        'summary' => 'Obsesif kompulsif bozukluk için ERP ve bilişsel davranışçı terapi yöntemleri.'],
            ['slug' => 'travma-terapisi',     'icon' => 'uil uil-shield',     'title' => 'Travma Terapisi',     'summary' => 'EMDR ve travma odaklı terapi yöntemleriyle geçmiş yaraların iyileştirilmesi.'],
            ['slug' => 'iliskisel-sorunlar',  'icon' => 'uil uil-users-alt',  'title' => 'İlişkisel Sorunlar',  'summary' => 'Çift terapisi ve bireysel ilişki danışmanlığı ile sağlıklı iletişim becerileri.'],
            ['slug' => 'online-terapi',       'icon' => 'uil uil-laptop',     'title' => 'Online Terapi',       'summary' => 'Türkiye\'nin her yerinden güvenli ve şifreli görüntülü görüşme ile online seans.'],
        ] as $s): ?>
        <article class="hizmet-karti">
            <div class="hizmet-karti__ikon" aria-hidden="true">
                <i class="<?= $s['icon'] ?>"></i>
            </div>
            <h2><?= $s['title'] ?></h2>
            <p><?= $s['summary'] ?></p>
            <div class="service__card-actions">
                <a href="<?= ROOT_URL ?>hizmetler/<?= $s['slug'] ?>" class="btn btn--outline">Detaylı İncele</a>
                <a href="<?= ROOT_URL ?>randevu" class="btn">Randevu Al</a>
            </div>
            <a href="<?= ROOT_URL ?>hizmetler/<?= $s['slug'] ?>" class="hizmet-karti__link">
                Detaylı İncele <i class="uil uil-arrow-right" aria-hidden="true"></i>
            </a>
        </article>
        <?php endforeach; endif; ?>
    </div>
</section>

<!-- SSS -->
<section class="services__faq">
    <div class="container services__faq-container">
        <h2>Sık Sorulan Sorular</h2>
        <div class="faq__list">
            <?php foreach ($faqItems as $item): ?>
            <div class="faq__item">
                <button class="faq__question" aria-expanded="false">
                    <?= e($item['question']) ?>
                    <i class="uil uil-angle-down" aria-hidden="true"></i>
                </button>
                <div class="faq__answer" hidden>
                    <p><?= e($item['answer']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:var(--space-5);">
            <a href="<?= ROOT_URL ?>sss" class="btn btn--outline btn--sm">
                Tüm Sorular <i class="uil uil-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="services__cta">
    <div class="container services__cta-container">
        <h2>Bir Adım Atmaya Hazır Mısınız?</h2>
        <p>Kendinize yatırım yapmanın en iyi zamanı şimdi. Ücretsiz ilk görüşme için bugün iletişime geçin.</p>
        <div class="services__cta-btns" style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap;">
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
