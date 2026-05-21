<?php
/**
 * templates/admin/pages/posts/create-submit.php
 * POST /admin/posts/create
 *
 * BUG FIX:
 *  - compact() içindeki key'ler camelCase değişken adlarıyla eşleşmiyordu → manual array
 *  - published_at MySQL DATETIME formatında üretiliyor
 *  - bind_param type string doğrulandı
 *  - Validator hata sonrası redirect eklendi
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/validator.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/slug.php';
require_once BASE_PATH . '/app/helpers/image.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

csrfVerify();

$authorId            = (int) $_SESSION['admin_id'];
$title               = trim($_POST['title']                ?? '');
$body                = trim($_POST['body']                 ?? '');
$categoryId          = (int) ($_POST['category_id']        ?? 0);
$relatedServiceSlug  = trim($_POST['related_service_slug'] ?? '');
$isPublished         = isset($_POST['is_published']) ? 1 : 0;
$isFeatured          = isset($_POST['is_featured'])  ? 1 : 0;
$metaTitle           = trim($_POST['meta_title']           ?? '');
$metaDesc            = trim($_POST['meta_desc']            ?? '');
$thumbnail           = $_FILES['thumbnail'] ?? null;

// ── old_input: form field name'leri ile eşleşmeli ───────────────
// NOT: Eski kod compact('category_id', ...) kullanıyordu ama
// değişken adları $categoryId (camelCase) olduğu için
// $category_id tanımsız kalıp warning üretiyordu.
$_SESSION['old_input'] = [
    'title'                => $title,
    'body'                 => $body,
    'category_id'          => $categoryId,
    'related_service_slug' => $relatedServiceSlug,
    'is_published'         => $isPublished,
    'is_featured'          => $isFeatured,
    'meta_title'           => $metaTitle,
    'meta_desc'            => $metaDesc,
];

// ── Validation ──────────────────────────────────────────────────
$v = new Validator($_POST);
$v->required('title', 'Başlık')
  ->maxLength('title', 255, 'Başlık');

if ($v->fails()) {
    flashSet('error', $v->firstError());
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

if ($categoryId <= 0) {
    flashSet('error', 'Kategori seçmelisiniz.');
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

if (trim(strip_tags($body)) === '') {
    flashSet('error', 'Yazı içeriği zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

if (!$thumbnail || empty($thumbnail['name'])) {
    flashSet('error', 'Kapak görseli seçmelisiniz.');
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

// ── Görsel doğrulama ────────────────────────────────────────────
$allowedExt  = ['jpg', 'jpeg', 'png'];
$ext         = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
$allowedMime = ['image/jpeg', 'image/jpg', 'image/png'];
$finfo       = finfo_open(FILEINFO_MIME_TYPE);
$mimeType    = finfo_file($finfo, $thumbnail['tmp_name']);
finfo_close($finfo);

if (!in_array($ext, $allowedExt, true) || !in_array($mimeType, $allowedMime, true)) {
    flashSet('error', 'Görsel formatı jpg, jpeg veya png olmalıdır.');
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

if ($thumbnail['size'] > 2 * 1024 * 1024) {
    flashSet('error', 'Görsel boyutu 2 MB\'dan küçük olmalıdır.');
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

// ── Görsel kaydet + pipeline ────────────────────────────────────
$thumbnailName = time() . '_' . sanitizeFilename($thumbnail['name']);
$uploadDir     = PUBLIC_PATH . '/images/uploads';

$imgResult = processUploadedImage(
    $thumbnail['tmp_name'],
    $uploadDir,
    $thumbnailName,
    ['maxWidth' => 1600, 'thumbW' => 400, 'thumbH' => 225, 'quality' => 85, 'makeWebp' => true]
);

if ($imgResult === false) {
    $uploadPath = $uploadDir . '/' . $thumbnailName;
    if (!move_uploaded_file($thumbnail['tmp_name'], $uploadPath)) {
        flashSet('error', 'Görsel yüklenemedi.');
        header('Location: ' . ROOT_URL . 'admin/posts/create');
        exit;
    }
}

// ── Veri hazırlığı ──────────────────────────────────────────────
$body                = sanitizeHtml($body);
$slug                = generateUniqueSlug($connection, $title, 'posts');
$relatedServiceVal   = $relatedServiceSlug !== '' ? $relatedServiceSlug : null;

// published_at: MySQL DATETIME formatında üret
// DB: published_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
// Yayınlanıyorsa şu anki tarih-saat, yayınlanmıyorsa null
// (null gidince DB default CURRENT_TIMESTAMP devreye girer)
$publishedAt = $isPublished ? date('Y-m-d H:i:s') : null;

// Öne çıkar: sadece bir yazı featured olabilir
if ($isFeatured === 1) {
    $connection->query("UPDATE posts SET is_featured = 0");
}

// ── INSERT ──────────────────────────────────────────────────────
$stmt = $connection->prepare("
    INSERT INTO posts
        (title, slug, body, thumbnail, category_id, author_id,
         is_published, is_featured, published_at,
         meta_title, meta_desc, related_service_slug)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// Type string doğrulama (HER POZİSYON TEK TEK):
//  1: title(s)  2: slug(s)  3: body(s)  4: thumbnail(s)     = ssss
//  5: category_id(i)  6: author_id(i)                        = ii
//  7: is_published(i)  8: is_featured(i)                     = ii
//  9: published_at(s) ← STRING! date() string döner          = s
// 10: meta_title(s)  11: meta_desc(s)  12: related_service(s)= sss
// Toplam: ssss + ii + ii + s + sss = ssssiiiissss  (12 karakter)
//
// ÖNCEKİ HATA: 9. pozisyon 'i' idi → date('Y-m-d H:i:s') int'e cast
// edilip '2026' oluyordu → MySQL "Incorrect datetime value: '2026'" fatal
$stmt->bind_param(
    'ssssiiiissss',
    $title, $slug, $body, $thumbnailName,
    $categoryId, $authorId,
    $isPublished, $isFeatured, $publishedAt,
    $metaTitle, $metaDesc, $relatedServiceVal
);

if (!$stmt->execute()) {
    flashSet('error', 'Yazı eklenemedi: ' . $stmt->error);
    header('Location: ' . ROOT_URL . 'admin/posts/create');
    exit;
}

$postId = (int) $connection->insert_id;
logAudit('create', 'posts', $postId, null, ['title' => $title]);

unset($_SESSION['old_input']);
flashSet('success', '"' . $title . '" yazısı başarıyla eklendi.');
header('Location: ' . ROOT_URL . 'admin/posts');
exit;
