<?php
/**
 * app/helpers/slug.php
 *
 * SEO uyumlu URL slug üretimi.
 * config/slug.php'nin yerini alır.
 */

/**
 * Metinden SEO uyumlu slug üretir.
 */
function createSlug(string $text): string
{
    $turkish = ['ı','i','ğ','g','ü','u','ş','s','ö','o','ç','c',
                'I','İ','Ğ','Ü','Ş','Ö','Ç'];
    $english = ['i','i','g','g','u','u','s','s','o','o','c','c',
                'i','i','g','u','s','o','c'];

    $text = str_replace($turkish, $english, $text);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);

    return trim($text, '-');
}

/**
 * Veritabanında benzersiz slug üretir.
 *
 * @param mysqli $db
 * @param string $text   Başlık veya kaynak metin
 * @param string $table  Kontrol edilecek tablo ('posts','post_categories','services')
 * @param int    $id     Güncelleme için mevcut kayıt ID'si (0 = yeni kayıt)
 * @return string
 */
function generateUniqueSlug(mysqli $db, string $text, string $table = 'posts', int $id = 0): string
{
    $allowedTables = ['posts', 'post_categories', 'services', 'city_pages'];

    if (!in_array($table, $allowedTables, true)) {
        $table = 'posts';
    }

    $slug         = createSlug($text);
    $originalSlug = $slug;
    $counter      = 1;

    while (true) {
        $query = "SELECT id FROM `{$table}` WHERE slug = ?";

        if ($id > 0) {
            $query .= ' AND id != ?';
            $stmt   = $db->prepare($query);
            $stmt->bind_param('si', $slug, $id);
        } else {
            $stmt = $db->prepare($query);
            $stmt->bind_param('s', $slug);
        }

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt->close();
            break;
        }

        $stmt->close();
        $counter++;
        $slug = $originalSlug . '-' . $counter;
    }

    return $slug;
}
