<?php
/**
 * templates/admin/pages/dashboard.php
 * URL: /admin
 */

$pageTitle  = 'Dashboard';
$activeMenu = 'dashboard';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';

// İstatistikler
$totalPosts      = (int) mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS c FROM posts WHERE is_published = 1"))['c'];
$totalCategories = (int) mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS c FROM post_categories"))['c'];
$totalViews      = (int) mysqli_fetch_assoc(mysqli_query($connection, "SELECT COALESCE(SUM(views),0) AS c FROM posts"))['c'];

$unreadMessages = 0;
$chkMsg = mysqli_query($connection, "SHOW TABLES LIKE 'contact_messages'");
if ($chkMsg && mysqli_num_rows($chkMsg) > 0) {
    $unreadMessages = (int) mysqli_fetch_assoc(
        mysqli_query($connection, "SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0 AND is_deleted = 0")
    )['c'];
}

$pendingAppointments = 0;
$chkApt = mysqli_query($connection, "SHOW TABLES LIKE 'appointments'");
if ($chkApt && mysqli_num_rows($chkApt) > 0) {
    $pendingAppointments = (int) mysqli_fetch_assoc(
        mysqli_query($connection, "SELECT COUNT(*) AS c FROM appointments WHERE status = 'pending'")
    )['c'];
}

// En çok okunan
$topPosts = mysqli_query($connection, "
    SELECT p.id, p.title, p.views, c.title AS category_title
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    WHERE p.is_published = 1
    ORDER BY p.views DESC LIMIT 5
");

// Son yazılar
$recentPosts = mysqli_query($connection, "
    SELECT p.id, p.title, p.published_at, c.title AS category_title
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    ORDER BY p.published_at DESC LIMIT 5
");

// Son randevular
$recentAppointments = null;
if ($chkApt && mysqli_num_rows($chkApt) > 0) {
    $recentAppointments = mysqli_query($connection, "
        SELECT id, full_name, phone, preferred_date, preferred_time,
               session_type, status, created_at
        FROM appointments
        ORDER BY created_at DESC LIMIT 5
    ");
}

// Son mesajlar
$recentMessages = null;
if ($chkMsg && mysqli_num_rows($chkMsg) > 0) {
    $recentMessages = mysqli_query($connection, "
        SELECT id, full_name, subject, is_read, created_at
        FROM contact_messages
        WHERE is_deleted = 0
        ORDER BY created_at DESC LIMIT 5
    ");
}

// ── İçerik sağlığı istatistikleri ─────────────────────────
$draftPosts  = (int) mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) AS c FROM posts WHERE is_published = 0")
)['c'];
$postsNoThumb = (int) mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) AS c FROM posts WHERE is_published = 1 AND (thumbnail IS NULL OR thumbnail = '')")
)['c'];
$postsNoMeta  = (int) mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) AS c FROM posts WHERE is_published = 1 AND (meta_title IS NULL OR meta_title = '' OR meta_desc IS NULL OR meta_desc = '')")
)['c'];

$statusLabels = [
    'pending'   => ['label' => 'Bekliyor',   'color' => 'var(--color-warning)'],
    'confirmed' => ['label' => 'Onaylandı',  'color' => 'var(--color-success)'],
    'cancelled' => ['label' => 'İptal',      'color' => 'var(--color-danger)'],
    'completed' => ['label' => 'Tamamlandı', 'color' => 'var(--color-primary)'],
    'no_show'   => ['label' => 'Gelmedi',    'color' => 'var(--color-text-faint)'],
];

// ── FAZ X: Ekstra istatistikler ──────────────────────────────

// Bugünkü randevular
$todayAppts = 0;
$tomorrowAppts = 0;
if ($chkApt && mysqli_num_rows($chkApt) > 0) {
    $todayAppts = (int) mysqli_fetch_assoc(
        mysqli_query($connection, "SELECT COUNT(*) AS c FROM appointments WHERE preferred_date = CURDATE() AND status NOT IN ('cancelled')")
    )['c'];
    $tomorrowAppts = (int) mysqli_fetch_assoc(
        mysqli_query($connection, "SELECT COUNT(*) AS c FROM appointments WHERE preferred_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND status NOT IN ('cancelled')")
    )['c'];
}

// Galeri
$totalGallery = 0;
$chkGallery = mysqli_query($connection, "SHOW TABLES LIKE 'gallery'");
if ($chkGallery && mysqli_num_rows($chkGallery) > 0) {
    $totalGallery = (int) mysqli_fetch_assoc(
        mysqli_query($connection, "SELECT COUNT(*) AS c FROM gallery WHERE is_active = 1")
    )['c'];
}

// Testimonial
$totalTestimonials = 0;
$chkTestimonials = mysqli_query($connection, "SHOW TABLES LIKE 'testimonials'");
if ($chkTestimonials && mysqli_num_rows($chkTestimonials) > 0) {
    $totalTestimonials = (int) mysqli_fetch_assoc(
        mysqli_query($connection, "SELECT COUNT(*) AS c FROM testimonials WHERE is_active = 1")
    )['c'];
}

// GBP durum
$gbpProfileUrl = siteSetting('gbp_profile_url', '');
$gbpReviewUrl  = siteSetting('gbp_review_url', '');
$gbpPlaceId    = siteSetting('gbp_place_id', '');
$mapsEmbedUrl  = siteSetting('maps_embed_url', '');

// Lead source breakdown (son 30 gün)
$sourceBreakdown = [];
if ($chkApt && mysqli_num_rows($chkApt) > 0) {
    $srcResult = mysqli_query($connection, "
        SELECT COALESCE(source, 'unknown') AS src, COUNT(*) AS c
        FROM appointments
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY src ORDER BY c DESC LIMIT 6
    ");
    if ($srcResult) {
        while ($sr = mysqli_fetch_assoc($srcResult)) {
            $sourceBreakdown[$sr['src']] = (int) $sr['c'];
        }
    }
}
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>

        <main>
            <h2>Dashboard</h2>

            <!-- İstatistik Kartları -->
            <div class="dashboard__stats">
                <div class="dashboard__stat-card" style="border-left:3px solid var(--color-primary);">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    <div class="stat-value"><?= $todayAppts ?></div>
                    <div class="stat-label">Bugünkü Randevu</div>
                </div>
                <div class="dashboard__stat-card">
                    <i class="uil uil-schedule" aria-hidden="true"></i>
                    <div class="stat-value"><?= $tomorrowAppts ?></div>
                    <div class="stat-label">Yarınki Randevu</div>
                </div>
                <div class="dashboard__stat-card">
                    <i class="uil uil-clock" aria-hidden="true"></i>
                    <div class="stat-value"><?= $pendingAppointments ?></div>
                    <div class="stat-label">Bekleyen Randevu</div>
                </div>
                <div class="dashboard__stat-card">
                    <i class="uil uil-envelope" aria-hidden="true"></i>
                    <div class="stat-value"><?= $unreadMessages ?></div>
                    <div class="stat-label">Yeni Mesaj</div>
                </div>
                <div class="dashboard__stat-card">
                    <i class="uil uil-postcard" aria-hidden="true"></i>
                    <div class="stat-value"><?= $totalPosts ?></div>
                    <div class="stat-label">Yayınlanan Yazı</div>
                </div>
                <div class="dashboard__stat-card">
                    <i class="uil uil-eye" aria-hidden="true"></i>
                    <div class="stat-value"><?= number_format($totalViews) ?></div>
                    <div class="stat-label">Toplam Görüntülenme</div>
                </div>
                <div class="dashboard__stat-card">
                    <i class="uil uil-image" aria-hidden="true"></i>
                    <div class="stat-value"><?= $totalGallery ?></div>
                    <div class="stat-label">Galeri Görseli</div>
                </div>
                <div class="dashboard__stat-card">
                    <i class="uil uil-comment-alt-lines" aria-hidden="true"></i>
                    <div class="stat-value"><?= $totalTestimonials ?></div>
                    <div class="stat-label">Aktif Yorum</div>
                </div>
            </div>

            <!-- GBP / Local Trust Durum -->
            <div class="dashboard__health">
                <?php
                $gbpChecks = [
                    ['ok' => $gbpProfileUrl !== '', 'text' => 'Google Business Profile URL', 'icon' => 'uil-google'],
                    ['ok' => $gbpReviewUrl !== '',  'text' => 'GBP Yorum URL',              'icon' => 'uil-star'],
                    ['ok' => $gbpPlaceId !== '',    'text' => 'GBP Place ID',               'icon' => 'uil-map-pin'],
                    ['ok' => $mapsEmbedUrl !== '',  'text' => 'Harita Embed URL',            'icon' => 'uil-map'],
                ];
                $gbpComplete = 0;
                foreach ($gbpChecks as $gc) { if ($gc['ok']) $gbpComplete++; }
                ?>
                <div class="dashboard__health-item <?= $gbpComplete === 4 ? 'dashboard__health-item--info' : 'dashboard__health-item--warn' ?>">
                    <i class="uil uil-map-pin-alt" aria-hidden="true"></i>
                    <span>Local SEO: <?= $gbpComplete ?>/4 tamamlandı
                        <?php foreach ($gbpChecks as $gc): ?>
                        <?= $gc['ok'] ? '' : ' · <strong>' . e($gc['text']) . ' eksik</strong>' ?>
                        <?php endforeach; ?>
                    </span>
                    <a href="<?= ROOT_URL ?>admin/local-trust" class="btn sm outline">Düzenle</a>
                </div>

                <?php if ($draftPosts > 0): ?>
                <div class="dashboard__health-item dashboard__health-item--warn">
                    <i class="uil uil-file-edit-alt" aria-hidden="true"></i>
                    <span><?= $draftPosts ?> taslak yazı yayınlanmayı bekliyor.</span>
                    <a href="<?= ROOT_URL ?>admin/posts" class="btn sm outline">Yazılara Git</a>
                </div>
                <?php endif; ?>
                <?php if ($postsNoMeta > 0): ?>
                <div class="dashboard__health-item dashboard__health-item--seo">
                    <i class="uil uil-search-alt" aria-hidden="true"></i>
                    <span><?= $postsNoMeta ?> yazıda meta title/description eksik.</span>
                    <a href="<?= ROOT_URL ?>admin/posts" class="btn sm outline">SEO Düzelt</a>
                </div>
                <?php endif; ?>
                <?php if ($totalGallery === 0): ?>
                <div class="dashboard__health-item dashboard__health-item--warn">
                    <i class="uil uil-image-slash" aria-hidden="true"></i>
                    <span>Henüz galeri görseli yok. Ofis fotoğrafları güven artırır.</span>
                    <a href="<?= ROOT_URL ?>admin/gallery/create" class="btn sm outline">Görsel Ekle</a>
                </div>
                <?php endif; ?>
                <?php if ($totalTestimonials === 0): ?>
                <div class="dashboard__health-item dashboard__health-item--warn">
                    <i class="uil uil-comment-alt-slash" aria-hidden="true"></i>
                    <span>Henüz danışan yorumu yok. Yorumlar dönüşümü artırır.</span>
                    <a href="<?= ROOT_URL ?>admin/testimonials/create" class="btn sm outline">Yorum Ekle</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Lead Source Breakdown (son 30 gün) -->
            <?php if (!empty($sourceBreakdown)): ?>
            <div class="dashboard__panel" style="margin-bottom:var(--space-6);">
                <div class="dashboard__panel-header">
                    <h3><i class="uil uil-chart-pie"></i> Başvuru Kaynakları (Son 30 Gün)</h3>
                </div>
                <div style="padding:var(--space-5);display:flex;flex-wrap:wrap;gap:var(--space-3);">
                    <?php foreach ($sourceBreakdown as $src => $cnt): ?>
                    <div style="background:var(--color-bg-3);border:1px solid var(--color-border);border-radius:var(--radius-md);padding:var(--space-3) var(--space-4);text-align:center;min-width:100px;">
                        <div style="font-size:1.3rem;font-weight:800;color:var(--color-primary);"><?= $cnt ?></div>
                        <div style="font-size:0.72rem;color:var(--color-text-faint);text-transform:uppercase;letter-spacing:0.05em;"><?= e($src) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Hızlı İşlemler -->
            <div class="dashboard__quick-actions">
                <a href="<?= ROOT_URL ?>admin/posts/create" class="btn">
                    <i class="uil uil-pen" aria-hidden="true"></i>
                    Yazı Ekle
                </a>
                <a href="<?= ROOT_URL ?>admin/gallery/create" class="btn outline">
                    <i class="uil uil-image-plus" aria-hidden="true"></i>
                    Görsel Ekle
                </a>
                <a href="<?= ROOT_URL ?>admin/testimonials/create" class="btn outline">
                    <i class="uil uil-comment-plus" aria-hidden="true"></i>
                    Yorum Ekle
                </a>
                <a href="<?= ROOT_URL ?>admin/services" class="btn outline">
                    <i class="uil uil-heart-rate" aria-hidden="true"></i>
                    Hizmetler
                </a>
                <a href="<?= ROOT_URL ?>admin/appointments" class="btn outline">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevular
                    <?php if ($pendingAppointments > 0): ?>
                    <span class="sidebar__badge"><?= $pendingAppointments ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= ROOT_URL ?>admin/local-trust" class="btn outline">
                    <i class="uil uil-map-pin-alt" aria-hidden="true"></i>
                    Konum & GBP
                </a>
                <a href="<?= ROOT_URL ?>admin/slots" class="btn outline">
                    <i class="uil uil-clock" aria-hidden="true"></i>
                    Slot Yönetimi
                </a>
            </div>

            <!-- Paneller -->
            <div class="dashboard__panels">
                <div class="dashboard__panel-left">

                    <!-- En Çok Okunan -->
                    <div class="dashboard__panel">
                        <div class="dashboard__panel-header">
                            <h3>
                                <i class="uil uil-chart-line"></i>
                                En Çok Okunan
                            </h3>
                            <a href="<?= ROOT_URL ?>admin/posts" class="btn sm outline">Tümü</a>
                        </div>
                        <?php if ($topPosts && mysqli_num_rows($topPosts) > 0): ?>
                        <div class="dashboard__list">
                            <?php while ($tp = mysqli_fetch_assoc($topPosts)): ?>
                            <div class="dashboard__list-item">
                                <div class="dashboard__list-info">
                                    <a href="<?= ROOT_URL ?>admin/posts/edit?id=<?= (int)$tp['id'] ?>"
                                       class="dashboard__list-title">
                                        <?= e($tp['title']) ?>
                                    </a>
                                    <span class="dashboard__list-meta">
                                        <?= e($tp['category_title'] ?? 'Genel') ?>
                                    </span>
                                </div>
                                <span class="dashboard__list-badge">
                                    <i class="uil uil-eye"></i>
                                    <?= number_format((int)$tp['views']) ?>
                                </span>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <p class="dashboard__empty">Henüz yazı yok.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Son Randevular -->
                    <?php if ($recentAppointments): ?>
                    <div class="dashboard__panel">
                        <div class="dashboard__panel-header">
                            <h3>
                                <i class="uil uil-calendar-alt"></i>
                                Son Randevular
                            </h3>
                            <a href="<?= ROOT_URL ?>admin/appointments" class="btn sm outline">Tümü</a>
                        </div>
                        <?php if (mysqli_num_rows($recentAppointments) > 0): ?>
                        <div class="dashboard__list">
                            <?php while ($apt = mysqli_fetch_assoc($recentAppointments)): ?>
                            <div class="dashboard__list-item <?= $apt['status'] === 'pending' ? 'unread' : '' ?>">
                                <div class="dashboard__list-info">
                                    <a href="<?= ROOT_URL ?>admin/appointments/view?id=<?= (int)$apt['id'] ?>"
                                       class="dashboard__list-title">
                                        <?= e($apt['full_name']) ?>
                                    </a>
                                    <span class="dashboard__list-meta">
                                        <?= turkceTarih($apt['preferred_date'], 'd F Y') ?>
                                        <?= e($apt['preferred_time']) ?>
                                        · <?= $apt['session_type'] === 'online' ? 'Online' : 'Yüz yüze' ?>
                                    </span>
                                </div>
                                <span class="status" style="color:<?= $statusLabels[$apt['status']]['color'] ?? 'var(--color-text-faint)' ?>;">
                                    <?= $statusLabels[$apt['status']]['label'] ?? $apt['status'] ?>
                                </span>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <p class="dashboard__empty">Henüz randevu yok.</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div><!-- /.panel-left -->

                <div class="dashboard__panel-right">

                    <!-- Son Yazılar -->
                    <div class="dashboard__panel">
                        <div class="dashboard__panel-header">
                            <h3>
                                <i class="uil uil-clock"></i>
                                Son Yazılar
                            </h3>
                        </div>
                        <?php if ($recentPosts && mysqli_num_rows($recentPosts) > 0): ?>
                        <div class="dashboard__list">
                            <?php while ($rp = mysqli_fetch_assoc($recentPosts)): ?>
                            <div class="dashboard__list-item">
                                <div class="dashboard__list-info">
                                    <a href="<?= ROOT_URL ?>admin/posts/edit?id=<?= (int)$rp['id'] ?>"
                                       class="dashboard__list-title">
                                        <?= e($rp['title']) ?>
                                    </a>
                                    <span class="dashboard__list-meta">
                                        <?= turkceTarih($rp['published_at'], 'd F Y') ?>
                                    </span>
                                </div>
                                <span class="dashboard__list-badge-muted">
                                    <?= e($rp['category_title'] ?? 'Genel') ?>
                                </span>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <p class="dashboard__empty">Henüz yazı yok.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Son Mesajlar -->
                    <?php if ($recentMessages): ?>
                    <div class="dashboard__panel">
                        <div class="dashboard__panel-header">
                            <h3>
                                <i class="uil uil-envelope"></i>
                                Son Mesajlar
                            </h3>
                            <a href="<?= ROOT_URL ?>admin/messages" class="btn sm outline">Tümü</a>
                        </div>
                        <?php if (mysqli_num_rows($recentMessages) > 0): ?>
                        <div class="dashboard__list">
                            <?php while ($msg = mysqli_fetch_assoc($recentMessages)): ?>
                            <div class="dashboard__list-item <?= $msg['is_read'] ? '' : 'unread' ?>">
                                <div class="dashboard__list-info">
                                    <a href="<?= ROOT_URL ?>admin/messages/view?id=<?= (int)$msg['id'] ?>"
                                       class="dashboard__list-title">
                                        <?= e($msg['full_name']) ?>
                                    </a>
                                    <span class="dashboard__list-meta">
                                        <?= e($msg['subject']) ?>
                                    </span>
                                </div>
                                <span class="dashboard__list-meta">
                                    <?= turkceTarih($msg['created_at'], 'd F') ?>
                                </span>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <p class="dashboard__empty">Henüz mesaj yok.</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div><!-- /.panel-right -->
            </div><!-- /.dashboard__panels -->
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
