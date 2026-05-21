<?php
/**
 * templates/admin/pages/posts/delete.php
 * POST /admin/posts/delete
 *
 * Güvenli silme: POST + CSRF zorunlu.
 * GET erişimde 405 döner.
 */

require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/csrf.php';

// GET erişimi engelle
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    flashSet('error', 'Silme işlemi yalnızca form submit ile yapılabilir.');
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

csrfVerify();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz yazı.');
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

$stmt = $connection->prepare(
    "SELECT id, title, thumbnail FROM posts WHERE id = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Yazı bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

$post = $result->fetch_assoc();

$del = $connection->prepare("DELETE FROM posts WHERE id = ? LIMIT 1");
$del->bind_param('i', $id);

if (!$del->execute()) {
    flashSet('error', 'Yazı silinemedi.');
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

// Görseli ve tüm varyantlarını sil
if (!empty($post['thumbnail'])) {
    deleteImageVariants($post['thumbnail']);
}

logAudit('delete', 'posts', $id, ['title' => $post['title']]);

flashSet('success', '"' . $post['title'] . '" yazısı silindi.');
header('Location: ' . ROOT_URL . 'admin/posts');
exit;
