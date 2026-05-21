<?php
/**
 * config/mail.php
 *
 * SMTP ve e-posta ayarları.
 * Bildirim e-postaları için (randevu onayı, iletişim formu bildirimi).
 */

if (!defined('APP_ENV')) {
    require_once __DIR__ . '/app.php';
}

define('MAIL_HOST',         env('MAIL_HOST',         ''));
define('MAIL_PORT',         env('MAIL_PORT',         587));
define('MAIL_ENCRYPTION',   env('MAIL_ENCRYPTION',   'tls'));
define('MAIL_USER',         env('MAIL_USERNAME', env('MAIL_USER', '')));
define('MAIL_PASS',         env('MAIL_PASSWORD', env('MAIL_PASS', '')));
define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', CONTACT_EMAIL));
define('MAIL_FROM_NAME',    env('MAIL_FROM_NAME',    SITE_NAME));

/**
 * Basit SMTP ile e-posta gönderir.
 * PHPMailer veya harici lib yoksa bu fonksiyon mail() ile fallback yapar.
 *
 * @param string $to      Alıcı e-posta
 * @param string $subject Konu
 * @param string $body    HTML içerik
 * @return bool
 */
function sendMail(string $to, string $subject, string $body): bool
{
    if (MAIL_HOST === '') {
        // SMTP tanımlı değilse PHP mail() ile gönder
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>' . "\r\n";

        return @mail($to, $subject, $body, $headers);
    }

    // PHPMailer entegrasyonu için yer tutucu
    // Composer ile PHPMailer yüklüyse buraya entegre edilebilir
    return false;
}
