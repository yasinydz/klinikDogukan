<?php
/**
 * public/index.php
 *
 * Front controller — tüm istekler buraya gelir.
 * Hata gösterimi config/app.php içindeki APP_DEBUG flag'ine göre yönetilir.
 * URL'yi parse eder, doğru template'i yükler.
 */

// ── Bootstrap ─────────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/seo.php';
require_once BASE_PATH . '/app/helpers/slug.php';
require_once BASE_PATH . '/app/helpers/paginator.php';
require_once BASE_PATH . '/app/helpers/rate_limiter.php';
require_once BASE_PATH . '/app/helpers/validator.php';
require_once BASE_PATH . '/app/helpers/image.php';
require_once BASE_PATH . '/app/helpers/text.php';
require_once BASE_PATH . '/app/helpers/settings.php';
require_once BASE_PATH . '/app/middleware/auth.php';

// ── URL Parse ─────────────────────────────────────────────────
$rawUrl   = $_GET['url'] ?? '';
$url      = trim(sanitizeText($rawUrl), '/');
$segments = $url !== '' ? explode('/', $url) : [];

$segment0 = $segments[0] ?? '';
$segment1 = $segments[1] ?? '';
$segment2 = $segments[2] ?? '';

// ── 301 Redirect Kontrolü ─────────────────────────────────────
// redirects tablosunda eşleşen aktif redirect varsa yönlendir.
// Slug değişikliğinde eski URL'lerin 404 vermesini engeller.
if ($url !== '' && $segment0 !== 'admin') {
    $redirectStmt = $connection->prepare(
        "SELECT to_url, redirect_type FROM redirects WHERE from_url = ? AND is_active = 1 LIMIT 1"
    );
    if ($redirectStmt) {
        $fromUrl = '/' . $url;
        $redirectStmt->bind_param('s', $fromUrl);
        $redirectStmt->execute();
        $redirectRow = $redirectStmt->get_result()->fetch_assoc();
        $redirectStmt->close();

        if ($redirectRow) {
            $code = $redirectRow['redirect_type'] === '302' ? 302 : 301;
            $dest = $redirectRow['to_url'];
            // Relative URL ise SITE_URL ekle
            if (str_starts_with($dest, '/')) {
                $dest = SITE_URL . $dest;
            }
            header('Location: ' . $dest, true, $code);
            exit;
        }
    }
}

// ── Admin Route Koruması ──────────────────────────────────────
// Admin rotaları — /admin ile başlayan her istek
if ($segment0 === 'admin') {
    require_once BASE_PATH . '/routes/admin.php';
    exit;
}

// ── Public Route Yükleyici ────────────────────────────────────
require_once BASE_PATH . '/routes/web.php';
