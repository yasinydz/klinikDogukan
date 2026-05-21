<?php
/**
 * templates/admin/pages/testimonials/create-submit.php
 * POST /admin/testimonials/create
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/testimonials');
    exit;
}

csrfVerify();

$authorName   = sanitizeText($_POST['author_name'] ?? '', 100);
$authorTitle  = sanitizeText($_POST['author_title'] ?? '', 150);
$content      = sanitizeText($_POST['content'] ?? '', 1000);
$rating       = max(1, min(5, (int)($_POST['rating'] ?? 5)));
$serviceType  = sanitizeText($_POST['service_type'] ?? '', 100);
$displayOrder = max(0, (int)($_POST['display_order'] ?? 0));
$isFeatured   = isset($_POST['is_featured']) ? 1 : 0;
$isActive     = isset($_POST['is_active']) ? 1 : 0;
$isGoogle     = isset($_POST['is_google']) ? 1 : 0;
$googleUrl    = $isGoogle ? sanitizeText($_POST['google_url'] ?? '', 500) : null;

if ($authorName === '' || $content === '') {
    flashSet('error', 'Ad ve yorum metni zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/testimonials/create');
    exit;
}

$stmt = $connection->prepare("
    INSERT INTO testimonials
        (author_name, author_title, content, rating, service_type,
         is_featured, is_active, is_google, google_url, display_order)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'sssisiissi',
    $authorName, $authorTitle, $content, $rating, $serviceType,
    $isFeatured, $isActive, $isGoogle, $googleUrl, $displayOrder
);

if (!$stmt->execute()) {
    flashSet('error', 'Yorum eklenemedi.');
    header('Location: ' . ROOT_URL . 'admin/testimonials/create');
    exit;
}

logAudit('create', 'testimonials', (int)$connection->insert_id, null, ['author' => $authorName]);
flashSet('success', 'Yorum başarıyla eklendi.');
header('Location: ' . ROOT_URL . 'admin/testimonials');
exit;
