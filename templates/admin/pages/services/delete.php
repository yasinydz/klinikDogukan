<?php
/**
 * templates/admin/pages/services/delete.php
 * POST /admin/services/delete
 *
 * Güvenli silme: POST + CSRF zorunlu.
 */

require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    flashSet('error', 'Silme işlemi yalnızca form submit ile yapılabilir.');
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

csrfVerify();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz hizmet.');
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

$stmt = $connection->prepare(
    "SELECT id, title, image FROM services WHERE id = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Hizmet bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

$service = $result->fetch_assoc();

$del = $connection->prepare("DELETE FROM services WHERE id = ? LIMIT 1");
$del->bind_param('i', $id);

if (!$del->execute()) {
    flashSet('error', 'Hizmet silinemedi.');
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

// Hizmet görselini ve varyantlarını sil
if (!empty($service['image'])) {
    deleteImageVariants($service['image']);
}

logAudit('delete', 'services', $id, ['title' => $service['title']]);
flashSet('success', '"' . $service['title'] . '" hizmeti silindi.');
header('Location: ' . ROOT_URL . 'admin/services');
exit;
