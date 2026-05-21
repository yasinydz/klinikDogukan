<?php
/**
 * app/helpers/rate_limiter.php
 *
 * IP bazlı rate limiting.
 * Session tabanlı — harici cache gerektirmez.
 * Production'da Redis/Memcached ile değiştirilebilir.
 */

class RateLimiter
{
    /**
     * İstek sayısını kontrol eder ve artırır.
     *
     * @param string $key    Benzersiz limit anahtarı (ör: 'contact_form')
     * @param int    $limit  İzin verilen maksimum istek sayısı
     * @param int    $window Zaman penceresi (saniye)
     * @return bool  True = izin var, False = limit aşıldı
     */
    public static function attempt(string $key, int $limit, int $window): bool
    {
        $ip         = self::getIp();
        $sessionKey = 'rl_' . md5($key . '_' . $ip);
        $now        = time();

        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [
                'count'    => 0,
                'reset_at' => $now + $window,
            ];
        }

        // Pencere süresi dolmuşsa sıfırla
        if ($now >= $_SESSION[$sessionKey]['reset_at']) {
            $_SESSION[$sessionKey] = [
                'count'    => 0,
                'reset_at' => $now + $window,
            ];
        }

        // Limit aşıldı mı?
        if ($_SESSION[$sessionKey]['count'] >= $limit) {
            self::log($key, $ip, 'BLOCKED');
            return false;
        }

        $_SESSION[$sessionKey]['count']++;
        return true;
    }

    /**
     * Kalan süreyi döndürür (saniye).
     */
    public static function remainingTime(string $key): int
    {
        $ip         = self::getIp();
        $sessionKey = 'rl_' . md5($key . '_' . $ip);

        if (!isset($_SESSION[$sessionKey])) {
            return 0;
        }

        $remaining = $_SESSION[$sessionKey]['reset_at'] - time();

        return max(0, $remaining);
    }

    /**
     * Belirli bir key'i sıfırlar (başarılı işlem sonrası).
     */
    public static function clear(string $key): void
    {
        $ip         = self::getIp();
        $sessionKey = 'rl_' . md5($key . '_' . $ip);
        unset($_SESSION[$sessionKey]);
    }

    /**
     * Limit aşımını loglar.
     */
    private static function log(string $key, string $ip, string $status): void
    {
        $logPath = dirname(__DIR__, 2) . '/storage/logs/app.log';
        $line    = date('Y-m-d H:i:s')
            . " RATE_LIMIT key={$key} ip={$ip} status={$status}"
            . PHP_EOL;

        @file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Gerçek IP adresini döndürür.
     * Proxy arkasındaki IP'yi de destekler.
     */
    private static function getIp(): string
    {
        $keys = [
            'HTTP_CF_CONNECTING_IP',   // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $key) {
            $ip = $_SERVER[$key] ?? '';

            if ($ip === '') {
                continue;
            }

            // Virgülle ayrılmış liste ise ilkini al
            $ip = trim(explode(',', $ip)[0]);

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

/**
 * Kolay kullanım fonksiyonu.
 * Rate limit aşıldığında 429 döner ve çıkar.
 */
function rateLimitCheck(string $key, int $limit, int $window): void
{
    if (!RateLimiter::attempt($key, $limit, $window)) {
        $remaining = RateLimiter::remainingTime($key);
        $minutes   = ceil($remaining / 60);

        http_response_code(429);
        header('Retry-After: ' . $remaining);

        die("Çok fazla istek gönderildi. Lütfen {$minutes} dakika sonra tekrar deneyin.");
    }
}
