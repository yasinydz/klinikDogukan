<?php
/**
 * templates/admin/pages/gallery/delete.php
 * POST /admin/gallery/delete
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

csrfVerify();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz görsel.');
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

$stmt = $connection->prepare("SELECT id, filename, title FROM gallery WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Görsel bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

$img = $result->fetch_assoc();

$del = $connection->prepare("DELETE FROM gallery WHERE id = ? LIMIT 1");
$del->bind_param('i', $id);

if (!$del->execute()) {
    flashSet('error', 'Görsel silinemedi.');
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

// Görsel dosyalarını temizle
if (!empty($img['filename'])) {
    deleteImageVariants($img['filename']);
}

logAudit('delete', 'gallery', $id, ['title' => $img['title']]);
flashSet('success', 'Görsel silindi.');
header('Location: ' . ROOT_URL . 'admin/gallery');
exit;
