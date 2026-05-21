<?php
/**
 * templates/admin/pages/categories/edit-submit.php
 * POST /admin/categories/edit
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

csrfVerify();

$id          = (int)   ($_POST['id']          ?? 0);
$title       = sanitizeText($_POST['title']       ?? '', 100);
$description = sanitizeText($_POST['description'] ?? '');
$metaDesc    = sanitizeText($_POST['meta_desc']   ?? '', 160);
$isNoindex   = isset($_POST['is_noindex']) ? 1 : 0;

if ($id <= 0 || $title === '' || $description === '') {
    flashSet('error', 'Geçersiz form verisi.');
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

// Slug DEĞİŞMİYOR — SEO için sabit tutulur
$stmt = $connection->prepare("
    UPDATE post_categories
    SET title = ?, description = ?, meta_desc = ?, is_noindex = ?
    WHERE id = ? LIMIT 1
");
$stmt->bind_param('sssii', $title, $description, $metaDesc, $isNoindex, $id);

if (!$stmt->execute()) {
    flashSet('error', 'Kategori güncellenemedi.');
    header('Location: ' . ROOT_URL . 'admin/categories/edit?id=' . $id);
    exit;
}

logAudit('update', 'post_categories', $id, null, ['title' => $title]);
flashSet('success', '"' . $title . '" kategorisi güncellendi.');
header('Location: ' . ROOT_URL . 'admin/categories');
exit;
