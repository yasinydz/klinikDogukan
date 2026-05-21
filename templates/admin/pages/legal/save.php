<?php
/**
 * templates/admin/pages/legal/save.php
 * POST /admin/legal  ve  POST /admin/city-pages
 *
 * İki farklı POST handler burada:
 * 1. Legal pages → page_key gelirse legal kaydeder
 * 2. City pages  → city_key gelirse şehir sayfasını günceller
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/legal');
    exit;
}

csrfVerify();

// ── City Pages POST ────────────────────────────────────────────
if (isset($_POST['city_key'])) {
    $cityKey   = sanitizeText($_POST['city_key']   ?? '', 50);
    $h1Text    = sanitizeText($_POST['h1_text']    ?? '', 200);
    $metaTitle = sanitizeText($_POST['meta_title'] ?? '', 70);
    $metaDesc  = sanitizeText($_POST['meta_desc']  ?? '', 160);
    $content   = sanitizeText($_POST['content']    ?? '');
    $isActive  = isset($_POST['is_active']) ? 1 : 0;

    $allowed = ['izmit', 'kocaeli', 'gebze'];
    if (!in_array($cityKey, $allowed, true)) {
        flashSet('error', 'Geçersiz şehir.');
        header('Location: ' . ROOT_URL . 'admin/city-pages');
        exit;
    }

    $stmt = $connection->prepare("
        UPDATE city_pages
        SET h1_text = ?, meta_title = ?, meta_desc = ?,
            content = ?, is_active = ?, updated_at = NOW()
        WHERE city_key = ?
    ");
    $stmt->bind_param('ssssis', $h1Text, $metaTitle, $metaDesc, $content, $isActive, $cityKey);

    if ($stmt->execute()) {
        logAudit('update', 'city_pages', 0, null, ['city_key' => $cityKey]);
        flashSet('success', ucfirst($cityKey) . ' sayfası güncellendi.');
    } else {
        flashSet('error', 'Güncelleme başarısız.');
    }

    header('Location: ' . ROOT_URL . 'admin/city-pages');
    exit;
}

// ── Legal Pages POST ───────────────────────────────────────────
$pageKey       = sanitizeText($_POST['page_key']       ?? '', 20);
$title         = sanitizeText($_POST['title']          ?? '', 200);
$version       = sanitizeText($_POST['version']        ?? '1.0', 20);
$effectiveDate = trim($_POST['effective_date']         ?? '');
$isActive      = isset($_POST['is_active']) ? 1 : 0;

$allowedKeys = ['kvkk', 'privacy', 'cookie', 'consent', 'commercial'];
if (!in_array($pageKey, $allowedKeys, true)) {
    flashSet('error', 'Geçersiz sayfa anahtarı.');
    header('Location: ' . ROOT_URL . 'admin/legal');
    exit;
}

// Tarih doğrulama
$dateVal = null;
if ($effectiveDate !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $effectiveDate);
    if ($d && $d->format('Y-m-d') === $effectiveDate) {
        $dateVal = $effectiveDate;
    }
}

$stmt = $connection->prepare("
    INSERT INTO legal_pages (page_key, title, content, version, is_active, effective_date)
    VALUES (?, ?, '', ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        version = VALUES(version),
        is_active = VALUES(is_active),
        effective_date = VALUES(effective_date),
        updated_at = NOW()
");
$stmt->bind_param('sssis',
    $pageKey, $title, $version, $isActive, $dateVal
);

if ($stmt->execute()) {
    logAudit('update', 'legal_pages', 0, null, ['page_key' => $pageKey, 'version' => $version]);
    flashSet('success', 'Hukuki metin bilgisi güncellendi.');
} else {
    flashSet('error', 'Kayıt başarısız.');
}

header('Location: ' . ROOT_URL . 'admin/legal');
exit;
