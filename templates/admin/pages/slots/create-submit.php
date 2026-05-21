<?php
/**
 * templates/admin/pages/slots/create-submit.php
 * POST /admin/slots/create
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/slots');
    exit;
}

csrfVerify();

$isBulk = isset($_POST['bulk']) && $_POST['bulk'] === '1';

if ($isBulk) {
    // Toplu slot oluşturma
    $startDate       = trim($_POST['start_date']       ?? '');
    $endDate         = trim($_POST['end_date']         ?? '');
    $startTime       = trim($_POST['start_time']       ?? '');
    $endTime         = trim($_POST['end_time']         ?? '');
    $intervalMinutes = max(30, (int)($_POST['interval_minutes'] ?? 60));
    $days            = $_POST['days'] ?? [];

    if (!$startDate || !$endDate || !$startTime || !$endTime || empty($days)) {
        flashSet('error', 'Tüm alanları doldurun.');
        header('Location: ' . ROOT_URL . 'admin/slots');
        exit;
    }

    $allowedDays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    $days = array_filter($days, fn($d) => in_array($d, $allowedDays, true));

    $start  = new DateTime($startDate);
    $end    = new DateTime($endDate);
    $end->modify('+1 day');

    $created = 0;
    $skipped = 0;

    $stmt = $connection->prepare("
        INSERT IGNORE INTO appointment_slots (slot_date, slot_time, is_available)
        VALUES (?, ?, 1)
    ");

    $current = clone $start;
    while ($current < $end) {
        $dayOfWeek = $current->format('D'); // Mon, Tue, ...

        if (in_array($dayOfWeek, $days, true)) {
            // Günün slotlarını üret
            $slotTime = new DateTime($current->format('Y-m-d') . ' ' . $startTime);
            $slotEnd  = new DateTime($current->format('Y-m-d') . ' ' . $endTime);

            while ($slotTime < $slotEnd) {
                $dateStr = $current->format('Y-m-d');
                $timeStr = $slotTime->format('H:i:s');

                $stmt->bind_param('ss', $dateStr, $timeStr);
                if ($stmt->execute()) {
                    if ($connection->affected_rows > 0) {
                        $created++;
                    } else {
                        $skipped++;
                    }
                }

                $slotTime->modify("+{$intervalMinutes} minutes");
            }
        }

        $current->modify('+1 day');
    }

    flashSet('success', "{$created} slot oluşturuldu." . ($skipped > 0 ? " {$skipped} mevcut slot atlandı." : ''));

} else {
    // Tekil slot
    $slotDate = trim($_POST['slot_date'] ?? '');
    $slotTime = trim($_POST['slot_time'] ?? '');

    if (!$slotDate || !$slotTime) {
        flashSet('error', 'Tarih ve saat zorunludur.');
        header('Location: ' . ROOT_URL . 'admin/slots');
        exit;
    }

    $stmt = $connection->prepare("
        INSERT IGNORE INTO appointment_slots (slot_date, slot_time, is_available)
        VALUES (?, ?, 1)
    ");
    $stmt->bind_param('ss', $slotDate, $slotTime);

    if ($stmt->execute() && $connection->affected_rows > 0) {
        flashSet('success', 'Slot oluşturuldu.');
    } else {
        flashSet('error', 'Bu slot zaten mevcut.');
    }
}

header('Location: ' . ROOT_URL . 'admin/slots');
exit;
