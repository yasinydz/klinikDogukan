<?php
/**
 * config/app.php
 *
 * Uygulama sabitleri ve ortam değişkenleri.
 * constants.php'nin yerini alır.
 * Tüm değerler .env dosyasından okunur.
 */

// ── .env Yükleyici ────────────────────────────────────────────
(function () {
    $envFile = dirname(__DIR__) . '/.env';

    if (!file_exists($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Yorum satırlarını atla
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Tırnak işaretlerini temizle
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
})();

// ── Env Okuma Yardımcısı ──────────────────────────────────────
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return match (strtolower((string) $value)) {
            'true', '1', 'yes' => true,
            'false', '0', 'no' => false,
            'null'             => null,
            default            => $value,
        };
    }
}

// ── Oturum Başlatma ───────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    $isSecure  = env('SESSION_SECURE', true) && env('APP_ENV') === 'production';
    $lifetime  = (int) env('SESSION_LIFETIME', 7200);

    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.gc_maxlifetime', (string) $lifetime);

    session_start();
}

// ── Uygulama ──────────────────────────────────────────────────
define('APP_ENV',   env('APP_ENV',   'production'));
define('APP_DEBUG', env('APP_DEBUG', false));

// ── URL ───────────────────────────────────────────────────────
(function () {
    $appUrl = env('APP_URL', '');

    if ($appUrl !== '') {
        define('SITE_URL', rtrim($appUrl, '/'));
        define('ROOT_URL', SITE_URL . '/');
        return;
    }

    // APP_URL tanımlanmamışsa otomatik tespit
    $host     = $_SERVER['HTTP_HOST']   ?? 'localhost';
    $isHttps  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
             || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
             || (($_SERVER['SERVER_PORT'] ?? 80) == 443);

    $protocol = $isHttps ? 'https' : 'http';

    define('SITE_URL', $protocol . '://' . $host);
    define('ROOT_URL', SITE_URL . '/');
})();

define(
    'PUBLIC_PATH',
    is_dir(BASE_PATH . '/public')
        ? BASE_PATH . '/public'
        : (is_dir(BASE_PATH . '/public_html') ? BASE_PATH . '/public_html' : BASE_PATH)
);

// ── Veritabanı ────────────────────────────────────────────────
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'psikolog'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

// ── Site Kimliği ──────────────────────────────────────────────
define('SITE_NAME',       'Klinik Psikolog Doğukan Kopuk');
define('PSYCHOLOGIST_NAME',  'Doğukan Kopuk');
define('PSYCHOLOGIST_TITLE', 'Klinik Psikolog');
define('PSYCHOLOGIST_EXPERIENCE_YEARS', (int) env('PSYCHOLOGIST_EXPERIENCE_YEARS', 5));
define('PSYCHOLOGIST_APPROACH', env('PSYCHOLOGIST_APPROACH',
    'Bilişsel Davranışçı Terapi (BDT), EMDR ve şema terapi yaklaşımlarını bütüncül biçimde kullanıyorum.'));
define('PSYCHOLOGIST_SPECIALTIES', env('PSYCHOLOGIST_SPECIALTIES',
    'Anksiyete,Depresyon,Travma,OKB,İlişkisel Sorunlar,Online Terapi'));
define('PSYCHOLOGIST_CERTIFICATIONS', env('PSYCHOLOGIST_CERTIFICATIONS',
    'Klinik Psikoloji Yüksek Lisansı,BDT Uygulayıcı Sertifikası,EMDR Uygulayıcı Sertifikası'));
define(
    'PSYCHOLOGIST_BIO',
    env(
        'PSYCHOLOGIST_BIO',
        "İzmit ve Kocaeli'de bireysel terapi, çift terapisi ve online seans sunan Klinik Psikolog Doğukan Kopuk, kanıta dayalı terapi yöntemleriyle danışanlarına güvenli bir alan sağlamaktadır."
    )
);

// ── İletişim / NAP ────────────────────────────────────────────
define('CONTACT_PHONE',      env('CONTACT_PHONE',      '+905000000000'));
define('CONTACT_PHONE_HREF', env('CONTACT_PHONE_HREF', '+905000000000'));
define('CONTACT_WHATSAPP',   env('CONTACT_WHATSAPP',   '905000000000'));
define('CONTACT_EMAIL',      env('CONTACT_EMAIL',      'iletisim@orneksite.com'));

// ── Adres ─────────────────────────────────────────────────────
define('ADDRESS_STREET',   env('ADDRESS_STREET',   ''));
define('ADDRESS_DISTRICT', env('ADDRESS_DISTRICT', 'Izmit'));
define('ADDRESS_CITY',     env('ADDRESS_CITY',     'Kocaeli'));
define('ADDRESS_COUNTRY',  env('ADDRESS_COUNTRY',  'TR'));
define('ADDRESS_POSTAL',   env('ADDRESS_POSTAL',   '41000'));
define('GEO_LATITUDE',     env('GEO_LATITUDE',     '40.7659'));
define('GEO_LONGITUDE',    env('GEO_LONGITUDE',    '29.9408'));

// ── Çalışma Saatleri ──────────────────────────────────────────
define('WORK_HOURS_WEEKDAY',  'Pzt – Cum: 09:00 – 18:00');
define('WORK_HOURS_SATURDAY', 'Cmt: 10:00 – 14:00');

// ── Analytics ─────────────────────────────────────────────────
define('GA4_ID', env('GA4_ID', ''));
define('AW_ID',  env('AW_ID',  ''));

// ── Sosyal Medya ──────────────────────────────────────────────
define('SOCIAL_INSTAGRAM', env('SOCIAL_INSTAGRAM', ''));
define('SOCIAL_FACEBOOK',  env('SOCIAL_FACEBOOK',  ''));
define('SOCIAL_LINKEDIN',  env('SOCIAL_LINKEDIN',  ''));
define('SOCIAL_YOUTUBE',   env('SOCIAL_YOUTUBE',   ''));

// ── Google Business Profile ───────────────────────────────────
define('GBP_PLACE_ID',    env('GBP_PLACE_ID',    ''));
define('GBP_PROFILE_URL', env('GBP_PROFILE_URL', ''));
define('GBP_REVIEW_URL',  env('GBP_REVIEW_URL',  ''));

// ── Rate Limiting ─────────────────────────────────────────────
define('RATE_LIMIT_CONTACT',     (int) env('RATE_LIMIT_CONTACT',     5));
define('RATE_LIMIT_APPOINTMENT', (int) env('RATE_LIMIT_APPOINTMENT', 3));
define('RATE_LIMIT_LOGIN',       (int) env('RATE_LIMIT_LOGIN',       5));
define('RATE_LIMIT_WINDOW',      (int) env('RATE_LIMIT_WINDOW',      900));

// ── Hata Yönetimi ─────────────────────────────────────────────
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);

    // Production'da hataları logla
    $logPath = dirname(__DIR__) . '/storage/logs/error.log';
    ini_set('log_errors', '1');
    ini_set('error_log', $logPath);
}


// ── Türkçe Tarih Yardımcısı ───────────────────────────────────
if (!function_exists('turkceTarih')) {
    function turkceTarih(string $dateStr, string $format = 'd F Y'): string {
        $ts = strtotime($dateStr);
        if ($ts === false) return $dateStr;

        $gunler = ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'];
        $aylar  = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran',
                   'Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];

        /*
         * str_replace() sequential çalıştığından bir replace'in çıktısı
         * sonraki replace'in girdisine dönüşür.
         * Örneğin: 'F'→'Mart' sonra 'M'→'Mar' yapılınca 'Mart' → 'Marart' olur.
         * 'l'→'Pazartesi' sonra 'i'→minute yapılınca 'Pazartesi' → 'Pazartes00' olur.
         * strtr() tüm key'leri tek bir geçişte değiştirir — cascade problemi yoktur.
         */
        $map = [
            'l' => $gunler[(int)date('w', $ts)],
            'F' => $aylar[(int)date('n', $ts)],
            'M' => mb_substr($aylar[(int)date('n', $ts)], 0, 3, 'UTF-8'),
            'd' => date('d', $ts),
            'j' => date('j', $ts),
            'Y' => date('Y', $ts),
            'H' => date('H', $ts),
            'i' => date('i', $ts),
            'N' => (string)((int)date('N', $ts)),
        ];

        return strtr($format, $map);
    }
}

// ── Zaman Dilimi ──────────────────────────────────────────────
date_default_timezone_set('Europe/Istanbul');
