<?php
/**
 * templates/admin/pages/settings/save.php
 * POST /admin/settings
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/settings');
    exit;
}

csrfVerify();

$settingFields = [
    // İletişim
    'contact_phone'      => 'contact',
    'contact_whatsapp'   => 'contact',
    'contact_email'      => 'contact',
    'address_full'       => 'contact',
    'work_hours_weekday' => 'contact',
    'work_hours_saturday'=> 'contact',
    // Analytics
    'ga4_id'             => 'analytics',
    'aw_id'              => 'analytics',
    // Sosyal Medya
    'social_instagram'   => 'social',
    'social_facebook'    => 'social',
    'social_linkedin'    => 'social',
    'social_youtube'     => 'social',
    // GBP
    'gbp_profile_url'    => 'gbp',
    'gbp_review_url'     => 'gbp',
    // Public Uzman Profili
    'public_title'           => 'profile',
    'public_tagline'         => 'profile',
    'public_bio'             => 'profile',
    'public_hero_intro'      => 'profile',
    'public_experience_years'=> 'profile',
    'public_approach'        => 'profile',
    'public_specialties'     => 'profile',
    'public_certifications'  => 'profile',
];

$stmt = $connection->prepare("
    INSERT INTO settings (setting_key, setting_value, setting_group)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group)
");

foreach ($settingFields as $key => $group) {
    $value = sanitizeText($_POST[$key] ?? '', 500);
    $stmt->bind_param('sss', $key, $value, $group);
    $stmt->execute();
}

logAudit('update', 'settings', 0, null, ['updated_keys' => array_keys($settingFields)]);
flashSet('success', 'Ayarlar başarıyla kaydedildi.');
header('Location: ' . ROOT_URL . 'admin/settings');
exit;
