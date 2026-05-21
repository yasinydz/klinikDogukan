<?php
/**
 * app/middleware/auth.php
 *
 * Admin kimlik doğrulama ve yetkilendirme.
 * config/auth.php'nin yerini alır.
 */

if (!defined('ROOT_URL')) {
    require_once dirname(__DIR__, 2) . '/config/app.php';
}

// ── Oturum Kontrol Fonksiyonları ──────────────────────────────

/**
 * Admin olarak giriş yapılmış mı?
 */
function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_id'], $_SESSION['admin_logged_in'])
        && $_SESSION['admin_logged_in'] === true
        && !empty($_SESSION['admin_id']);
}

/**
 * Admin girişi zorunlu kılar.
 * Giriş yapılmamışsa login sayfasına yönlendirir.
 */
function requireAdminAuth(): void
{
    if (!isAdminLoggedIn()) {
        flashSet('error', 'Bu sayfaya erişmek için giriş yapmalısınız.');
        header('Location: ' . ROOT_URL . 'admin/giris');
        exit;
    }

    // Oturum süresi kontrolü
    $sessionLifetime = (int) env('SESSION_LIFETIME', 7200);
    $lastActivity    = $_SESSION['last_activity'] ?? 0;

    if (time() - $lastActivity > $sessionLifetime) {
        adminLogout();
        flashSet('error', 'Oturumunuz sona erdi. Lütfen tekrar giriş yapın.');
        header('Location: ' . ROOT_URL . 'admin/giris');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

/**
 * Giriş yapmış admin'i admin paneline yönlendirir.
 * Login sayfaları için kullanılır.
 */
function requireAdminGuest(): void
{
    if (isAdminLoggedIn()) {
        header('Location: ' . ROOT_URL . 'admin');
        exit;
    }
}

/**
 * Admin oturumunu güvenli şekilde sonlandırır.
 */
function adminLogout(): void
{
    // Audit log kaydı
    if (!empty($_SESSION['admin_id'])) {
        logAudit('logout', 'admins', (int) $_SESSION['admin_id']);
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

// ── Audit Log ─────────────────────────────────────────────────

/**
 * Admin işlemlerini audit_logs tablosuna kaydeder.
 *
 * @param string     $action     'login' | 'logout' | 'create' | 'update' | 'delete'
 * @param string     $entityType 'posts' | 'services' | vb.
 * @param int        $entityId
 * @param array|null $oldValue
 * @param array|null $newValue
 */
function logAudit(
    string  $action,
    string  $entityType,
    int     $entityId  = 0,
    ?array  $oldValue  = null,
    ?array  $newValue  = null
): void {
    global $connection;

    if (!$connection instanceof mysqli) {
        return;
    }

    $adminId   = (int) ($_SESSION['admin_id'] ?? 0);
    $ip        = $_SERVER['REMOTE_ADDR']     ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $oldJson   = $oldValue !== null ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : null;
    $newJson   = $newValue !== null ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : null;

    $stmt = $connection->prepare("
        INSERT INTO audit_logs
            (admin_id, action, entity_type, entity_id, old_value, new_value, ip, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        return;
    }

    $stmt->bind_param(
        'ississss',
        $adminId, $action, $entityType, $entityId,
        $oldJson, $newJson, $ip, $userAgent
    );

    $stmt->execute();
    $stmt->close();
}

// ── Eski Sistem Uyumluluğu ────────────────────────────────────
// Mevcut kod geçiş dönemi için — yeni kod yukarıdaki fonksiyonları kullanmalı

function adminGirisYapmisMi(): bool
{
    return isAdminLoggedIn();
}

function adminGirisZorunlu(): void
{
    requireAdminAuth();
}

function adminMisafirOlmali(): void
{
    requireAdminGuest();
}
