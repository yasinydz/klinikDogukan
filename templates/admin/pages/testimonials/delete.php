<?php
/**
 * templates/admin/pages/testimonials/delete.php
 * POST /admin/testimonials/delete
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ' . ROOT_URL . 'admin/testimonials');
    exit;
}

csrfVerify();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz yorum.');
    header('Location: ' . ROOT_URL . 'admin/testimonials');
    exit;
}

$stmt = $connection->prepare("SELECT id, author_name FROM testimonials WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) {
    flashSet('error', 'Yorum bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/testimonials');
    exit;
}

$del = $connection->prepare("DELETE FROM testimonials WHERE id = ? LIMIT 1");
$del->bind_param('i', $id);
$del->execute();

logAudit('delete', 'testimonials', $id, ['author' => $t['author_name']]);
flashSet('success', 'Yorum silindi.');
header('Location: ' . ROOT_URL . 'admin/testimonials');
exit;
