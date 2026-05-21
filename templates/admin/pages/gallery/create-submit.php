<?php
/**
 * templates/admin/pages/gallery/create-submit.php
 * POST /admin/gallery/create
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

csrfVerify();

$title        = sanitizeText($_POST['title'] ?? '', 200);
$altText      = sanitizeText($_POST['alt_text'] ?? '', 300);
$category     = sanitizeText($_POST['category'] ?? 'genel', 50);
$displayOrder = max(0, (int)($_POST['display_order'] ?? 0));
$isFeatured   = isset($_POST['is_featured']) ? 1 : 0;
$isActive     = isset($_POST['is_active']) ? 1 : 0;

$file = $_FILES['image'] ?? null;

if (!$file || empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
    flashSet('error', 'Lütfen bir görsel seçin.');
    header('Location: ' . ROOT_URL . 'admin/gallery/create');
    exit;
}

$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($ext, ['jpg', 'jpeg', 'png'], true) || !in_array($mime, ['image/jpeg', 'image/png'], true)) {
    flashSet('error', 'Görsel formatı JPG veya PNG olmalıdır.');
    header('Location: ' . ROOT_URL . 'admin/gallery/create');
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) {
    flashSet('error', 'Görsel boyutu en fazla 2 MB olabilir.');
    header('Location: ' . ROOT_URL . 'admin/gallery/create');
    exit;
}

$filename  = time() . '_gallery_' . sanitizeFilename($file['name']);
$uploadDir = PUBLIC_PATH . '/images/uploads';

$imgResult = processUploadedImage(
    $file['tmp_name'],
    $uploadDir,
    $filename,
    ['maxWidth' => 1600, 'thumbW' => 400, 'thumbH' => 300, 'quality' => 85, 'makeWebp' => true]
);

if ($imgResult === false) {
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
        flashSet('error', 'Görsel yüklenemedi.');
        header('Location: ' . ROOT_URL . 'admin/gallery/create');
        exit;
    }
}

$stmt = $connection->prepare("
    INSERT INTO gallery (title, alt_text, filename, category, display_order, is_featured, is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('ssssiii', $title, $altText, $filename, $category, $displayOrder, $isFeatured, $isActive);

if (!$stmt->execute()) {
    flashSet('error', 'Görsel kaydedilemedi.');
    header('Location: ' . ROOT_URL . 'admin/gallery/create');
    exit;
}

logAudit('create', 'gallery', (int)$connection->insert_id, null, ['title' => $title]);
flashSet('success', 'Görsel başarıyla eklendi.');
header('Location: ' . ROOT_URL . 'admin/gallery');
exit;
