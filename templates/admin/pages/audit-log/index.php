<?php
/**
 * templates/admin/pages/audit-log/index.php
 * URL: /admin/audit-log
 */

$pageTitle  = 'İşlem Geçmişi';
$activeMenu = 'audit-log';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$page    = max(1, (int)($_GET['sayfa'] ?? 1));
$perPage = 50;
$offset  = ($page - 1) * $perPage;

$total = (int)mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) AS c FROM audit_logs")
)['c'];

$totalPages = (int)ceil($total / $perPage);

$logs = mysqli_query($connection, "
    SELECT l.id, l.action, l.entity_type, l.entity_id,
           l.old_value, l.new_value, l.ip, l.created_at,
           a.username
    FROM audit_logs l
    LEFT JOIN admins a ON l.admin_id = a.id
    ORDER BY l.created_at DESC
    LIMIT {$perPage} OFFSET {$offset}
");

$actionLabels = [
    'login'  => ['label' => 'Giriş',     'color' => 'var(--color-success)'],
    'logout' => ['label' => 'Çıkış',     'color' => 'var(--color-text-faint)'],
    'create' => ['label' => 'Oluşturdu', 'color' => 'var(--color-primary)'],
    'update' => ['label' => 'Güncelledi','color' => 'var(--color-warning)'],
    'delete' => ['label' => 'Sildi',     'color' => 'var(--color-danger)'],
];
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>İşlem Geçmişi</h2>
                <span style="font-size:0.85rem;color:var(--color-text-muted);">
                    Toplam <?= number_format($total) ?> kayıt
                </span>
            </div>

            <?php if ($logs && mysqli_num_rows($logs) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Kullanıcı</th>
                            <th>İşlem</th>
                            <th>Tablo</th>
                            <th>Kayıt ID</th>
                            <th>Değişiklik</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = mysqli_fetch_assoc($logs)): ?>
                        <tr>
                            <td>
                                <small><?= turkceTarih($log['created_at'], 'd F Y H:i') ?></small>
                            </td>
                            <td><?= e($log['username'] ?? 'Sistem') ?></td>
                            <td>
                                <?php
                                $actionInfo = $actionLabels[$log['action']] ?? ['label' => $log['action'], 'color' => 'inherit'];
                                ?>
                                <span style="font-size:0.78rem;font-weight:600;color:<?= $actionInfo['color'] ?>;">
                                    <?= $actionInfo['label'] ?>
                                </span>
                            </td>
                            <td>
                                <code style="font-size:0.78rem;opacity:0.7;">
                                    <?= e($log['entity_type']) ?>
                                </code>
                            </td>
                            <td><?= (int)$log['entity_id'] ?: '—' ?></td>
                            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?php
                                $newVal = $log['new_value']
                                    ? json_decode($log['new_value'], true)
                                    : null;
                                if ($newVal && isset($newVal['title'])):
                                ?>
                                <small style="color:var(--color-text-muted);">
                                    <?= e(mb_substr($newVal['title'], 0, 40)) ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small style="color:var(--color-text-faint);">
                                    <?= e($log['ip'] ?? '—') ?>
                                </small>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Sayfalama -->
            <?php if ($totalPages > 1): ?>
            <nav class="pagination container" style="margin-top:var(--space-5);" aria-label="Sayfa navigasyonu">
                <?php if ($page > 1): ?>
                <a href="<?= ROOT_URL ?>admin/audit-log?sayfa=<?= $page - 1 ?>" class="btn sm">‹ Önceki</a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="<?= ROOT_URL ?>admin/audit-log?sayfa=<?= $i ?>"
                   class="btn sm <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="<?= ROOT_URL ?>admin/audit-log?sayfa=<?= $page + 1 ?>" class="btn sm">Sonraki ›</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

            <?php else: ?>
            <div class="alert__message error"><p>Henüz işlem kaydı bulunmuyor.</p></div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
