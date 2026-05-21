<?php
/**
 * templates/admin/pages/categories/create-submit.php
 * POST /admin/categories/create
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/slug.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/categories/create');
    exit;
}

csrfVerify();

$title       = sanitizeText($_POST['title']       ?? '', 100);
$description = sanitizeText($_POST['description'] ?? '');
$metaDesc    = sanitizeText($_POST['meta_desc']   ?? '', 160);
$isNoindex   = isset($_POST['is_noindex']) ? 1 : 0;

$_SESSION['old_input'] = compact('title', 'description', 'meta_desc', 'is_noindex');

if ($title === '') {
    flashSet('error', 'Kategori başlığı zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/categories/create');
    exit;
}

if ($description === '') {
    flashSet('error', 'Açıklama zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/categories/create');
    exit;
}

$slug = generateUniqueSlug($connection, $title, 'post_categories');

$stmt = $connection->prepare("
    INSERT INTO post_categories (title, slug, description, meta_desc, is_noindex)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param('ssssi', $title, $slug, $description, $metaDesc, $isNoindex);

if (!$stmt->execute()) {
    flashSet('error', 'Kategori eklenemedi.');
    header('Location: ' . ROOT_URL . 'admin/categories/create');
    exit;
}

logAudit('create', 'post_categories', (int)$connection->insert_id, null, ['title' => $title]);

unset($_SESSION['old_input']);
flashSet('success', '"' . $title . '" kategorisi eklendi.');
header('Location: ' . ROOT_URL . 'admin/categories');
exit;
