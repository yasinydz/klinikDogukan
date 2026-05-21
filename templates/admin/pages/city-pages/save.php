<?php
/**
 * templates/admin/pages/city-pages/save.php
 * POST /admin/city-pages
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/city-pages');
    exit;
}

csrfVerify();

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
    flashSet('error', 'Güncelleme başarısız: ' . $stmt->error);
}

header('Location: ' . ROOT_URL . 'admin/city-pages');
exit;
