<?php
/**
 * templates/admin/pages/testimonials/edit-submit.php
 * POST /admin/testimonials/edit
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/testimonials');
    exit;
}

csrfVerify();

$id           = (int) ($_POST['id'] ?? 0);
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

if ($id <= 0 || $authorName === '' || $content === '') {
    flashSet('error', 'Ad ve yorum metni zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/testimonials/edit?id=' . $id);
    exit;
}

$stmt = $connection->prepare("
    UPDATE testimonials SET
        author_name = ?, author_title = ?, content = ?, rating = ?,
        service_type = ?, is_featured = ?, is_active = ?,
        is_google = ?, google_url = ?, display_order = ?
    WHERE id = ? LIMIT 1
");
$stmt->bind_param(
    'sssisiissii',
    $authorName, $authorTitle, $content, $rating,
    $serviceType, $isFeatured, $isActive,
    $isGoogle, $googleUrl, $displayOrder, $id
);

if (!$stmt->execute()) {
    flashSet('error', 'Yorum güncellenemedi.');
    header('Location: ' . ROOT_URL . 'admin/testimonials/edit?id=' . $id);
    exit;
}

logAudit('update', 'testimonials', $id, null, ['author' => $authorName]);
flashSet('success', 'Yorum güncellendi.');
header('Location: ' . ROOT_URL . 'admin/testimonials');
exit;
