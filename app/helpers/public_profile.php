<?php
/**
 * app/helpers/public_profile.php
 *
 * Birleşik public uzman profili.
 * Kaynak 1: admins tablosu → ad, soyad, avatar, email
 * Kaynak 2: settings tablosu → public title, bio, specialties, vb.
 * Kaynak 3: config/app.php constant'ları → fallback
 *
 * Kullanım:
 *   require_once BASE_PATH . '/app/helpers/public_profile.php';
 *   $profile = getPublicProfile();
 *   echo $profile['full_name'];
 *   echo publicAvatarUrl();
 */

require_once (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/app/helpers/settings.php';

if (!function_exists('getPublicProfile')) {

    /**
     * Tüm public uzman profilini birleşik array olarak döndürür.
     * Sonuç request boyunca cache'lenir.
     */
    function getPublicProfile(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        global $connection;

        // ── Admins tablosundan (primary admin = id 1) ────────────
        $adminData = [
            'first_name' => '',
            'last_name'  => '',
            'avatar'     => 'default.png',
            'email'      => '',
        ];

        if ($connection) {
            $stmt = $connection->prepare(
                "SELECT first_name, last_name, avatar, email FROM admins WHERE id = 1 LIMIT 1"
            );
            if ($stmt) {
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                if ($row) {
                    $adminData = $row;
                }
                $stmt->close();
            }
        }

        // ── Birleşik profil ──────────────────────────────────────
        $firstName = $adminData['first_name'] !== '' ? $adminData['first_name'] : 'Doğukan';
        $lastName  = $adminData['last_name']  !== '' ? $adminData['last_name']  : 'Kopuk';

        $cache = [
            // Admin tablosundan
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'full_name'    => trim($firstName . ' ' . $lastName),
            'avatar'       => $adminData['avatar'] ?? 'default.png',
            'email'        => $adminData['email'] ?? '',

            // Settings tablosundan (constant fallback)
            'title'        => siteSetting('public_title',        defined('PSYCHOLOGIST_TITLE') ? PSYCHOLOGIST_TITLE : 'Klinik Psikolog'),
            'bio'          => siteSetting('public_bio',          defined('PSYCHOLOGIST_BIO') ? PSYCHOLOGIST_BIO : ''),
            'tagline'      => siteSetting('public_tagline',      'Klinik Psikolog | Bilişsel Davranışçı Terapi Uzmanı'),
            'hero_intro'   => siteSetting('public_hero_intro',   ''),
            'experience'   => (int) siteSetting('public_experience_years', defined('PSYCHOLOGIST_EXPERIENCE_YEARS') ? (string)PSYCHOLOGIST_EXPERIENCE_YEARS : '8'),
            'approach'     => siteSetting('public_approach',      defined('PSYCHOLOGIST_APPROACH') ? PSYCHOLOGIST_APPROACH : ''),
            'specialties'  => siteSetting('public_specialties',   defined('PSYCHOLOGIST_SPECIALTIES') ? PSYCHOLOGIST_SPECIALTIES : ''),
            'certifications' => siteSetting('public_certifications', defined('PSYCHOLOGIST_CERTIFICATIONS') ? PSYCHOLOGIST_CERTIFICATIONS : ''),
        ];

        return $cache;
    }

    /**
     * Tek bir profil değeri döndürür.
     */
    function publicProfileValue(string $key, string $default = ''): string
    {
        $profile = getPublicProfile();
        $val = $profile[$key] ?? '';
        return $val !== '' ? (string) $val : $default;
    }

    /**
     * Public uzman avatar URL'si.
     * Avatar uploads/ içindeyse oradan, değilse images/ içinden, yoksa fallback.
     */
    function publicAvatarUrl(): string
    {
        $avatar = getPublicProfile()['avatar'];

        if ($avatar === '' || $avatar === 'default.png') {
            // Default fallback: images/dogukan.png veya generic
            $defaultPath = PUBLIC_PATH . '/images/dogukan.png';
            if (file_exists($defaultPath)) {
                return SITE_URL . '/images/dogukan.png';
            }
            return SITE_URL . '/images/default-avatar.png';
        }

        // Upload edilmiş avatar
        $uploadPath = PUBLIC_PATH . '/images/uploads/' . $avatar;
        if (file_exists($uploadPath)) {
            return SITE_URL . '/images/uploads/' . $avatar;
        }

        // images/ altında mı?
        $imgPath = PUBLIC_PATH . '/images/' . $avatar;
        if (file_exists($imgPath)) {
            return SITE_URL . '/images/' . $avatar;
        }

        // Final fallback
        return SITE_URL . '/images/dogukan.png';
    }

    /**
     * Herhangi bir admin'in avatar URL'si (post author vb. için).
     */
    function adminAvatarUrl(?string $avatar): string
    {
        if (!$avatar || $avatar === '' || $avatar === 'default.png') {
            return SITE_URL . '/images/dogukan.png';
        }

        $uploadPath = PUBLIC_PATH . '/images/uploads/' . $avatar;
        if (file_exists($uploadPath)) {
            return SITE_URL . '/images/uploads/' . $avatar;
        }

        $imgPath = PUBLIC_PATH . '/images/' . $avatar;
        if (file_exists($imgPath)) {
            return SITE_URL . '/images/' . $avatar;
        }

        return SITE_URL . '/images/dogukan.png';
    }

    /**
     * Uzmanlık alanları array olarak.
     */
    function publicSpecialties(): array
    {
        return array_filter(array_map('trim', explode(',', publicProfileValue('specialties'))));
    }

    /**
     * Sertifikalar array olarak.
     */
    function publicCertifications(): array
    {
        return array_filter(array_map('trim', explode(',', publicProfileValue('certifications'))));
    }
}
