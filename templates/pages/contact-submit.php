<?php
/**
 * templates/pages/contact-submit.php
 * POST /iletisim/gonder
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/validator.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/rate_limiter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'iletisim');
    exit;
}

csrfVerify();
rateLimitCheck('contact_form', RATE_LIMIT_CONTACT, RATE_LIMIT_WINDOW);

$fullName              = sanitizeText($_POST['full_name'] ?? '', 100);
$phone                 = sanitizePhone($_POST['phone'] ?? '');
$email                 = trim($_POST['email'] ?? '');
$subject               = sanitizeText($_POST['subject'] ?? '', 200);
$message               = sanitizeText($_POST['message'] ?? '', 300);
$privacyAccepted       = $_POST['privacy_notice_accepted'] ?? '';
$commercialConsent     = isset($_POST['commercial_consent_given']) ? 1 : 0;

// Lead source detection
$leadSource = sanitizeText($_POST['lead_source'] ?? '', 30);
$allowedSources = ['organik', 'google_ads', 'instagram', 'whatsapp', 'maps', 'referral', 'direct'];
if ($leadSource === '' || !in_array($leadSource, $allowedSources, true)) {
    $utmSource = sanitizeText($_GET['utm_source'] ?? '', 30);
    $referer   = $_SERVER['HTTP_REFERER'] ?? '';
    if ($utmSource !== '') {
        $leadSource = in_array($utmSource, $allowedSources, true) ? $utmSource : 'direct';
    } elseif (str_contains($referer, 'instagram.com')) {
        $leadSource = 'instagram';
    } elseif (str_contains($referer, 'google.com')) {
        $leadSource = 'organik';
    } else {
        $leadSource = 'direct';
    }
}

$allowedSubjects = [
    'Randevu bilgisi almak istiyorum',
    'Ücret ve seans hakkında bilgi',
    'Online terapi hakkında',
    'Genel bilgi',
    'Diğer',
];

$v = new Validator($_POST);
$v->required('full_name', 'Ad Soyad')
  ->minLength('full_name', 2, 'Ad Soyad')
  ->required('phone', 'Telefon')
  ->phone('phone', 'Telefon')
  ->email('email', 'E-posta')
  ->required('subject', 'Konu')
  ->inList('subject', $allowedSubjects, 'Konu')
  ->required('message', 'Mesaj')
  ->minLength('message', 10, 'Mesaj')
  ->accepted('privacy_notice_accepted', 'KVKK Aydınlatma Metni onayı');

if ($v->fails()) {
    $_SESSION['old_input'] = compact('full_name', 'phone', 'email', 'subject', 'message');
    flashSet('error', $v->firstError());
    header('Location: ' . ROOT_URL . 'iletisim');
    exit;
}

$ip        = sanitizeIp($_SERVER['REMOTE_ADDR'] ?? '');
$userAgent = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
$emailVal  = $email !== '' ? $email : null;
$legalBasis = 'legitimate_interest';

$stmt = $connection->prepare("
    INSERT INTO contact_messages
        (full_name, phone, email, subject, message,
         privacy_notice_accepted, commercial_consent_given,
         legal_basis, ip, user_agent, source, retention_expires_at)
    VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?,
            DATE_ADD(NOW(), INTERVAL 6 MONTH))
");
$stmt->bind_param(
    'ssssssisss',
    $fullName, $phone, $emailVal, $subject, $message,
    $commercialConsent, $legalBasis, $ip, $userAgent, $leadSource
);

if (!$stmt->execute()) {
    flashSet('error', 'Mesajınız gönderilemedi. Lütfen tekrar deneyin.');
    header('Location: ' . ROOT_URL . 'iletisim');
    exit;
}

$messageId = (int) $connection->insert_id;

// Consent log
$consentData = json_encode([
    'privacy_notice_accepted' => true,
    'commercial_consent_given' => (bool) $commercialConsent,
    'legal_basis' => $legalBasis,
], JSON_UNESCAPED_UNICODE);

$cStmt = $connection->prepare("
    INSERT INTO consent_logs (form_type, record_id, ip, user_agent, consents)
    VALUES ('contact', ?, ?, ?, ?)
");
$cStmt->bind_param('isss', $messageId, $ip, $userAgent, $consentData);
$cStmt->execute();

// ── Email Bildirimi ───────────────────────────────────────────
require_once BASE_PATH . '/app/services/Mailer.php';

// Admin'e bildirim
$adminMailer = new Mailer();
$adminMailer->to(CONTACT_EMAIL)
    ->subject('Yeni İletişim Mesajı: ' . $subject)
    ->replyTo($emailVal ?? '')
    ->template('contact-admin', [
        'NAME'         => $fullName,
        'PHONE'        => $phone,
        'EMAIL'        => $emailVal ?? '—',
        'SUBJECT_TEXT' => $subject,
        'MESSAGE_RAW'  => nl2br(e($message)),
    ])
    ->send();

// Kullanıcıya onay (email varsa)
if ($emailVal) {
    $userMailer = new Mailer();
    $userMailer->to($emailVal)
        ->subject('Mesajınız Alındı — ' . SITE_NAME)
        ->template('contact-user', [
            'NAME'         => $fullName,
            'SUBJECT_TEXT' => $subject,
        ])
        ->send();
}

unset($_SESSION['old_input']);
flashSet('success', 'Mesajınız başarıyla iletildi.');
header('Location: ' . ROOT_URL . 'iletisim');
exit;
