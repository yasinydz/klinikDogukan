<?php
/**
 * templates/admin/pages/categories/delete.php
 * POST /admin/categories/delete
 *
 * Güvenli silme: POST + CSRF zorunlu.
 */

require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    flashSet('error', 'Silme işlemi yalnızca form submit ile yapılabilir.');
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

csrfVerify();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz kategori.');
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

$stmt = $connection->prepare(
    "SELECT id, title FROM post_categories WHERE id = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Kategori bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

$category = $result->fetch_assoc();

// İçindeki yazıları kategorisiz yap (NULL — FK ON DELETE SET NULL)
$nullify = $connection->prepare(
    "UPDATE posts SET category_id = NULL WHERE category_id = ?"
);
$nullify->bind_param('i', $id);
$nullify->execute();

$del = $connection->prepare("DELETE FROM post_categories WHERE id = ? LIMIT 1");
$del->bind_param('i', $id);

if (!$del->execute()) {
    flashSet('error', 'Kategori silinemedi.');
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

logAudit('delete', 'post_categories', $id, ['title' => $category['title']]);
flashSet('success', '"' . $category['title'] . '" kategorisi silindi.');
header('Location: ' . ROOT_URL . 'admin/categories');
exit;
