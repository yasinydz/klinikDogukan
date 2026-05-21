<?php
/**
 * routes/web.php
 *
 * Public sayfa route tanımları.
 * public/index.php tarafından çağrılır.
 *
 * $segment0, $segment1, $segment2 değişkenleri index.php'den gelir.
 */

$pagesPath = BASE_PATH . '/templates/pages';

// Şehir sayfası whitelist
$cityPages = ['izmit', 'kocaeli', 'gebze'];

// ── Route Eşleme ──────────────────────────────────────────────

switch (true) {

    // Ana Sayfa — GET /
    case $url === '':
        require $pagesPath . '/home.php';
        break;

    // Hakkımda — GET /hakkimda
    case $url === 'hakkimda':
        require $pagesPath . '/about.php';
        break;

    // Hizmetler Listesi — GET /hizmetler
    case $url === 'hizmetler':
        require $pagesPath . '/services.php';
        break;

    // Hizmet Detay — GET /hizmetler/{slug}
    case $segment0 === 'hizmetler' && $segment1 !== '':
        $_GET['slug'] = $segment1;
        require $pagesPath . '/service-detail.php';
        break;

    // Blog Listesi — GET /blog
    case $url === 'blog':
        require $pagesPath . '/blog.php';
        break;

    // Blog Yazısı — GET /blog/{slug}
    case $segment0 === 'blog' && $segment1 !== '':
        $_GET['slug'] = $segment1;
        require $pagesPath . '/post.php';
        break;

    // Kategori Arşivi — GET /kategori/{slug}
    case $segment0 === 'kategori' && $segment1 !== '':
        $_GET['slug'] = $segment1;
        require $pagesPath . '/category.php';
        break;

    // Arama — GET /arama
    case $url === 'arama':
        require $pagesPath . '/search.php';
        break;

    // İletişim — GET /iletisim
    case $url === 'iletisim':
        require $pagesPath . '/contact.php';
        break;

    // İletişim Form Submit — POST /iletisim/gonder
    case $url === 'iletisim/gonder' && $_SERVER['REQUEST_METHOD'] === 'POST':
        require $pagesPath . '/contact-submit.php';
        break;

    // Randevu — GET /randevu
    case $url === 'randevu':
        require $pagesPath . '/appointment.php';
        break;

    // Randevu Teşekkür — GET /randevu/tesekkur
    case $url === 'randevu/tesekkur':
        require $pagesPath . '/appointment-thanks.php';
        break;

    // Randevu Submit — POST /randevu/al
    case $url === 'randevu/al' && $_SERVER['REQUEST_METHOD'] === 'POST':
        require $pagesPath . '/appointment-submit.php';
        break;

    // Randevu Slot API — GET /randevu/slotlar
    case $url === 'randevu/slotlar':
        require $pagesPath . '/appointment-slots-api.php';
        break;

    // SSS — GET /sss
    case $url === 'sss':
        require $pagesPath . '/faq.php';
        break;

    // Şehir Landing Pages — GET /izmit-psikolog, /kocaeli-psikolog, /gebze-psikolog
    case in_array(str_replace('-psikolog', '', $url), $cityPages, true) && str_ends_with($url, '-psikolog'):
        $_GET['city'] = str_replace('-psikolog', '', $url);
        require $pagesPath . '/city.php';
        break;

    // ── Hukuki Sayfalar ───────────────────────────────────────

    case $url === 'gizlilik-politikasi':
        require $pagesPath . '/legal/privacy-policy.php';
        break;

    case $url === 'kvkk-aydinlatma':
        require $pagesPath . '/legal/kvkk.php';
        break;

    case $url === 'cerez-politikasi':
        require $pagesPath . '/legal/cookie-policy.php';
        break;

    case $url === 'veri-sahibi-basvuru':
        require $pagesPath . '/legal/data-subject-rights.php';
        break;

    // ── Sitemap & Robots ──────────────────────────────────────

    case $url === 'sitemap.xml':
        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');
        require $pagesPath . '/sitemap.php';
        break;

    // Offline sayfası (PWA)
    case $url === 'offline':
        require $pagesPath . '/offline.php';
        break;

    // ── 404 ───────────────────────────────────────────────────

    default:
        http_response_code(404);
        require $pagesPath . '/404.php';
        break;
}
