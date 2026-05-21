<?php
/**
 * config/database.php
 *
 * Veritabanı bağlantısı.
 * app.php'den sonra include edilmeli.
 */

if (!defined('DB_HOST')) {
    require_once __DIR__ . '/app.php';
}

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int) DB_PORT);

if ($connection->connect_error) {
    // Hata mesajını kullanıcıya gösterme — logla
    $errorMsg = date('Y-m-d H:i:s') . ' DB Connection Error: '
        . $connection->connect_error . PHP_EOL;

    $logPath = dirname(__DIR__) . '/storage/logs/error.log';
    @file_put_contents($logPath, $errorMsg, FILE_APPEND | LOCK_EX);

    if (APP_DEBUG) {
        die('Veritabanı bağlantı hatası: ' . htmlspecialchars($connection->connect_error));
    } else {
        http_response_code(503);
        die('Şu anda hizmet verilemiyor. Lütfen daha sonra tekrar deneyin.');
    }
}

$connection->set_charset('utf8mb4');

// Strict mode ve timezone
$connection->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
$connection->query("SET time_zone = '+03:00'");
