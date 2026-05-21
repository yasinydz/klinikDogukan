<?php
/**
 * app/helpers/text.php
 * Metin yardımcı fonksiyonları.
 */

if (!function_exists('readingTime')) {
    /**
     * Yaklaşık okuma süresi hesaplar.
     * @param string $text HTML içerik
     * @return string "X dk okuma"
     */
    function readingTime(string $text): string
    {
        return max(1, (int) ceil(str_word_count(strip_tags($text)) / 200)) . ' dk okuma';
    }
}
