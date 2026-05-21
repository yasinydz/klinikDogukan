<?php
/**
 * templates/pages/home.php
 * URL: /
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/public_profile.php';
require_once BASE_PATH . '/app/middleware/auth.php';

// ── SEO ───────────────────────────────────────────────────────
$seo_title       = 'Klinik Psikolog Doğukan Kopuk — İzmit · Kocaeli · Online Terapi';
$seo_description = 'Klinik Psikolog Doğukan Kopuk ile anksiyete, depresyon, travma ve ilişki sorunlarında profesyonel destek. İzmit\'te yüz yüze, Türkiye geneli online seans.';
$seo_canonical   = SITE_URL;

$seo_schemas = [
    schemaLocalBusiness(),
    schemaPerson(),
];

// Öne çıkan yazı
$featuredPost = null;
$featured = mysqli_query($connection, "
    SELECT p.title, p.slug, p.body, p.thumbnail, p.views, p.published_at,
           c.title AS category_title, c.slug AS category_slug,
           u.first_name, u.last_name
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    LEFT JOIN admins u ON p.author_id = u.id
    WHERE p.is_featured = 1 AND p.is_published = 1
    LIMIT 1
");
if ($featured && mysqli_num_rows($featured) > 0) {
    $featuredPost = mysqli_fetch_assoc($featured);
}

// Son yazılar
$recentPosts = mysqli_query($connection, "
    SELECT p.title, p.slug, p.body, p.thumbnail, p.views, p.published_at,
           c.title AS category_title, c.slug AS category_slug,
           u.first_name, u.last_name
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    LEFT JOIN admins u ON p.author_id = u.id
    WHERE p.is_published = 1 AND p.is_featured = 0
    ORDER BY p.published_at DESC
    LIMIT 3
");

// Aktif hizmetler
$services = mysqli_query($connection, "
    SELECT slug, title, summary, icon_class, image
    FROM services
    WHERE is_active = 1
    ORDER BY display_order ASC
    LIMIT 6
");

// SSS (ana sayfa için ilk 4)
$faqItems = [
    [
        'question' => 'Terapiye nasıl başlarım?',
        'answer'   => 'Randevu formu, telefon veya WhatsApp üzerinden ulaşmanız yeterlidir.',
    ],
    [
        'question' => 'Online terapi yüz yüze kadar etkili mi?',
        'answer'   => 'Araştırmalar, online terapinin anksiyete ve depresyon gibi pek çok sorun için yüz yüze terapi kadar etkili olduğunu göstermektedir.',
    ],
    [
        'question' => 'Kaç seans gerekir?',
        'answer'   => 'Seans sayısı kişiden kişiye değişir. Ortalama 8–20 seans arasındadır. Ön görüşmede daha net bir plan oluşturulur.',
    ],
    [
        'question' => 'Bilgilerim gizli tutulur mu?',
        'answer'   => 'Evet. Terapi sürecinde paylaşılan tüm bilgiler etik kurallar ve yasal düzenlemeler çerçevesinde kesinlikle gizli tutulur.',
    ],
];

$seo_schemas[] = schemaFaqPage($faqItems);

require_once BASE_PATH . '/templates/partials/header.php';
?>

<!-- ── HERO ──────────────────────── -->
<section class="hero">
    <div class="container hero__container">

        <div class="hero__text">
            <p class="hero__greeting">Merhaba, ben <?= e(PSYCHOLOGIST_NAME) ?></p>
            <h1>
                Kendinize İyi Gelmek İçin<br>
                <span class="text-accent">Güvenli Bir Alan</span>
            </h1>
            <p class="hero__desc">
                Anksiyete, depresyon, travma ve ilişki sorunlarında
                bilimsel temelli, bireysel terapi.
                İzmit'te yüz yüze veya Türkiye'nin her yerinden online seans.
            </p>

            <div class="hero__cta">
                <a href="<?= ROOT_URL ?>randevu" class="btn btn--lg">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevu Talebi Oluştur
                </a>
                <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode('Merhaba, bilgi almak istiyorum.') ?>"
                   class="btn btn--lg btn--outline"
                   target="_blank" rel="noopener noreferrer">
                    <i class="uil uil-whatsapp" aria-hidden="true"></i>
                    WhatsApp
                </a>
            </div>

            <div class="hero__badges">
                <!--<span class="hero__badge">-->
                <!--    <i class="uil uil-check-circle" aria-hidden="true"></i>-->
                <!--    Randevu-->
                <!--</span>-->
                <span class="hero__badge">
                    <i class="uil uil-video" aria-hidden="true"></i>
                    Online seans mevcut
                </span>
                <span class="hero__badge">
                    <i class="uil uil-lock" aria-hidden="true"></i>
                    Gizlilik güvencesi
                </span>
            </div>
        </div>

        <div class="hero__visual">
            <div class="hero__image-frame">
                <img src="<?= e(publicAvatarUrl()) ?>"
                     alt="Klinik Psikolog Doğukan Kopuk — İzmit"
                     width="380" height="460"
                     loading="eager">
                <div class="hero__image-accent" aria-hidden="true"></div>
            </div>
            <div class="hero__credential">
                <strong>Klinik Psikolog</strong>
                <span><?= e(PSYCHOLOGIST_NAME) ?></span>
                <!-- <span class="hero__credential-detail">BDT Uzmanı · 8+ Yıl Deneyim</span> -->
            </div>
        </div>

    </div>
    <div class="hero__leaf" aria-hidden="true"></div>
</section>

<!-- ── TRUST BAR — Kaynak: psikologtypethree ────────────── -->
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

<!-- ── PROBLEM BLOCK ────────────────────────────────────────── -->
<section class="problems" aria-label="Yardımcı olunan konular">
    <div class="container problems__container">
        <div class="problems__text">
            <h2>Şu An Bunları Yaşıyor Olabilirsiniz</h2>
            <p>Aşağıdaki durumlardan herhangi biri size tanıdık geliyorsa, yalnız değilsiniz.</p>
            <ul class="problems__list">
                <li><i class="uil uil-heart-rate" aria-hidden="true"></i> Sürekli kaygı ve panik ataklar</li>
                <li><i class="uil uil-cloud-sad" aria-hidden="true"></i> Motivasyon ve enerji kaybı, depresif ruh hali</li>
                <li><i class="uil uil-redo" aria-hidden="true"></i> Tekrarlayan düşünceler ve takıntılar (OKB)</li>
                <li><i class="uil uil-shield" aria-hidden="true"></i> Geçmiş travmalar ve işlenemeyen anılar</li>
                <li><i class="uil uil-users-alt" aria-hidden="true"></i> İlişki sorunları ve iletişim güçlükleri</li>
                <li><i class="uil uil-moon" aria-hidden="true"></i> Uyku sorunları ve kronik stres</li>
            </ul>
            <a href="<?= ROOT_URL ?>randevu" class="btn">
                Destek Almak İstiyorum
            </a>
        </div>
        <div class="problems__image" aria-hidden="true">
            <img src="<?= e(publicAvatarUrl()) ?>"
                 alt="" width="480" height="380" loading="lazy">
        </div>
    </div>
</section>

<!-- ── HİZMETLER ────────────────────────────────────────────── -->
<section class="home-hizmetler">
    <div class="container">
        <div class="section__header">
            <span class="category__button">Hizmetler</span>
            <h2>Size Nasıl Yardımcı Olabilirim?</h2>
            <p class="text-muted">
                Her bireyin ihtiyacı farklıdır. Aşağıdaki hizmetleri inceleyebilir,
                sorularınız için benimle iletişime geçebilirsiniz.
            </p>
        </div>

        <div class="home-hizmetler__grid">
            <?php if ($services && mysqli_num_rows($services) > 0):
                while ($s = mysqli_fetch_assoc($services)): ?>
            <article class="hizmet-karti">
                <div class="hizmet-karti__ikon" aria-hidden="true">
                    <i class="<?= e($s['icon_class'] ?? 'uil uil-heart') ?>"></i>
                </div>
                <h3><?= e($s['title']) ?></h3>
                <p><?= e($s['summary'] ?? '') ?></p>
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
                ['slug' => 'anksiyete-terapisi',  'icon' => 'uil-heart-rate', 'title' => 'Anksiyete Terapisi',  'summary' => 'Kaygı bozuklukları ve panik atak için BDT tabanlı destek.'],
                ['slug' => 'depresyon-terapisi',   'icon' => 'uil-cloud-sad',  'title' => 'Depresyon Terapisi',  'summary' => 'Kanıta dayalı terapi yöntemleriyle bireysel destek.'],
                ['slug' => 'travma-terapisi',      'icon' => 'uil-shield',     'title' => 'Travma Terapisi',     'summary' => 'EMDR ve travma odaklı terapi yöntemleri.'],
                ['slug' => 'iliskisel-sorunlar',   'icon' => 'uil-users-alt',  'title' => 'İlişkisel Sorunlar',  'summary' => 'Çift terapisi ve ilişki danışmanlığı.'],
                ['slug' => 'okb-terapisi',         'icon' => 'uil-redo',       'title' => 'OKB Terapisi',        'summary' => 'ERP ve bilişsel davranışçı terapi yöntemleri.'],
                ['slug' => 'online-terapi',        'icon' => 'uil-laptop',     'title' => 'Online Terapi',       'summary' => 'Türkiye geneli güvenli online seans imkânı.'],
            ] as $s): ?>
            <article class="hizmet-karti">
                <div class="hizmet-karti__ikon" aria-hidden="true">
                    <i class="uil <?= $s['icon'] ?>"></i>
                </div>
                <h3><?= $s['title'] ?></h3>
                <p><?= $s['summary'] ?></p>
                <a href="<?= ROOT_URL ?>hizmetler/<?= $s['slug'] ?>" class="hizmet-karti__link">
                    Detaylı İncele <i class="uil uil-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            <?php endforeach; endif; ?>
        </div>

        <div class="text-center" style="margin-top:var(--space-7);">
            <a href="<?= ROOT_URL ?>hizmetler" class="btn btn--outline">
                Tüm Hizmetleri Gör
            </a>
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

<!-- ── SÜREÇ ─────────────────────────────────────────────────── -->
<section class="surec surec--bordered">
    <div class="container">
        <div class="section__header">
            <span class="category__button">Nasıl Çalışır?</span>
            <h2>Terapi Süreci</h2>
        </div>
        <ol class="surec__adimlar">
            <?php
            $steps = [
                ['num' => '1', 'title' => 'İletişime Geçin',   'desc' => 'Randevu formu, telefon veya WhatsApp ile ulaşarak ilk görüşmeyi planlayabilirsiniz.'],
                ['num' => '2', 'title' => 'Ön Görüşme',       'desc' => 'İhtiyaçlarınızı, beklentilerinizi ve hedeflerinizi birlikte değerlendiririz.'],
                ['num' => '3', 'title' => 'Kişisel Plan',      'desc' => 'Size özel, bilimsel temelli terapi planı oluşturulur.'],
                ['num' => '4', 'title' => 'İlerleme',          'desc' => 'Haftalık seanslarla hedeflerinize adım adım yaklaşırsınız.'],
            ];
            foreach ($steps as $step): ?>
            <li class="surec__adim">
                <div class="surec__sayi" aria-hidden="true"><?= $step['num'] ?></div>
                <div class="surec__icerik">
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['desc']) ?></p>
                </div>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- ── SOSYAL KANIT ─────────────────────────────────────────── -->
<?php
// Dinamik testimoniallar
$testimonials = mysqli_query($connection, "
    SELECT author_name, author_title, content, rating, is_google, google_url
    FROM testimonials
    WHERE is_active = 1
    ORDER BY is_featured DESC, display_order ASC
    LIMIT " . (int)siteSetting('testimonial_home_limit', '3') . "
");
$hasTestimonials = $testimonials && mysqli_num_rows($testimonials) > 0;

if ($hasTestimonials):
?>
<section class="yorumlar" aria-label="Danışan yorumları">
    <div class="container yorumlar__container">
        <div class="section__header">
            <h2>Danışanların Deneyimleri</h2>
        </div>
        <div class="yorumlar__grid">
            <?php while ($t = mysqli_fetch_assoc($testimonials)): ?>
            <blockquote class="yorum-karti">
                <div style="margin-bottom:var(--space-3);color:var(--color-warning);font-size:0.9rem;">
                    <?= str_repeat('★', (int)$t['rating']) . str_repeat('☆', 5 - (int)$t['rating']) ?>
                </div>
                <p>"<?= e($t['content']) ?>"</p>
                <footer>
                    <cite>— <?= e($t['author_name']) ?><?= $t['author_title'] ? ', ' . e($t['author_title']) : '' ?></cite>
                    <?php if ($t['is_google']): ?>
                    <span class="badge badge--primary" style="margin-left:var(--space-2);">Google</span>
                    <?php endif; ?>
                </footer>
            </blockquote>
            <?php endwhile; ?>
        </div>
        <?php
        $gbpReviewUrl = siteSetting('gbp_review_url', GBP_REVIEW_URL);
        if ($gbpReviewUrl !== ''):
        ?>
        <div class="text-center" style="margin-top:var(--space-6);">
            <a href="<?= e($gbpReviewUrl) ?>" class="btn btn--outline"
               target="_blank" rel="noopener noreferrer">
                <i class="uil uil-google" aria-hidden="true"></i>
                Google Yorumlarını İncele
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ── ÖNE ÇIKAN YAZI ────────────────────────────────────────── -->
<?php if ($featuredPost): ?>
<section class="featured">
    <div class="container">
        <div class="featured__container">
            <div class="post__thumbnail">
                <a href="<?= ROOT_URL ?>blog/<?= urlencode($featuredPost['slug']) ?>"
                   tabindex="-1" aria-hidden="true">
                    <img src="<?= getImageUrl($featuredPost['thumbnail'] ?? '') ?>"
                         alt="<?= e($featuredPost['title']) ?>"
                         width="600" height="400" loading="lazy">
                </a>
            </div>
            <div class="post__info">
                <?php if (!empty($featuredPost['category_slug'])): ?>
                <a href="<?= ROOT_URL ?>kategori/<?= urlencode($featuredPost['category_slug']) ?>"
                   class="category__button">
                    <?= e($featuredPost['category_title'] ?? 'Genel') ?>
                </a>
                <?php endif; ?>
                <h2 class="post__title">
                    <a href="<?= ROOT_URL ?>blog/<?= urlencode($featuredPost['slug']) ?>">
                        <?= e($featuredPost['title']) ?>
                    </a>
                </h2>
                <p class="post__body">
                    <?= e(mb_substr(strip_tags($featuredPost['body']), 0, 200, 'UTF-8')) ?>...
                </p>
                <div class="post__meta">
                    <span>
                        <i class="uil uil-clock" aria-hidden="true"></i>
                        <?= readingTime($featuredPost['body']) ?>
                    </span>
                    <span>
                        <i class="uil uil-eye" aria-hidden="true"></i>
                        <?= number_format((int)$featuredPost['views']) ?>
                    </span>
                </div>
                <a href="<?= ROOT_URL ?>blog/<?= urlencode($featuredPost['slug']) ?>"
                   class="btn btn--outline">
                    Yazıyı Oku
                    <i class="uil uil-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── SON YAZILAR ───────────────────────────────────────────── -->
<?php if ($recentPosts && mysqli_num_rows($recentPosts) > 0): ?>
<section class="blog-preview">
    <div class="container">
        <div class="section__header section__header--flex">
            <h2>Son Yazılar</h2>
            <a href="<?= ROOT_URL ?>blog" class="btn btn--outline btn--sm">Tüm Yazılar</a>
        </div>
        <div class="posts__container">
            <?php while ($post = mysqli_fetch_assoc($recentPosts)): ?>
            <article class="post">
                <div class="post__thumbnail">
                    <a href="<?= ROOT_URL ?>blog/<?= urlencode($post['slug']) ?>"
                       tabindex="-1" aria-hidden="true">
                        <img src="<?= getImageUrl($post['thumbnail'] ?? '') ?>"
                             alt="<?= e($post['title']) ?>"
                             width="400" height="225" loading="lazy">
                    </a>
                </div>
                <div class="post__info">
                    <?php if (!empty($post['category_slug'])): ?>
                    <a href="<?= ROOT_URL ?>kategori/<?= urlencode($post['category_slug']) ?>"
                       class="category__button">
                        <?= e($post['category_title'] ?? 'Genel') ?>
                    </a>
                    <?php endif; ?>
                    <h3 class="post__title">
                        <a href="<?= ROOT_URL ?>blog/<?= urlencode($post['slug']) ?>">
                            <?= e($post['title']) ?>
                        </a>
                    </h3>
                    <p class="post__body">
                        <?= e(mb_substr(strip_tags($post['body']), 0, 120, 'UTF-8')) ?>...
                    </p>
                    <div class="post__meta">
                        <span>
                            <i class="uil uil-clock" aria-hidden="true"></i>
                            <?= readingTime($post['body']) ?>
                        </span>
                        <span>
                            <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                            <?= turkceTarih($post['published_at'], 'd F Y') ?>
                        </span>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── GALERİ ÖNİZLEME ────────────────────────────────────────── -->
<?php
$showGalleryHome = siteSetting('gallery_show_home', '1') === '1';
$galleryLimit    = (int) siteSetting('gallery_home_limit', '6');
$galleryImages   = null;

if ($showGalleryHome) {
    $chkGallery = mysqli_query($connection, "SHOW TABLES LIKE 'gallery'");
    if ($chkGallery && mysqli_num_rows($chkGallery) > 0) {
        $galleryImages = mysqli_query($connection,
            "SELECT filename, title, alt_text FROM gallery
             WHERE is_active = 1
             ORDER BY is_featured DESC, display_order ASC
             LIMIT " . $galleryLimit
        );
    }
}

if ($galleryImages && mysqli_num_rows($galleryImages) > 0):
?>
<section class="home-galeri" id="galeri">
    <div class="container">
        <div class="section__header">
            <span class="category__button">Galeri</span>
            <h2>Çalışma Ortamımız</h2>
            <p class="text-muted">Güvenli, huzurlu ve profesyonel bir ortamda destek alın.</p>
        </div>
        <div class="home-galeri__grid">
            <?php while ($gi = mysqli_fetch_assoc($galleryImages)): ?>
            <a href="<?= ROOT_URL ?>images/uploads/<?= e($gi['filename']) ?>"
               class="home-galeri__item"
               data-lightbox="gallery"
               data-caption="<?= e($gi['title'] ?: $gi['alt_text']) ?>"
               aria-label="<?= e($gi['alt_text'] ?: ($gi['title'] ?: 'Galeri görseli')) ?>">
                <img src="<?= ROOT_URL ?>images/uploads/<?= e($gi['filename']) ?>"
                     alt="<?= e($gi['alt_text'] ?: ($gi['title'] ?: 'Psikolog ofisi İzmit')) ?>"
                     width="400" height="300" loading="lazy">
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── KONUM / ULAŞIM ──────────────────────────────────────── -->
<?php
$showMapsHome = siteSetting('maps_show_home', '1') === '1';
$mapsEmbed    = siteSetting('maps_embed_url', '');
$mapsDir      = siteSetting('maps_directions_url', '');
$transNote    = siteSetting('maps_transport_note', '');
$parkNote     = siteSetting('maps_parking_note', '');
$onlineNote   = siteSetting('maps_online_note', 'Türkiye genelinde online görüşme imkânı mevcuttur.');
$inpersonNote = siteSetting('maps_inperson_note', '');

if ($showMapsHome && $mapsEmbed !== ''):
?>
<section class="home-konum" id="konum">
    <div class="container">
        <div class="section__header">
            <span class="category__button">Konum</span>
            <h2>Ulaşım Bilgileri</h2>
        </div>
        <div class="home-konum__layout">
            <div class="home-konum__map">
                <iframe src="<?= e($mapsEmbed) ?>"
                        width="100%" height="350"
                        style="border:0;border-radius:var(--radius-lg);"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Psikolog Doğukan Kopuk — Konum"></iframe>
            </div>
            <div class="home-konum__info">
                <div class="home-konum__card">
                    <i class="uil uil-map-marker" aria-hidden="true"></i>
                    <div>
                        <strong>Adres</strong>
                        <p><?= e(siteSetting('address_full', ADDRESS_STREET . ', ' . ADDRESS_DISTRICT . '/' . ADDRESS_CITY)) ?></p>
                    </div>
                </div>
                <?php if ($inpersonNote !== ''): ?>
                <div class="home-konum__card">
                    <i class="uil uil-building" aria-hidden="true"></i>
                    <div>
                        <strong>Yüz Yüze Görüşme</strong>
                        <p><?= e($inpersonNote) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($onlineNote !== ''): ?>
                <div class="home-konum__card">
                    <i class="uil uil-video" aria-hidden="true"></i>
                    <div>
                        <strong>Online Görüşme</strong>
                        <p><?= e($onlineNote) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($transNote !== ''): ?>
                <div class="home-konum__card">
                    <i class="uil uil-bus" aria-hidden="true"></i>
                    <div>
                        <strong>Toplu Taşıma</strong>
                        <p><?= e($transNote) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($parkNote !== ''): ?>
                <div class="home-konum__card">
                    <i class="uil uil-parking-square" aria-hidden="true"></i>
                    <div>
                        <strong>Otopark</strong>
                        <p><?= e($parkNote) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($mapsDir !== ''): ?>
                <a href="<?= e($mapsDir) ?>" class="btn btn--outline" target="_blank" rel="noopener noreferrer">
                    <i class="uil uil-directions" aria-hidden="true"></i>
                    Yol Tarifi Al
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── SSS ───────────────────────────────────────────────────── -->
<section class="sss sss--bg">
    <div class="container container--narrow">
        <div class="section__header">
            <h2>Sık Sorulan Sorular</h2>
        </div>
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
        <div class="text-center" style="margin-top:var(--space-6);">
            <a href="<?= ROOT_URL ?>sss" class="btn btn--outline">Tüm Sorular</a>
        </div>
    </div>
</section>

<!-- ── CTA — Kaynak: psikologtypethree ──────────────────── -->
<section class="home-cta-v2">
    <div class="container">
        <div class="home-cta-v2__inner">
            <div class="home-cta-v2__text">
                <h2>Bir Adım Atmaya<br>Hazır Mısınız?</h2>
                <p>
                    Yüz yüze veya online psikoterapi seçeneğiyle
                    size en uygun şekilde destek sunuyorum.
                </p>
            </div>
            <div class="home-cta-v2__actions">
                <a href="<?= ROOT_URL ?>randevu" class="btn btn--lg">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevu Al
                </a>
                <a href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode('Merhaba, randevu almak istiyorum.') ?>"
                   class="btn btn--lg btn--outline"
                   target="_blank" rel="noopener noreferrer">
                    <i class="uil uil-whatsapp" aria-hidden="true"></i>
                    WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
