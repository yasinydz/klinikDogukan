<?php
/**
 * templates/admin/pages/appointments/index.php
 * URL: /admin/appointments
 */

$pageTitle  = 'Randevular';
$activeMenu = 'appointments';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/csrf.php';

$appointments = mysqli_query($connection, "
    SELECT id, full_name, phone, email,
           preferred_date, preferred_time, session_type,
           status, source, created_at
    FROM appointments
    ORDER BY created_at DESC
");

$statusLabels = [
    'pending'   => ['label' => 'Bekliyor',   'color' => 'var(--color-warning)'],
    'confirmed' => ['label' => 'Onaylandı',  'color' => 'var(--color-success)'],
    'cancelled' => ['label' => 'İptal',      'color' => 'var(--color-danger)'],
    'completed' => ['label' => 'Tamamlandı', 'color' => 'var(--color-primary)'],
    'no_show'   => ['label' => 'Gelmedi',    'color' => 'var(--color-text-faint)'],
];
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Randevular</h2>
                <a href="<?= ROOT_URL ?>admin/slots" class="btn sm outline">
                    <i class="uil uil-clock"></i> Slot Yönetimi
                </a>
            </div>

            <?php if ($appointments && mysqli_num_rows($appointments) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Durum</th>
                            <th>Ad Soyad</th>
                            <th>Telefon</th>
                            <th>Tarih / Saat</th>
                            <th>Tip</th>
                            <th>Başvuru</th>
                            <th>Kaynak</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($apt = mysqli_fetch_assoc($appointments)): ?>
                        <tr class="<?= $apt['status'] === 'pending' ? 'row--unread' : '' ?>">
                            <td>
                                <span style="font-size:0.78rem;font-weight:600;color:<?= $statusLabels[$apt['status']]['color'] ?? 'inherit' ?>;">
                                    ● <?= $statusLabels[$apt['status']]['label'] ?? $apt['status'] ?>
                                </span>
                            </td>
                            <td><?= e($apt['full_name']) ?></td>
                            <td>
                                <a href="tel:<?= e(preg_replace('/\s/', '', $apt['phone'])) ?>">
                                    <?= e($apt['phone']) ?>
                                </a>
                            </td>
                            <td>
                                <?= turkceTarih($apt['preferred_date'], 'd F Y') ?>
                                <br>
                                <small><?= e($apt['preferred_time']) ?></small>
                            </td>
                            <td>
                                <?= $apt['session_type'] === 'online'
                                    ? '<i class="uil uil-video"></i> Online'
                                    : '<i class="uil uil-map-marker"></i> Yüz yüze'
                                ?>
                            </td>
                            <td>
                                <small><?= turkceTarih($apt['created_at'], 'd F H:i') ?></small>
                            </td>
                            <td>
                                <?php if (!empty($apt['source'])): ?>
                                <span class="badge badge--muted"><?= e($apt['source']) ?></span>
                                <?php else: ?>
                                <span class="badge badge--muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table__actions">
                                    <a href="<?= ROOT_URL ?>admin/appointments/view?id=<?= (int)$apt['id'] ?>"
                                       class="btn sm">Detay</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert__message error"><p>Henüz randevu bulunmuyor.</p></div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
