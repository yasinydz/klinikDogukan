<?php
/**
 * templates/admin/pages/services/create-submit.php
 * POST /admin/services/create
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/slug.php';
require_once BASE_PATH . '/app/helpers/image.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

csrfVerify();

$title        = sanitizeText($_POST['title']      ?? '', 200);
$summary      = sanitizeText($_POST['summary']    ?? '', 300);
$content      = sanitizeHtml($_POST['content']    ?? '');
$iconClass    = sanitizeText($_POST['icon_class'] ?? '', 100);
$displayOrder = max(0, (int)($_POST['display_order'] ?? 0));
$isActive     = isset($_POST['is_active']) ? 1 : 0;
$metaTitle    = sanitizeText($_POST['meta_title'] ?? '', 70);
$metaDesc     = sanitizeText($_POST['meta_desc']  ?? '', 160);

if ($title === '') {
    flashSet('error', 'Hizmet başlığı zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/services/create');
    exit;
}

if ($summary === '') {
    flashSet('error', 'Kısa açıklama zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/services/create');
    exit;
}

// ── Görsel Yükleme (opsiyonel) ────────────────────────────────
$imageName    = null;
$serviceImage = $_FILES['service_image'] ?? null;

if ($serviceImage && !empty($serviceImage['name']) && $serviceImage['error'] === UPLOAD_ERR_OK) {
    $imgExt  = strtolower(pathinfo($serviceImage['name'], PATHINFO_EXTENSION));
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $imgMime = finfo_file($finfo, $serviceImage['tmp_name']);
    finfo_close($finfo);

    if (!in_array($imgExt, ['jpg', 'jpeg', 'png'], true)
        || !in_array($imgMime, ['image/jpeg', 'image/png'], true)) {
        flashSet('error', 'Görsel formatı jpg, jpeg veya png olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/services/create');
        exit;
    }

    if ($serviceImage['size'] > 2 * 1024 * 1024) {
        flashSet('error', 'Görsel boyutu 2 MB\'dan küçük olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/services/create');
        exit;
    }

    $imageName = time() . '_' . sanitizeFilename($serviceImage['name']);
    $uploadDir = PUBLIC_PATH . '/images/uploads';

    $imgResult = processUploadedImage(
        $serviceImage['tmp_name'],
        $uploadDir,
        $imageName,
        ['maxWidth' => 1600, 'thumbW' => 400, 'thumbH' => 225, 'quality' => 85, 'makeWebp' => true]
    );

    if ($imgResult === false) {
        if (!move_uploaded_file($serviceImage['tmp_name'], $uploadDir . '/' . $imageName)) {
            flashSet('error', 'Görsel yüklenemedi.');
            header('Location: ' . ROOT_URL . 'admin/services/create');
            exit;
        }
    }
}

// ── Kaydet ────────────────────────────────────────────────────
$slug = generateUniqueSlug($connection, $title, 'services');

$stmt = $connection->prepare("
    INSERT INTO services
        (slug, title, summary, content, icon_class, image, display_order, is_active, meta_title, meta_desc)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'ssssssisis',
    $slug, $title, $summary, $content, $iconClass, $imageName,
    $displayOrder, $isActive, $metaTitle, $metaDesc
);

if (!$stmt->execute()) {
    flashSet('error', 'Hizmet eklenemedi: ' . $stmt->error);
    header('Location: ' . ROOT_URL . 'admin/services/create');
    exit;
}

logAudit('create', 'services', (int)$connection->insert_id, null, ['title' => $title]);
flashSet('success', '"' . $title . '" hizmeti eklendi.');
header('Location: ' . ROOT_URL . 'admin/services');
exit;
