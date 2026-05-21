<?php
/**
 * app/helpers/csrf.php
 *
 * CSRF token üretimi ve doğrulaması.
 * Her form submit'te token kontrol edilmeli.
 */

/**
 * CSRF token üretir veya mevcut olanı döndürür.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Form içine hidden input olarak CSRF token basar.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8')
        . '">';
}

/**
 * Gelen CSRF tokenını doğrular.
 * Başarısız olursa 403 döner ve çıkar.
 */
function csrfVerify(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);

        $logPath = dirname(__DIR__, 2) . '/storage/logs/app.log';
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $uri     = $_SERVER['REQUEST_URI'] ?? '';
        @file_put_contents(
            $logPath,
            date('Y-m-d H:i:s') . " CSRF_FAIL ip={$ip} uri={$uri}" . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        die('Geçersiz istek. Lütfen sayfayı yenileyip tekrar deneyin.');
    }

    // Token'ı yenile (double-submit önlemi)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
