<?php
/**
 * routes/admin.php
 *
 * Admin panel route tanımları.
 * public/index.php tarafından çağrılır.
 *
 * $segment0 = 'admin'
 * $segment1 = modül (posts, services, vb.)
 * $segment2 = aksiyon (create, edit, delete, vb.)
 */

$adminPagesPath = BASE_PATH . '/templates/admin/pages';

// ── Admin Login (Giriş yapmadan erişilebilir) ─────────────────
if ($segment1 === 'giris') {
    require_once BASE_PATH . '/app/middleware/auth.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require $adminPagesPath . '/login-submit.php';
    } else {
        requireAdminGuest();
        require $adminPagesPath . '/login.php';
    }
    exit;
}

// Çıkış
if ($segment1 === 'cikis') {
    require_once BASE_PATH . '/app/middleware/auth.php';
    adminLogout();
    header('Location: ' . ROOT_URL . 'admin/giris');
    exit;
}

// ── Tüm diğer admin route'ları için auth zorunlu ──────────────
requireAdminAuth();

// ── Route Eşleme ──────────────────────────────────────────────

switch (true) {

    // Dashboard — GET /admin  veya  GET /admin/dashboard
    case $segment1 === '' || $segment1 === 'dashboard':
        require $adminPagesPath . '/dashboard.php';
        break;

    // ── Yazılar ───────────────────────────────────────────────

    case $segment1 === 'posts' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/posts/index.php';
        break;

    case $segment1 === 'posts' && $segment2 === 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/posts/create-submit.php';
        } else {
            require $adminPagesPath . '/posts/create.php';
        }
        break;

    case $segment1 === 'posts' && $segment2 === 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/posts/edit-submit.php';
        } else {
            require $adminPagesPath . '/posts/edit.php';
        }
        break;

    case $segment1 === 'posts' && $segment2 === 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: ' . ROOT_URL . 'admin/posts');
            exit;
        }
        require $adminPagesPath . '/posts/delete.php';
        break;

    // ── Kategoriler ───────────────────────────────────────────

    case $segment1 === 'categories' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/categories/index.php';
        break;

    case $segment1 === 'categories' && $segment2 === 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/categories/create-submit.php';
        } else {
            require $adminPagesPath . '/categories/create.php';
        }
        break;

    case $segment1 === 'categories' && $segment2 === 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/categories/edit-submit.php';
        } else {
            require $adminPagesPath . '/categories/edit.php';
        }
        break;

    case $segment1 === 'categories' && $segment2 === 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: ' . ROOT_URL . 'admin/categories');
            exit;
        }
        require $adminPagesPath . '/categories/delete.php';
        break;

    // ── Hizmetler ─────────────────────────────────────────────

    case $segment1 === 'services' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/services/index.php';
        break;

    case $segment1 === 'services' && $segment2 === 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/services/create-submit.php';
        } else {
            require $adminPagesPath . '/services/create.php';
        }
        break;

    case $segment1 === 'services' && $segment2 === 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/services/edit-submit.php';
        } else {
            require $adminPagesPath . '/services/edit.php';
        }
        break;

    case $segment1 === 'services' && $segment2 === 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: ' . ROOT_URL . 'admin/services');
            exit;
        }
        require $adminPagesPath . '/services/delete.php';
        break;

    // ── Randevular ────────────────────────────────────────────

    case $segment1 === 'appointments' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/appointments/index.php';
        break;

    case $segment1 === 'appointments' && $segment2 === 'view':
        require $adminPagesPath . '/appointments/view.php';
        break;

    case $segment1 === 'appointments' && $segment2 === 'update-status':
        require $adminPagesPath . '/appointments/update-status.php';
        break;

    // ── Slotlar ───────────────────────────────────────────────

    case $segment1 === 'slots' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/slots/index.php';
        break;

    case $segment1 === 'slots' && $segment2 === 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/slots/create-submit.php';
        } else {
            require $adminPagesPath . '/slots/create.php';
        }
        break;

    case $segment1 === 'slots' && $segment2 === 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: ' . ROOT_URL . 'admin/slots');
            exit;
        }
        require $adminPagesPath . '/slots/delete.php';
        break;

    // ── Mesajlar ──────────────────────────────────────────────

    case $segment1 === 'messages' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/messages/index.php';
        break;

    case $segment1 === 'messages' && $segment2 === 'view':
        require $adminPagesPath . '/messages/view.php';
        break;

    // ── Şehir Sayfaları ───────────────────────────────────────

    case $segment1 === 'city-pages':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/city-pages/save.php';
        } else {
            require $adminPagesPath . '/city-pages/index.php';
        }
        break;

    // ── Hukuki Sayfalar ───────────────────────────────────────

    case $segment1 === 'legal':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/legal/save.php';
        } else {
            require $adminPagesPath . '/legal/index.php';
        }
        break;

    // ── Profil ──────────────────────────────────────────────────

    case $segment1 === 'profile' && $_SERVER['REQUEST_METHOD'] === 'POST':
        require $adminPagesPath . '/settings/profile-save.php';
        break;

    // ── Ayarlar ───────────────────────────────────────────────

    case $segment1 === 'settings':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/settings/save.php';
        } else {
            require $adminPagesPath . '/settings/index.php';
        }
        break;

    // ── Audit Log ─────────────────────────────────────────────

    case $segment1 === 'audit-log':
        require $adminPagesPath . '/audit-log/index.php';
        break;

    // ── Galeri ────────────────────────────────────────────────

    case $segment1 === 'gallery' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/gallery/index.php';
        break;

    case $segment1 === 'gallery' && $segment2 === 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/gallery/create-submit.php';
        } else {
            require $adminPagesPath . '/gallery/create.php';
        }
        break;

    case $segment1 === 'gallery' && $segment2 === 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/gallery/edit-submit.php';
        } else {
            require $adminPagesPath . '/gallery/edit.php';
        }
        break;

    case $segment1 === 'gallery' && $segment2 === 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: ' . ROOT_URL . 'admin/gallery');
            exit;
        }
        require $adminPagesPath . '/gallery/delete.php';
        break;

    // ── Testimonials ──────────────────────────────────────────

    case $segment1 === 'testimonials' && ($segment2 === '' || $segment2 === 'index'):
        require $adminPagesPath . '/testimonials/index.php';
        break;

    case $segment1 === 'testimonials' && $segment2 === 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/testimonials/create-submit.php';
        } else {
            require $adminPagesPath . '/testimonials/create.php';
        }
        break;

    case $segment1 === 'testimonials' && $segment2 === 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/testimonials/edit-submit.php';
        } else {
            require $adminPagesPath . '/testimonials/edit.php';
        }
        break;

    case $segment1 === 'testimonials' && $segment2 === 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: ' . ROOT_URL . 'admin/testimonials');
            exit;
        }
        require $adminPagesPath . '/testimonials/delete.php';
        break;

    // ── Local Trust / GBP Ayarları ────────────────────────────

    case $segment1 === 'local-trust':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require $adminPagesPath . '/local-trust/save.php';
        } else {
            require $adminPagesPath . '/local-trust/index.php';
        }
        break;

    // ── 404 ───────────────────────────────────────────────────

    default:
        http_response_code(404);
        require BASE_PATH . '/templates/pages/404.php';
        break;
}
