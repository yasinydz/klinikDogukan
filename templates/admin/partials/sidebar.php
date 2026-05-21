<?php
/**
 * templates/admin/partials/sidebar.php
 *
 * Admin panel sidebar navigasyon.
 * $activeMenu değişkeni include eden sayfadan gelir.
 */

$activeMenu = $activeMenu ?? '';

// Okunmamış mesaj sayısı
$unreadMessages = 0;
$checkMessages  = mysqli_query($connection, "SHOW TABLES LIKE 'contact_messages'");
if ($checkMessages && mysqli_num_rows($checkMessages) > 0) {
    $result = mysqli_query(
        $connection,
        "SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0 AND is_deleted = 0"
    );
    $unreadMessages = (int) (mysqli_fetch_assoc($result)['c'] ?? 0);
}

// Okunmamış randevu sayısı
$unreadAppointments = 0;
$checkAppointments  = mysqli_query($connection, "SHOW TABLES LIKE 'appointments'");
if ($checkAppointments && mysqli_num_rows($checkAppointments) > 0) {
    $result = mysqli_query(
        $connection,
        "SELECT COUNT(*) AS c FROM appointments WHERE status = 'pending'"
    );
    $unreadAppointments = (int) (mysqli_fetch_assoc($result)['c'] ?? 0);
}

$navItems = [
    'dashboard'    => [
        'url'   => ROOT_URL . 'admin',
        'icon'  => 'uil-dashboard',
        'label' => 'Dashboard',
    ],
    'posts'        => [
        'url'   => ROOT_URL . 'admin/posts',
        'icon'  => 'uil-postcard',
        'label' => 'Yazılar',
    ],
    'categories'   => [
        'url'   => ROOT_URL . 'admin/categories',
        'icon'  => 'uil-folder',
        'label' => 'Kategoriler',
    ],
    'services'     => [
        'url'   => ROOT_URL . 'admin/services',
        'icon'  => 'uil-heart-rate',
        'label' => 'Hizmetler',
    ],
    'appointments' => [
        'url'   => ROOT_URL . 'admin/appointments',
        'icon'  => 'uil-calendar-alt',
        'label' => 'Randevular',
        'badge' => $unreadAppointments,
    ],
    'slots'        => [
        'url'   => ROOT_URL . 'admin/slots',
        'icon'  => 'uil-clock',
        'label' => 'Slotlar',
    ],
    'messages'     => [
        'url'   => ROOT_URL . 'admin/messages',
        'icon'  => 'uil-envelope',
        'label' => 'Mesajlar',
        'badge' => $unreadMessages,
    ],
    'city-pages'   => [
        'url'   => ROOT_URL . 'admin/city-pages',
        'icon'  => 'uil-map-marker',
        'label' => 'Şehir Sayfaları',
    ],
    'gallery'      => [
        'url'   => ROOT_URL . 'admin/gallery',
        'icon'  => 'uil-image',
        'label' => 'Galeri',
    ],
    'testimonials' => [
        'url'   => ROOT_URL . 'admin/testimonials',
        'icon'  => 'uil-comment-alt-lines',
        'label' => 'Yorumlar',
    ],
    'local-trust'  => [
        'url'   => ROOT_URL . 'admin/local-trust',
        'icon'  => 'uil-map-pin-alt',
        'label' => 'Konum & GBP',
    ],
    'legal'        => [
        'url'   => ROOT_URL . 'admin/legal',
        'icon'  => 'uil-file-shield-alt',
        'label' => 'Hukuki Metinler',
    ],
    'settings'     => [
        'url'   => ROOT_URL . 'admin/settings',
        'icon'  => 'uil-setting',
        'label' => 'Ayarlar',
    ],
    'audit-log'    => [
        'url'   => ROOT_URL . 'admin/audit-log',
        'icon'  => 'uil-history',
        'label' => 'İşlem Geçmişi',
    ],
];
?>

<!-- Sidebar Toggle Butonları -->
<button id="show__sidebar-btn"
        class="sidebar__toggle"
        type="button"
        aria-label="Menüyü aç">
    <i class="uil uil-angle-right-b" aria-hidden="true"></i>
</button>
<button id="hide__sidebar-btn"
        class="sidebar__toggle"
        type="button"
        aria-label="Menüyü kapat">
    <i class="uil uil-angle-left-b" aria-hidden="true"></i>
</button>

<aside aria-label="Admin navigasyon">

    <!-- Admin Profil Kartı -->
    <?php
    $sbAvatar    = $_SESSION['admin_avatar']     ?? 'default.png';
    $sbFirstName = $_SESSION['admin_first_name'] ?? '';
    $sbLastName  = $_SESSION['admin_last_name']  ?? '';
    $sbUsername   = $_SESSION['admin_username']   ?? 'admin';
    $sbFullName   = trim($sbFirstName . ' ' . $sbLastName);
    if ($sbFullName === '') $sbFullName = $sbUsername;
    $sbAvatarUrl = ($sbAvatar !== 'default.png' && $sbAvatar !== '')
        ? ROOT_URL . 'images/uploads/' . $sbAvatar
        : ROOT_URL . 'images/dogukan.png';
    ?>
    <a href="<?= ROOT_URL ?>admin/settings#profile" class="sidebar__profile" title="Profil Ayarları">
        <img src="<?= e($sbAvatarUrl) ?>"
             alt="<?= e($sbFullName) ?>"
             class="sidebar__avatar"
             width="36" height="36"
             loading="lazy">
        <div class="sidebar__profile-info">
            <span class="sidebar__profile-name"><?= e($sbFullName) ?></span>
            <span class="sidebar__profile-role">Yönetici</span>
        </div>
    </a>

    <ul>
        <?php foreach ($navItems as $key => $item): ?>
        <li>
            <a href="<?= e($item['url']) ?>"
               class="<?= $activeMenu === $key ? 'active' : '' ?>"
               <?= $activeMenu === $key ? 'aria-current="page"' : '' ?>>
                <i class="uil <?= e($item['icon']) ?>" aria-hidden="true"></i>
                <h5>
                    <?= e($item['label']) ?>
                    <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                    <span class="sidebar__badge"><?= (int) $item['badge'] ?></span>
                    <?php endif; ?>
                </h5>
            </a>
        </li>
        <?php endforeach; ?>

        <li class="sidebar__separator"></li>

        <li>
            <a href="<?= ROOT_URL ?>"
               target="_blank"
               rel="noopener noreferrer">
                <i class="uil uil-external-link-alt" aria-hidden="true"></i>
                <h5>Siteyi Gör</h5>
            </a>
        </li>

        <li>
            <a href="<?= ROOT_URL ?>admin/cikis">
                <i class="uil uil-signout" aria-hidden="true"></i>
                <h5>Çıkış Yap</h5>
            </a>
        </li>
    </ul>
</aside>
