<?php
/**
 * templates/admin/pages/gallery/edit-submit.php
 * POST /admin/gallery/edit
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

csrfVerify();

$id              = (int) ($_POST['id'] ?? 0);
$title           = sanitizeText($_POST['title'] ?? '', 200);
$altText         = sanitizeText($_POST['alt_text'] ?? '', 300);
$description     = sanitizeText($_POST['description'] ?? '', 1000);
$category        = sanitizeText($_POST['category'] ?? 'genel', 50);
$displayOrder    = max(0, (int)($_POST['display_order'] ?? 0));
$isFeatured      = isset($_POST['is_featured']) ? 1 : 0;
$isActive        = isset($_POST['is_active']) ? 1 : 0;
$currentFilename = trim($_POST['current_filename'] ?? '');

if ($id <= 0) {
    flashSet('error', 'Geçersiz görsel.');
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

// Yeni görsel yüklendi mi?
$newFilename = null;
$file = $_FILES['image'] ?? null;

if ($file && !empty($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
    $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (in_array($ext, ['jpg', 'jpeg', 'png'], true)
        && in_array($mime, ['image/jpeg', 'image/png'], true)
        && $file['size'] <= 2 * 1024 * 1024) {

        $newFilename = time() . '_gallery_' . sanitizeFilename($file['name']);
        $uploadDir   = PUBLIC_PATH . '/images/uploads';

        $imgResult = processUploadedImage(
            $file['tmp_name'], $uploadDir, $newFilename,
            ['maxWidth' => 1600, 'thumbW' => 400, 'thumbH' => 300, 'quality' => 85, 'makeWebp' => true]
        );

        if ($imgResult === false) {
            move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $newFilename);
        }
    } else {
        flashSet('error', 'Görsel formatı JPG/PNG, boyut max 2 MB olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/gallery/edit?id=' . $id);
        exit;
    }
}

// UPDATE
$sql    = "UPDATE gallery SET title=?, alt_text=?, description=?, category=?, display_order=?, is_featured=?, is_active=?";
$params = [$title, $altText, $description, $category, $displayOrder, $isFeatured, $isActive];
$types  = 'ssssiis';

if ($newFilename !== null) {
    $sql     .= ", filename=?";
    $params[] = $newFilename;
    $types   .= 's';
}

$sql     .= " WHERE id=? LIMIT 1";
$params[] = $id;
$types   .= 'i';

$stmt = $connection->prepare($sql);
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    flashSet('error', 'Görsel güncellenemedi.');
    header('Location: ' . ROOT_URL . 'admin/gallery/edit?id=' . $id);
    exit;
}

// Eski görseli temizle
if ($newFilename !== null && $currentFilename !== '') {
    deleteImageVariants($currentFilename);
}

logAudit('update', 'gallery', $id, null, ['title' => $title]);
flashSet('success', 'Görsel güncellendi.');
header('Location: ' . ROOT_URL . 'admin/gallery');
exit;
