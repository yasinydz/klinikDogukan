<?php
/**
 * templates/pages/appointment-submit.php
 * POST /randevu/al
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/validator.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/rate_limiter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'randevu');
    exit;
}

csrfVerify();
rateLimitCheck('appointment_form', RATE_LIMIT_APPOINTMENT, RATE_LIMIT_WINDOW);

$fullName          = sanitizeText($_POST['full_name'] ?? '', 100);
$phone             = sanitizePhone($_POST['phone'] ?? '');
$email             = trim($_POST['email'] ?? '');
$preferredDate     = trim($_POST['preferred_date'] ?? '');
$preferredTime     = trim($_POST['preferred_time'] ?? '');
$sessionType       = trim($_POST['session_type'] ?? '');
$privacyAccepted   = $_POST['privacy_notice_accepted'] ?? '';
$commercialConsent = isset($_POST['commercial_consent_given']) ? 1 : 0;

// Lead source detection
$leadSource = sanitizeText($_POST['lead_source'] ?? '', 30);
$allowedSources = ['organik', 'google_ads', 'instagram', 'whatsapp', 'maps', 'referral', 'direct'];

if ($leadSource === '' || !in_array($leadSource, $allowedSources, true)) {
    $utmSource = sanitizeText($_GET['utm_source'] ?? $_POST['utm_source'] ?? '', 30);
    $referer   = $_SERVER['HTTP_REFERER'] ?? '';

    if ($utmSource !== '') {
        $leadSource = in_array($utmSource, $allowedSources, true) ? $utmSource : 'direct';
    } elseif (str_contains($referer, 'google.com/maps') || str_contains($referer, 'maps.google')) {
        $leadSource = 'maps';
    } elseif (str_contains($referer, 'instagram.com')) {
        $leadSource = 'instagram';
    } elseif (str_contains($referer, 'google.com') || str_contains($referer, 'google.com.tr')) {
        $leadSource = 'organik';
    } else {
        $leadSource = 'direct';
    }
}

$v = new Validator($_POST);
$v->required('full_name', 'Ad Soyad')
  ->minLength('full_name', 2, 'Ad Soyad')
  ->required('phone', 'Telefon')
  ->phone('phone', 'Telefon')
  ->email('email', 'E-posta')
  ->required('preferred_date', 'Tarih')
  ->date('preferred_date', 'Tarih', true)
  ->required('preferred_time', 'Saat')
  ->time('preferred_time', 'Saat')
  ->required('session_type', 'Görüşme tipi')
  ->inList('session_type', ['in_person', 'online'], 'Görüşme tipi')
  ->accepted('privacy_notice_accepted', 'KVKK Aydınlatma Metni onayı');

$oldInput = [
    'full_name'      => $fullName,
    'phone'          => $phone,
    'email'          => $email,
    'preferred_date' => $preferredDate,
    'preferred_time' => $preferredTime,
    'session_type'   => $sessionType,
];

if ($v->fails()) {
    $_SESSION['old_input'] = $oldInput;
    flashSet('error', $v->firstError());
    header('Location: ' . ROOT_URL . 'randevu');
    exit;
}

$ip        = sanitizeIp($_SERVER['REMOTE_ADDR'] ?? '');
$userAgent = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
$emailVal  = $email !== '' ? $email : null;
$legalBasis = 'contract';

$connection->begin_transaction();

try {
    // Slot var mı ve müsait mi? FOR UPDATE ile kilitle
    $slotStmt = $connection->prepare("
        SELECT id
        FROM appointment_slots
        WHERE slot_date = ?
          AND slot_time = ?
          AND is_available = 1
          AND is_blocked = 0
        FOR UPDATE
    ");

    if (!$slotStmt) {
        throw new RuntimeException('Slot prepare failed: ' . $connection->error);
    }

    $slotStmt->bind_param('ss', $preferredDate, $preferredTime);
    $slotStmt->execute();
    $slotResult = $slotStmt->get_result();

    if ($slotResult->num_rows === 0) {
        $checkSlotsExist = $connection->query(
            "SELECT COUNT(*) AS c FROM appointment_slots WHERE slot_date >= CURDATE()"
        );

        $slotsActive = $checkSlotsExist
            ? ((int) $checkSlotsExist->fetch_assoc()['c'] > 0)
            : false;

        if ($slotsActive) {
            $connection->rollback();
            $_SESSION['old_input'] = $oldInput;
            flashSet('error', 'Seçtiğiniz tarih/saat artık müsait değil. Lütfen başka bir zaman seçin.');
            header('Location: ' . ROOT_URL . 'randevu');
            exit;
        }

        // Slot sistemi henüz kurulmamışsa serbest tarih/saat ile devam et
        $slotId = null;
    } else {
        $slot   = $slotResult->fetch_assoc();
        $slotId = (int) $slot['id'];

        $blockStmt = $connection->prepare("
            UPDATE appointment_slots
            SET is_available = 0
            WHERE id = ?
        ");

        if (!$blockStmt) {
            throw new RuntimeException('Slot block prepare failed: ' . $connection->error);
        }

        $blockStmt->bind_param('i', $slotId);
        $blockStmt->execute();
    }

    $aptStmt = $connection->prepare("
        INSERT INTO appointments
            (
                slot_id,
                full_name,
                phone,
                email,
                preferred_date,
                preferred_time,
                session_type,
                status,
                privacy_notice_accepted,
                commercial_consent_given,
                legal_basis,
                ip,
                user_agent,
                source
            )
        VALUES
            (?, ?, ?, ?, ?, ?, ?, 'pending', 1, ?, ?, ?, ?, ?)
    ");

    if (!$aptStmt) {
        throw new RuntimeException('Appointment prepare failed: ' . $connection->error);
    }

    // DÜZELTME: 12 parametre var, type string de 12 olmalı
    $aptStmt->bind_param(
        'issssssissss',
        $slotId,
        $fullName,
        $phone,
        $emailVal,
        $preferredDate,
        $preferredTime,
        $sessionType,
        $commercialConsent,
        $legalBasis,
        $ip,
        $userAgent,
        $leadSource
    );

    $aptStmt->execute();
    $appointmentId = (int) $connection->insert_id;

    $cStmt = $connection->prepare("
        INSERT INTO consent_logs (form_type, record_id, ip, user_agent, consents)
        VALUES ('appointment', ?, ?, ?, ?)
    ");

    if (!$cStmt) {
        throw new RuntimeException('Consent log prepare failed: ' . $connection->error);
    }

    $consentData = json_encode([
        'privacy_notice_accepted'  => true,
        'commercial_consent_given' => (bool) $commercialConsent,
        'legal_basis'              => $legalBasis,
    ], JSON_UNESCAPED_UNICODE);

    $cStmt->bind_param('isss', $appointmentId, $ip, $userAgent, $consentData);
    $cStmt->execute();

    $connection->commit();

} catch (Throwable $e) {
    $connection->rollback();

    $logDir = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }

    $logPath = $logDir . '/error.log';
    @file_put_contents(
        $logPath,
        date('Y-m-d H:i:s') . ' Appointment error: ' . $e->getMessage() . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    $_SESSION['old_input'] = $oldInput;
    flashSet('error', 'Randevu talebiniz alınamadı. Lütfen telefonla ulaşın.');
    header('Location: ' . ROOT_URL . 'randevu');
    exit;
}

// ── Email Bildirimi ───────────────────────────────────────────
require_once BASE_PATH . '/app/services/Mailer.php';

$sessionLabel = $sessionType === 'online' ? 'Online Görüşme' : 'Yüz Yüze Görüşme';
$dateLabel    = turkceTarih($preferredDate, 'd F Y');

// Admin'e bildirim
$adminMailer = new Mailer();
$adminMailer->to(CONTACT_EMAIL)
    ->subject('Yeni Randevu Talebi: ' . $fullName)
    ->template('appointment-admin', [
        'NAME'         => $fullName,
        'PHONE'        => $phone,
        'EMAIL'        => $emailVal ?? '—',
        'DATE'         => $dateLabel,
        'TIME'         => $preferredTime,
        'SESSION_TYPE' => $sessionLabel,
    ])
    ->send();

// Kullanıcıya onay
if ($emailVal) {
    $userMailer = new Mailer();
    $userMailer->to($emailVal)
        ->subject('Randevu Talebiniz Alındı — ' . SITE_NAME)
        ->template('appointment-new', [
            'NAME'         => $fullName,
            'DATE'         => $dateLabel,
            'TIME'         => $preferredTime,
            'SESSION_TYPE' => $sessionLabel,
        ])
        ->send();
}

unset($_SESSION['old_input']);
$_SESSION['appointment_completed'] = true;

header('Location: ' . ROOT_URL . 'randevu/tesekkur');
exit;