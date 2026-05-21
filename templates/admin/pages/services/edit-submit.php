<?php
/**
 * templates/admin/pages/services/edit-submit.php
 * POST /admin/services/edit
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/image.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

csrfVerify();

$id           = (int) ($_POST['id'] ?? 0);
$title        = sanitizeText($_POST['title']      ?? '', 200);
$summary      = sanitizeText($_POST['summary']    ?? '', 300);
$content      = sanitizeHtml($_POST['content']    ?? '');
$iconClass    = sanitizeText($_POST['icon_class'] ?? '', 100);
$displayOrder = max(0, (int)($_POST['display_order'] ?? 0));
$isActive     = isset($_POST['is_active']) ? 1 : 0;
$metaTitle    = sanitizeText($_POST['meta_title'] ?? '', 70);
$metaDesc     = sanitizeText($_POST['meta_desc']  ?? '', 160);

if ($id <= 0) {
    flashSet('error', 'Geçersiz hizmet.');
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

if ($title === '' || $summary === '') {
    flashSet('error', 'Başlık ve kısa açıklama zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/services/edit?id=' . $id);
    exit;
}

// ── Görsel Yükleme ────────────────────────────────────────────
$imageName    = null;
$removeImage  = isset($_POST['remove_image']);
$serviceImage = $_FILES['service_image'] ?? null;

if ($serviceImage && !empty($serviceImage['name']) && $serviceImage['error'] === UPLOAD_ERR_OK) {
    $imgExt  = strtolower(pathinfo($serviceImage['name'], PATHINFO_EXTENSION));
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $imgMime = finfo_file($finfo, $serviceImage['tmp_name']);
    finfo_close($finfo);

    if (in_array($imgExt, ['jpg', 'jpeg', 'png'], true)
        && in_array($imgMime, ['image/jpeg', 'image/png'], true)
        && $serviceImage['size'] <= 2 * 1024 * 1024) {

        $imageName = time() . '_' . sanitizeFilename($serviceImage['name']);
        $uploadDir = PUBLIC_PATH . '/images/uploads';

        $imgResult = processUploadedImage(
            $serviceImage['tmp_name'],
            $uploadDir,
            $imageName,
            ['maxWidth' => 1600, 'thumbW' => 400, 'thumbH' => 225, 'quality' => 85, 'makeWebp' => true]
        );

        if ($imgResult === false) {
            move_uploaded_file($serviceImage['tmp_name'], $uploadDir . '/' . $imageName);
        }
    } else {
        flashSet('error', 'Görsel formatı jpg/jpeg/png ve boyut max 2 MB olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/services/edit?id=' . $id);
        exit;
    }
}

// ── UPDATE sorgusu ────────────────────────────────────────────
// Slug değiştirilmiyor — SEO için sabit

$sql = "UPDATE services
        SET title = ?, summary = ?, content = ?, icon_class = ?,
            display_order = ?, is_active = ?, meta_title = ?, meta_desc = ?,
            updated_at = NOW()";

$params = [$title, $summary, $content, $iconClass, $displayOrder, $isActive, $metaTitle, $metaDesc];
$types  = 'ssssisss';

if ($imageName !== null) {
    // Yeni görsel yüklendi
    $sql    .= ", image = ?";
    $params[] = $imageName;
    $types   .= 's';
} elseif ($removeImage) {
    // Görsel kaldırma istendi
    $sql .= ", image = NULL";
}

$sql    .= " WHERE id = ? LIMIT 1";
$params[] = $id;
$types   .= 'i';

$stmt = $connection->prepare($sql);
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    flashSet('error', 'Hizmet güncellenemedi: ' . $stmt->error);
    header('Location: ' . ROOT_URL . 'admin/services/edit?id=' . $id);
    exit;
}

logAudit('update', 'services', $id, null, ['title' => $title]);
flashSet('success', '"' . $title . '" hizmeti güncellendi.');
header('Location: ' . ROOT_URL . 'admin/services');
exit;
