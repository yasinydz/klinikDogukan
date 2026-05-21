<?php
/**
 * templates/admin/pages/posts/edit-submit.php
 * POST /admin/posts/edit
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/validator.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/slug.php';
require_once BASE_PATH . '/app/helpers/image.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

csrfVerify();

$id                  = (int)  ($_POST['id']                    ?? 0);
$title               = trim(  $_POST['title']                  ?? '');
$body                = trim(  $_POST['body']                   ?? '');
$previousThumbnail   = trim(  $_POST['previous_thumbnail']     ?? '');
$categoryId          = (int)  ($_POST['category_id']           ?? 0);
$relatedServiceSlug  = trim(  $_POST['related_service_slug']   ?? '');
$isPublished         = isset($_POST['is_published']) ? 1 : 0;
$isFeatured          = isset($_POST['is_featured'])  ? 1 : 0;
$metaTitle           = trim(  $_POST['meta_title']             ?? '');
$metaDesc            = trim(  $_POST['meta_desc']              ?? '');
$thumbnail           = $_FILES['thumbnail'] ?? null;

if ($id <= 0) {
    flashSet('error', 'Geçersiz yazı.');
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

if ($title === '') {
    flashSet('error', 'Başlık zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/posts/edit?id=' . $id);
    exit;
}

if ($categoryId <= 0) {
    flashSet('error', 'Kategori seçmelisiniz.');
    header('Location: ' . ROOT_URL . 'admin/posts/edit?id=' . $id);
    exit;
}

if (trim(strip_tags($body)) === '') {
    flashSet('error', 'Yazı içeriği zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/posts/edit?id=' . $id);
    exit;
}

// Görsel işleme
$thumbnailToSave = $previousThumbnail;

if ($thumbnail && !empty($thumbnail['name'])) {
    $allowedExt  = ['jpg', 'jpeg', 'png'];
    $ext         = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
    $finfo       = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType    = finfo_file($finfo, $thumbnail['tmp_name']);
    finfo_close($finfo);
    $allowedMime = ['image/jpeg', 'image/jpg', 'image/png'];

    if (!in_array($ext, $allowedExt, true) || !in_array($mimeType, $allowedMime, true)) {
        flashSet('error', 'Görsel formatı jpg, jpeg veya png olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/posts/edit?id=' . $id);
        exit;
    }

    if ($thumbnail['size'] > 2 * 1024 * 1024) {
        flashSet('error', 'Görsel boyutu 2 MB\'dan küçük olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/posts/edit?id=' . $id);
        exit;
    }

    $newName   = time() . '_' . sanitizeFilename($thumbnail['name']);
    $uploadDir = PUBLIC_PATH . '/images/uploads';

    $imgResult = processUploadedImage(
        $thumbnail['tmp_name'],
        $uploadDir,
        $newName,
        ['maxWidth' => 1600, 'thumbW' => 400, 'thumbH' => 225, 'quality' => 85, 'makeWebp' => true]
    );

    if ($imgResult === false) {
        if (!move_uploaded_file($thumbnail['tmp_name'], $uploadDir . '/' . $newName)) {
            flashSet('error', 'Görsel yüklenemedi.');
            header('Location: ' . ROOT_URL . 'admin/posts/edit?id=' . $id);
            exit;
        }
    }

    $thumbnailToSave = $newName;
}

$body               = sanitizeHtml($body);
$slug               = generateUniqueSlug($connection, $title, 'posts', $id);
$relatedServiceVal  = $relatedServiceSlug !== '' ? $relatedServiceSlug : null;
$publishedAt        = $isPublished ? date('Y-m-d H:i:s') : null;

// Mevcut veriyi audit için al + eski slug'ı kaydet
$oldStmt = $connection->prepare("SELECT title, slug, is_published FROM posts WHERE id = ? LIMIT 1");
$oldStmt->bind_param('i', $id);
$oldStmt->execute();
$oldData = $oldStmt->get_result()->fetch_assoc();
$oldSlug = $oldData['slug'] ?? '';

// Slug değiştiyse eski URL için 301 redirect oluştur
if ($oldSlug !== '' && $oldSlug !== $slug) {
    $fromUrl = '/blog/' . $oldSlug;
    $toUrl   = '/blog/' . $slug;

    // Loop önleme: yeni slug'dan eski slug'a giden redirect varsa deaktive et
    $rLoop = $connection->prepare(
        "UPDATE redirects SET is_active = 0 WHERE from_url = ? AND to_url = ?"
    );
    if ($rLoop) {
        $rLoop->bind_param('ss', $toUrl, $fromUrl);
        $rLoop->execute();
        $rLoop->close();
    }

    // Zincir düzleştirme: X -> oldSlug olan redirect'leri X -> newSlug yap
    $rChain = $connection->prepare(
        "UPDATE redirects SET to_url = ? WHERE to_url = ? AND is_active = 1"
    );
    if ($rChain) {
        $rChain->bind_param('ss', $toUrl, $fromUrl);
        $rChain->execute();
        $rChain->close();
    }

    // Aynı from_url zaten varsa güncelle, yoksa ekle
    $rCheck = $connection->prepare(
        "SELECT id FROM redirects WHERE from_url = ? LIMIT 1"
    );
    if ($rCheck) {
        $rCheck->bind_param('s', $fromUrl);
        $rCheck->execute();
        $rExisting = $rCheck->get_result()->fetch_assoc();
        $rCheck->close();

        if ($rExisting) {
            $rUpd = $connection->prepare(
                "UPDATE redirects SET to_url = ?, is_active = 1 WHERE id = ?"
            );
            $rUpd->bind_param('si', $toUrl, $rExisting['id']);
            $rUpd->execute();
            $rUpd->close();
        } else {
            $rIns = $connection->prepare(
                "INSERT INTO redirects (from_url, to_url, redirect_type, is_active) VALUES (?, ?, '301', 1)"
            );
            $rIns->bind_param('ss', $fromUrl, $toUrl);
            $rIns->execute();
            $rIns->close();
        }
    }
}

if ($isFeatured === 1) {
    $rs = $connection->prepare("UPDATE posts SET is_featured = 0 WHERE id != ?");
    $rs->bind_param('i', $id);
    $rs->execute();
}

$stmt = $connection->prepare("
    UPDATE posts
    SET title = ?, slug = ?, body = ?, thumbnail = ?,
        category_id = ?, is_published = ?, is_featured = ?,
        published_at = COALESCE(published_at, ?),
        meta_title = ?, meta_desc = ?, related_service_slug = ?,
        updated_at = NOW()
    WHERE id = ? LIMIT 1
");
$stmt->bind_param(
    'ssssiiissssi',
    $title, $slug, $body, $thumbnailToSave,
    $categoryId, $isPublished, $isFeatured,
    $publishedAt,
    $metaTitle, $metaDesc, $relatedServiceVal,
    $id
);

if (!$stmt->execute()) {
    flashSet('error', 'Yazı güncellenemedi: ' . $stmt->error);
    header('Location: ' . ROOT_URL . 'admin/posts/edit?id=' . $id);
    exit;
}

// Eski görseli ve tüm varyantlarını sil
if ($thumbnailToSave !== $previousThumbnail && $previousThumbnail !== '') {
    deleteImageVariants($previousThumbnail);
}

logAudit('update', 'posts', $id,
    ['title' => $oldData['title'] ?? ''],
    ['title' => $title]
);

flashSet('success', '"' . $title . '" yazısı güncellendi.');
header('Location: ' . ROOT_URL . 'admin/posts');
exit;
