<?php
/**
 * templates/admin/pages/login-submit.php
 * POST /admin/giris
 */

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/rate_limiter.php';
require_once BASE_PATH . '/app/middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'admin/giris');
    exit;
}

csrfVerify();

// Brute force koruması
rateLimitCheck('admin_login', RATE_LIMIT_LOGIN, RATE_LIMIT_WINDOW);

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$_SESSION['old_input'] = ['username' => $username];

if ($username === '' || $password === '') {
    flashSet('error', 'Kullanıcı adı ve şifre zorunludur.');
    header('Location: ' . ROOT_URL . 'admin/giris');
    exit;
}

$stmt = $connection->prepare(
    "SELECT id, username, password, is_active, first_name, last_name, avatar FROM admins WHERE username = ? LIMIT 1"
);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

// Kullanıcı bulunamadı — zamanlama saldırısını önlemek için yine de hash doğrulama yap
$dummyHash = '$2y$10$usesomesillystringhere/uDDmz8lmk8Ux8OHPVd1j0PoQ0Xge';
$user      = $result->num_rows === 1 ? $result->fetch_assoc() : null;
$hash      = $user['password'] ?? $dummyHash;

if (!password_verify($password, $hash) || $user === null) {
    // IP + tarih bazlı log
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $log = date('Y-m-d H:i:s') . " LOGIN_FAIL ip={$ip} user={$username}" . PHP_EOL;
    @file_put_contents(
        BASE_PATH . '/storage/logs/app.log',
        $log,
        FILE_APPEND | LOCK_EX
    );

    flashSet('error', 'Kullanıcı adı veya şifre hatalı.');
    header('Location: ' . ROOT_URL . 'admin/giris');
    exit;
}

if (!(bool) $user['is_active']) {
    flashSet('error', 'Hesabınız devre dışı bırakılmış.');
    header('Location: ' . ROOT_URL . 'admin/giris');
    exit;
}

// Başarılı giriş
session_regenerate_id(true);

$_SESSION['admin_id']         = (int) $user['id'];
$_SESSION['admin_username']   = $user['username'];
$_SESSION['admin_first_name'] = $user['first_name'] ?? '';
$_SESSION['admin_last_name']  = $user['last_name']  ?? '';
$_SESSION['admin_avatar']     = $user['avatar']     ?? 'default.png';
$_SESSION['admin_logged_in']  = true;
$_SESSION['last_activity']    = time();

// Son giriş zamanını güncelle
$upd = $connection->prepare("UPDATE admins SET last_login_at = NOW() WHERE id = ?");
$upd->bind_param('i', $user['id']);
$upd->execute();

// Audit log
logAudit('login', 'admins', (int) $user['id']);

// Rate limit sayacını sıfırla
RateLimiter::clear('admin_login');

unset($_SESSION['old_input']);

flashSet('success', 'Giriş başarılı. Hoş geldiniz, ' . e($user['username']) . '!');
header('Location: ' . ROOT_URL . 'admin');
exit;
