<?php
/**
 * app/helpers/sanitize.php
 *
 * Input sanitizasyon ve output escaping fonksiyonları.
 * config/sanitize.php'nin yerini alır, aynı isimde fonksiyon korundu.
 */

/**
 * HTML output için güvenli escaping.
 * Tüm echo'larda bu fonksiyon kullanılmalı.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * TinyMCE veya benzeri editörden gelen HTML içeriği temizler.
 * Tehlikeli tag ve attribute'ları kaldırır.
 *
 * @param string $html Ham HTML
 * @return string Temizlenmiş HTML
 */
function sanitizeHtml(string $html): string
{
    if (empty($html)) {
        return '';
    }

    // İzin verilen etiketler
    $allowedTags = '<p><br><strong><b><em><i><u><s><strike>'
        . '<h1><h2><h3><h4><h5><h6>'
        . '<ul><ol><li>'
        . '<blockquote><pre><code>'
        . '<a><img>'
        . '<table><thead><tbody><tfoot><tr><th><td>'
        . '<hr><sub><sup><span><div>';

    $clean = strip_tags($html, $allowedTags);

    // on* event handler'ları kaldır
    $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean);
    $clean = preg_replace('/\s+on\w+\s*=\s*[^\s>]*/i',           '', $clean);

    // javascript: ve data: URI'lerini kaldır
    $clean = preg_replace('/href\s*=\s*["\']?\s*javascript\s*:/i',  'href="', $clean);
    $clean = preg_replace('/src\s*=\s*["\']?\s*javascript\s*:/i',   'src="',  $clean);
    $clean = preg_replace('/href\s*=\s*["\']?\s*data\s*:/i',        'href="', $clean);
    $clean = preg_replace('/src\s*=\s*["\']?\s*data\s*:/i',         'src="',  $clean);

    // style içinden expression() ve javascript: temizle
    $clean = preg_replace('/style\s*=\s*["\'][^"\']*expression\s*\([^)]*\)[^"\']*["\']/i', '', $clean);
    $clean = preg_replace('/style\s*=\s*["\'][^"\']*javascript\s*:[^"\']*["\']/i',         '', $clean);

    return $clean;
}

/**
 * Serbest metin input'u temizler.
 * HTML tag yok, sadece boşluk normalize.
 */
function sanitizeText(string $value, int $maxLength = 0): string
{
    $value = strip_tags($value);
    $value = trim($value);
    $value = preg_replace('/\s+/', ' ', $value);

    if ($maxLength > 0) {
        $value = mb_substr($value, 0, $maxLength, 'UTF-8');
    }

    return $value;
}

/**
 * Telefon numarasını normalize eder.
 * Sadece rakam, +, boşluk, tire, parantez bırakır.
 */
function sanitizePhone(string $phone): string
{
    return preg_replace('/[^\d\+\s\-\(\)]/', '', trim($phone));
}

/**
 * Slug için güvenli string üretir.
 * Türkçe karakter dönüşümü dahil.
 */
function sanitizeSlug(string $value): string
{
    $turkish = ['ı','i','ğ','g','ü','u','ş','s','ö','o','ç','c',
                'I','İ','Ğ','Ü','Ş','Ö','Ç'];
    $english = ['i','i','g','g','u','u','s','s','o','o','c','c',
                'i','i','g','u','s','o','c'];

    $value = str_replace($turkish, $english, $value);
    $value = mb_strtolower($value, 'UTF-8');
    $value = preg_replace('/[^a-z0-9\-]/', '-', $value);
    $value = preg_replace('/-+/', '-', $value);

    return trim($value, '-');
}

/**
 * Görsel dosya adını güvenli hale getirir.
 */
function sanitizeFilename(string $filename): string
{
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    $filename = ltrim($filename, '.');

    return $filename;
}

/**
 * IP adresini doğrular ve döndürür.
 */
function sanitizeIp(string $ip): string
{
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
}
