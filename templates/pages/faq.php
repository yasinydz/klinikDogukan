<?php
/**
 * templates/pages/faq.php
 * URL: /sss
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$seo_title       = 'Sık Sorulan Sorular | Psikolog Doğukan Kopuk';
$seo_description = 'Terapi süreci, seans ücretleri, online terapi ve randevu hakkında sık sorulan sorular. Psikolog Doğukan Kopuk ile ilgili merak ettikleriniz.';
$seo_canonical   = SITE_URL . '/sss';

$seo_breadcrumbs = [
    ['name' => 'Anasayfa', 'url' => SITE_URL],
    ['name' => 'SSS',      'url' => ''],
];

$faqSections = [
    [
        'category' => 'Terapi Süreci',
        'items'    => [
            ['question' => 'Terapiye nasıl başlarım?',
             'answer'   => 'İletişim formu, telefon veya WhatsApp üzerinden ulaşmanız yeterlidir. İlk görüşmede ihtiyaçlarınızı değerlendirip size uygun bir plan oluşturulur.'],
            ['question' => 'İlk seansta ne olur?',
             'answer'   => 'Neden destek almak istediğinizi, beklentilerinizi ve geçmiş deneyimlerinizi konuşuruz. Birlikte çalışıp çalışmayacağımıza karar veririz.'],
            ['question' => 'Kaç seans gerekir?',
             'answer'   => 'Her psikoterapi süreci, kişinin ihtiyaçlarına ve ele alınan konunun derinliğine göre farklılık gösterir. Bu sebeple standart bir süre ya da sabit bir seans planı sunmak yerine, sürecin kapsamı ve ritmi terapi içerisinde birlikte belirlenir.'],
            ['question' => 'Seans ne kadar sürer?',
             'answer'   => 'Bireysel seanslar genellikle 45-55 dakikadır.'],
            ['question' => 'Seanslar ne sıklıkta yapılır?',
             'answer'   => 'Genellikle haftada bir seans yapılır. Sürecin ilerlemesine göre sıklık azaltılabilir ya da artırılabilir.'],
        ],
    ],
    [
        'category' => 'Online Terapi',
        'items'    => [
            ['question' => 'Online terapi yüz yüze kadar etkili mi?',
             'answer'   => 'Araştırmalar, online terapinin anksiyete, depresyon ve pek çok psikolojik sorun için yüz yüze terapi kadar etkili olduğunu göstermektedir.'],
            ['question' => 'Online seans nasıl yapılır?',
             'answer'   => 'Seanslar Zoom veya Google Meet üzerinden gerçekleştirilir. Randevu saatinden önce bağlantı linki tarafınıza iletilir. Güvenli ve şifreli bağlantı üzerinden katılabilirsiniz.'],
            ['question' => 'Online terapi için ne gerekir?',
             'answer'   => 'Stabil bir internet bağlantısı, kamera ve mikrofonu olan bir cihaz ve özel bir ortam yeterlidir.'],
        ],
    ],
    [
        'category' => 'Gizlilik',
        'items'    => [
            ['question' => 'Bilgilerim gizli tutulacak mı?',
             'answer'   => 'Evet. Terapi sürecinde paylaşılan tüm bilgiler etik kurallar ve yasal düzenlemeler çerçevesinde kesinlikle gizli tutulur. Yasal zorunluluk olmadıkça hiçbir bilgi üçüncü taraflarla paylaşılmaz.'],
            ['question' => 'Aile üyelerim bilgi alabilir mi?',
             'answer'   => 'Hayır. Sizin açık izniz olmadan hiçbir kişiyle — aile üyeleri dahil — bilgi paylaşılmaz.'],
        ],
    ],
    [
        'category' => 'Ücret & Randevu',
        'items'    => [
            ['question' => 'Seans ücretleri nedir?',
             'answer'   => 'Seans ücretleri hizmet türüne göre değişmektedir. Detaylı bilgi için lütfen iletişime geçin. İlk değerlendirme seansı ücretsizdir.'],
            ['question' => 'Randevuyu iptal edebilir miyim?',
             'answer'   => 'Seans saatinden en az 24 saat önce bildirmeniz durumunda randevunuzu iptal edebilir veya erteleyebilirsiniz.'],
            ['question' => 'Sigorta kapsamında mı?',
             'answer'   => 'Bazı özel sağlık sigortaları psikolojik danışmanlık seanslarını karşılayabilir. Sigorta şirketinizle iletişime geçerek kapsam dahilinde olup olmadığını öğrenebilirsiniz.'],
        ],
    ],
];

// Tüm FAQ'ları schema için düzleştir
$allFaqItems = [];
foreach ($faqSections as $section) {
    foreach ($section['items'] as $item) {
        $allFaqItems[] = $item;
    }
}

$seo_schemas = [schemaFaqPage($allFaqItems), schemaLocalBusiness()];

require_once BASE_PATH . '/templates/partials/header.php';
?>

<!-- Hero -->
<section class="services__hero">
    <div class="container services__hero-container">
        <?php $breadcrumbs = $seo_breadcrumbs;
        require BASE_PATH . '/templates/partials/breadcrumb.php'; ?>

        <span class="category__button">SSS</span>
        <h1>Sık Sorulan Sorular</h1>
        <p>
            Terapi süreci, online seans, gizlilik ve randevu hakkında
            merak ettiklerinizi burada bulabilirsiniz.
            Cevabını bulamadığınız sorular için
            <a href="<?= ROOT_URL ?>iletisim">iletişime geçin</a>.
        </p>
    </div>
</section>

<!-- FAQ Bölümleri -->
<section class="sss-sayfa">
    <div class="container sss-sayfa__container">
        <?php foreach ($faqSections as $section): ?>
        <div class="faq-section">
            <h2 class="sss-kategori__baslik"><?= e($section['category']) ?></h2>
            <div class="faq__list">
                <?php foreach ($section['items'] as $item): ?>
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
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA -->
<section class="services__cta">
    <div class="container services__cta-container">
        <h2>Hâlâ Sorunuz Var mı?</h2>
        <p>Cevabını bulamadığınız sorularınız için doğrudan iletişime geçebilirsiniz.</p>
        <div class="services__cta-btns" style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap;">
            <a href="<?= ROOT_URL ?>randevu" class="btn">
                <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                Randevu Al
            </a>
            <a href="<?= ROOT_URL ?>iletisim" class="btn btn--outline">
                İletişime Geç
            </a>
        </div>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
