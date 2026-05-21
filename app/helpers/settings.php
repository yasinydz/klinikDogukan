<?php
/**
 * app/helpers/settings.php
 *
 * DB settings tablosundan değer okur, .env constant'ına fallback yapar.
 * Tek seferde tüm ayarları cache'ler.
 *
 * Kullanım:
 *   siteSetting('contact_phone', CONTACT_PHONE)
 */

if (!function_exists('siteSetting')) {

    /**
     * @param string $key     settings.setting_key
     * @param mixed  $default Fallback değer (genelde .env constant'ı)
     * @return string
     */
    function siteSetting(string $key, $default = ''): string
    {
        static $cache = null;

        if ($cache === null) {
            global $connection;
            $cache = [];

            if ($connection) {
                $result = @$connection->query(
                    "SELECT setting_key, setting_value FROM settings"
                );
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $cache[$row['setting_key']] = $row['setting_value'];
                    }
                }
            }
        }

        $val = $cache[$key] ?? null;
        return ($val !== null && $val !== '') ? $val : (string) $default;
    }
}
