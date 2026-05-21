<?php
/**
 * templates/admin/pages/local-trust/save.php
 * POST /admin/local-trust
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/local-trust');
    exit;
}

csrfVerify();

$fields = [
    'gbp_profile_url'     => 'gbp',
    'gbp_review_url'      => 'gbp',
    'gbp_place_id'        => 'gbp',
    'maps_embed_url'      => 'maps',
    'maps_directions_url' => 'maps',
    'maps_transport_note' => 'maps',
    'maps_parking_note'   => 'maps',
    'maps_inperson_note'  => 'maps',
    'maps_online_note'    => 'maps',
];

// Checkbox'lar — gönderilmezse '0'
$checkboxes = [
    'maps_show_home'    => 'maps',
    'maps_show_contact' => 'maps',
    'maps_show_footer'  => 'maps',
];

$stmt = $connection->prepare("
    INSERT INTO settings (setting_key, setting_value, setting_group)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group)
");

foreach ($fields as $key => $group) {
    $value = sanitizeText($_POST[$key] ?? '', 1000);
    $stmt->bind_param('sss', $key, $value, $group);
    $stmt->execute();
}

foreach ($checkboxes as $key => $group) {
    $value = isset($_POST[$key]) ? '1' : '0';
    $stmt->bind_param('sss', $key, $value, $group);
    $stmt->execute();
}

$stmt->close();

logAudit('update', 'settings', 0, null, ['section' => 'local-trust']);
flashSet('success', 'Konum ve GBP ayarları kaydedildi.');
header('Location: ' . ROOT_URL . 'admin/local-trust');
exit;
