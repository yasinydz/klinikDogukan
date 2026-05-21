<?php
/**
 * templates/admin/pages/appointments/update-status.php
 * POST /admin/appointments/update-status
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/appointments');
    exit;
}

csrfVerify();

$id         = (int) ($_POST['id'] ?? 0);
$status     = trim($_POST['status'] ?? '');
$adminNotes = trim($_POST['admin_notes'] ?? '');

$allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'];

if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
    flashSet('error', 'Geçersiz istek.');
    header('Location: ' . ROOT_URL . 'admin/appointments');
    exit;
}

// Mevcut durum + slot bilgisi
$oldStmt = $connection->prepare("
    SELECT id, status, slot_id, cancelled_at
    FROM appointments
    WHERE id = ?
    LIMIT 1
");

if (!$oldStmt) {
    flashSet('error', 'Sorgu hazırlanamadı.');
    header('Location: ' . ROOT_URL . 'admin/appointments');
    exit;
}

$oldStmt->bind_param('i', $id);
$oldStmt->execute();
$oldData = $oldStmt->get_result()->fetch_assoc();

if (!$oldData) {
    flashSet('error', 'Randevu bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/appointments');
    exit;
}

$oldStatus = $oldData['status'];
$slotId    = isset($oldData['slot_id']) ? (int) $oldData['slot_id'] : 0;

$connection->begin_transaction();

try {
    // cancelled'a geçince slotu geri aç
    if ($status === 'cancelled' && $oldStatus !== 'cancelled' && $slotId > 0) {
        $releaseStmt = $connection->prepare("
            UPDATE appointment_slots
            SET is_available = 1
            WHERE id = ?
              AND is_blocked = 0
        ");

        if (!$releaseStmt) {
            throw new RuntimeException('Release slot prepare failed: ' . $connection->error);
        }

        $releaseStmt->bind_param('i', $slotId);
        $releaseStmt->execute();
    }

    // cancelled'dan aktif statüye dönüyorsa slotu tekrar kapat
    if ($oldStatus === 'cancelled' && $status !== 'cancelled' && $slotId > 0) {
        $reserveStmt = $connection->prepare("
            UPDATE appointment_slots
            SET is_available = 0
            WHERE id = ?
              AND is_available = 1
              AND is_blocked = 0
        ");

        if (!$reserveStmt) {
            throw new RuntimeException('Reserve slot prepare failed: ' . $connection->error);
        }

        $reserveStmt->bind_param('i', $slotId);
        $reserveStmt->execute();

        if ($reserveStmt->affected_rows === 0) {
            throw new RuntimeException('Slot artık müsait değil, randevu tekrar aktif edilemedi.');
        }
    }

    $cancelledAtSql = 'cancelled_at = cancelled_at';
    if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
        $cancelledAtSql = 'cancelled_at = NOW()';
    } elseif ($oldStatus === 'cancelled' && $status !== 'cancelled') {
        $cancelledAtSql = 'cancelled_at = NULL';
    }

    $stmt = $connection->prepare("
        UPDATE appointments
        SET status = ?, admin_notes = ?, updated_at = NOW(), {$cancelledAtSql}
        WHERE id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new RuntimeException('Update prepare failed: ' . $connection->error);
    }

    $stmt->bind_param('ssi', $status, $adminNotes, $id);

    if (!$stmt->execute()) {
        throw new RuntimeException('Güncelleme başarısız: ' . $stmt->error);
    }

    $connection->commit();

} catch (Throwable $e) {
    $connection->rollback();
    flashSet('error', $e->getMessage());
    header('Location: ' . ROOT_URL . 'admin/appointments/view?id=' . $id);
    exit;
}

logAudit(
    'update',
    'appointments',
    $id,
    [
        'status'       => $oldStatus,
        'admin_notes'  => $oldData['admin_notes'] ?? '',
        'slot_id'      => $slotId,
    ],
    [
        'status'       => $status,
        'admin_notes'  => $adminNotes,
        'slot_id'      => $slotId,
    ]
);

flashSet('success', 'Randevu durumu güncellendi.');
header('Location: ' . ROOT_URL . 'admin/appointments/view?id=' . $id);
exit;