<?php
/**
 * templates/pages/appointment-slots-api.php
 * GET /randevu/slotlar?date=YYYY-MM-DD
 *
 * JSON response: müsait saat slotları
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex');

$date = trim($_GET['date'] ?? '');

// Tarih formatı doğrulama
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode([]);
    exit;
}

$dateObj = DateTime::createFromFormat('Y-m-d', $date);
if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
    echo json_encode([]);
    exit;
}

// Geçmiş tarih kontrolü
if ($dateObj < new DateTime('today')) {
    echo json_encode([]);
    exit;
}

// Müsait slotları çek
$checkSlots = mysqli_query($connection, "SHOW TABLES LIKE 'appointment_slots'");
if (!$checkSlots || mysqli_num_rows($checkSlots) === 0) {
    echo json_encode([]);
    exit;
}

$stmt = $connection->prepare("
    SELECT slot_time
    FROM appointment_slots
    WHERE slot_date = ?
      AND is_available = 1
      AND is_blocked = 0
    ORDER BY slot_time ASC
");
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = mysqli_fetch_assoc($result)) {
    $slots[] = [
        'time' => date('H:i', strtotime($row['slot_time'])),
    ];
}

echo json_encode($slots, JSON_UNESCAPED_UNICODE);
exit;
