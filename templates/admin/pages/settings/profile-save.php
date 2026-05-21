<?php
/**
 * templates/admin/pages/settings/profile-save.php
 * POST /admin/profile
 *
 * Admin kullanıcısının kendi profilini güncellemesi.
 * Ad, soyad, email, username, avatar, şifre değişimi.
 */

require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/sanitize.php';
require_once BASE_PATH . '/app/helpers/image.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/settings');
    exit;
}

csrfVerify();

$adminId   = (int) $_SESSION['admin_id'];
$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name']  ?? '');
$email     = trim($_POST['email']      ?? '');
$username  = trim($_POST['username']   ?? '');

// ── Validation ──────────────────────────────────────────────────
if ($firstName === '' || $lastName === '') {
    flashSet('error', 'Ad ve soyad zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/settings#profile');
    exit;
}

if ($username === '' || strlen($username) < 3) {
    flashSet('error', 'Kullanıcı adı en az 3 karakter olmalıdır.');
    header('Location: ' . ROOT_URL . 'admin/settings#profile');
    exit;
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flashSet('error', 'Geçerli bir e-posta adresi girin.');
    header('Location: ' . ROOT_URL . 'admin/settings#profile');
    exit;
}

// ── Uniqueness kontrolü (username/email başka admin'de var mı) ──
$chk = $connection->prepare(
    "SELECT id FROM admins WHERE (username = ? OR email = ?) AND id != ? LIMIT 1"
);
$chk->bind_param('ssi', $username, $email, $adminId);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    flashSet('error', 'Bu kullanıcı adı veya e-posta zaten kullanımda.');
    header('Location: ' . ROOT_URL . 'admin/settings#profile');
    exit;
}

// ── Mevcut veriyi al ────────────────────────────────────────────
$curStmt = $connection->prepare("SELECT avatar FROM admins WHERE id = ? LIMIT 1");
$curStmt->bind_param('i', $adminId);
$curStmt->execute();
$currentAdmin = $curStmt->get_result()->fetch_assoc();
$currentAvatar = $currentAdmin['avatar'] ?? 'default.png';

// ── Avatar upload ───────────────────────────────────────────────
$avatarToSave = $currentAvatar;
$avatarFile   = $_FILES['avatar'] ?? null;

if ($avatarFile && !empty($avatarFile['name']) && $avatarFile['error'] === UPLOAD_ERR_OK) {

    $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
    $ext         = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));
    $finfo       = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType    = finfo_file($finfo, $avatarFile['tmp_name']);
    finfo_close($finfo);
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($ext, $allowedExt, true) || !in_array($mimeType, $allowedMime, true)) {
        flashSet('error', 'Avatar formatı jpg, png veya webp olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/settings#profile');
        exit;
    }

    if ($avatarFile['size'] > 2 * 1024 * 1024) {
        flashSet('error', 'Avatar boyutu 2 MB\'dan küçük olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/settings#profile');
        exit;
    }

    $avatarName = 'avatar_' . $adminId . '_' . time() . '.' . $ext;
    $uploadDir  = PUBLIC_PATH . '/images/uploads';

    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // Resize (150x150 crop)
    $imgResult = processUploadedImage(
        $avatarFile['tmp_name'],
        $uploadDir,
        $avatarName,
        ['maxWidth' => 400, 'thumbW' => 150, 'thumbH' => 150, 'quality' => 90, 'makeWebp' => false]
    );

    if ($imgResult === false) {
        // GD yoksa basit move
        if (!move_uploaded_file($avatarFile['tmp_name'], $uploadDir . '/' . $avatarName)) {
            flashSet('error', 'Avatar yüklenemedi.');
            header('Location: ' . ROOT_URL . 'admin/settings#profile');
            exit;
        }
    }

    $avatarToSave = $avatarName;

    // Eski avatarı sil (default.png hariç)
    if ($currentAvatar !== 'default.png' && $currentAvatar !== '') {
        $oldPath = $uploadDir . '/' . $currentAvatar;
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }
}

// ── Şifre değişimi (opsiyonel) ──────────────────────────────────
$currentPassword = trim($_POST['current_password'] ?? '');
$newPassword     = trim($_POST['new_password']     ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

$passwordSql     = '';
$passwordChanged = false;

if ($newPassword !== '') {
    if ($currentPassword === '') {
        flashSet('error', 'Şifre değiştirmek için mevcut şifrenizi girin.');
        header('Location: ' . ROOT_URL . 'admin/settings#profile');
        exit;
    }

    // Mevcut şifre doğrulama
    $pwStmt = $connection->prepare("SELECT password FROM admins WHERE id = ? LIMIT 1");
    $pwStmt->bind_param('i', $adminId);
    $pwStmt->execute();
    $pwRow = $pwStmt->get_result()->fetch_assoc();

    if (!password_verify($currentPassword, $pwRow['password'])) {
        flashSet('error', 'Mevcut şifre hatalı.');
        header('Location: ' . ROOT_URL . 'admin/settings#profile');
        exit;
    }

    if (strlen($newPassword) < 6) {
        flashSet('error', 'Yeni şifre en az 6 karakter olmalıdır.');
        header('Location: ' . ROOT_URL . 'admin/settings#profile');
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        flashSet('error', 'Yeni şifre ve tekrarı eşleşmiyor.');
        header('Location: ' . ROOT_URL . 'admin/settings#profile');
        exit;
    }

    $passwordChanged = true;
}

// ── UPDATE ──────────────────────────────────────────────────────
if ($passwordChanged) {
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $connection->prepare("
        UPDATE admins
        SET first_name = ?, last_name = ?, email = ?, username = ?, avatar = ?, password = ?
        WHERE id = ? LIMIT 1
    ");
    $stmt->bind_param('ssssssi',
        $firstName, $lastName, $email, $username, $avatarToSave, $hashedPassword, $adminId
    );
} else {
    $stmt = $connection->prepare("
        UPDATE admins
        SET first_name = ?, last_name = ?, email = ?, username = ?, avatar = ?
        WHERE id = ? LIMIT 1
    ");
    $stmt->bind_param('sssssi',
        $firstName, $lastName, $email, $username, $avatarToSave, $adminId
    );
}

if (!$stmt->execute()) {
    flashSet('error', 'Profil güncellenemedi: ' . $stmt->error);
    header('Location: ' . ROOT_URL . 'admin/settings#profile');
    exit;
}

// ── Session güncelle ────────────────────────────────────────────
$_SESSION['admin_username']   = $username;
$_SESSION['admin_first_name'] = $firstName;
$_SESSION['admin_last_name']  = $lastName;
$_SESSION['admin_avatar']     = $avatarToSave;

logAudit('update', 'admins', $adminId, null, [
    'first_name' => $firstName,
    'last_name'  => $lastName,
    'email'      => $email,
    'avatar'     => $avatarToSave,
    'password_changed' => $passwordChanged,
]);

$msg = 'Profil başarıyla güncellendi.';
if ($passwordChanged) {
    $msg .= ' Şifreniz de değiştirildi.';
}

flashSet('success', $msg);
header('Location: ' . ROOT_URL . 'admin/settings#profile');
exit;
