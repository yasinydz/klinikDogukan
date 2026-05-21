<?php
/**
 * templates/admin/pages/slots/delete.php
 * POST /admin/slots/delete
 *
 * Güvenli silme: POST + CSRF zorunlu.
 */

require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    flashSet('error', 'Silme işlemi yalnızca form submit ile yapılabilir.');
    header('Location: ' . ROOT_URL . 'admin/slots');
    exit;
}

csrfVerify();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz slot.');
    header('Location: ' . ROOT_URL . 'admin/slots');
    exit;
}

// Rezerve edilmiş slot silinemez
$check = $connection->prepare("
    SELECT COUNT(*) AS c FROM appointments
    WHERE preferred_date = (SELECT slot_date FROM appointment_slots WHERE id = ?)
      AND preferred_time = (SELECT slot_time FROM appointment_slots WHERE id = ?)
      AND status NOT IN ('cancelled')
");
$check->bind_param('ii', $id, $id);
$check->execute();
$bookingCount = (int) $check->get_result()->fetch_assoc()['c'];

if ($bookingCount > 0) {
    flashSet('error', 'Bu slotta aktif randevu bulunuyor. Önce randevuyu iptal edin.');
    header('Location: ' . ROOT_URL . 'admin/slots');
    exit;
}

$del = $connection->prepare("DELETE FROM appointment_slots WHERE id = ? LIMIT 1");
$del->bind_param('i', $id);

if (!$del->execute()) {
    flashSet('error', 'Slot silinemedi.');
} else {
    flashSet('success', 'Slot silindi.');
}

header('Location: ' . ROOT_URL . 'admin/slots');
exit;
