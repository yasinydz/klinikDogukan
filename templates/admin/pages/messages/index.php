<?php
/**
 * templates/admin/pages/messages/index.php
 * URL: /admin/messages
 */

$pageTitle  = 'Mesajlar';
$activeMenu = 'messages';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$messages = mysqli_query($connection, "
    SELECT id, full_name, phone, subject, is_read, created_at
    FROM contact_messages
    WHERE is_deleted = 0
    ORDER BY created_at DESC
");
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Mesajlar</h2>
            </div>

            <?php if ($messages && mysqli_num_rows($messages) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Durum</th>
                            <th>Ad Soyad</th>
                            <th>Telefon</th>
                            <th>Konu</th>
                            <th>Tarih</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($msg = mysqli_fetch_assoc($messages)): ?>
                        <tr class="<?= $msg['is_read'] ? '' : 'row--unread' ?>">
                            <td>
                                <span class="status-dot <?= $msg['is_read'] ? 'status-dot--read' : 'status-dot--new' ?>"
                                      title="<?= $msg['is_read'] ? 'Okundu' : 'Okunmadı' ?>"></span>
                            </td>
                            <td><?= e($msg['full_name']) ?></td>
                            <td>
                                <a href="tel:<?= e(preg_replace('/\s/', '', $msg['phone'])) ?>">
                                    <?= e($msg['phone']) ?>
                                </a>
                            </td>
                            <td><?= e($msg['subject']) ?></td>
                            <td><?= turkceTarih($msg['created_at'], 'd F Y H:i') ?></td>
                            <td>
                                <a href="<?= ROOT_URL ?>admin/messages/view?id=<?= (int)$msg['id'] ?>"
                                   class="btn sm">Görüntüle</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert__message error"><p>Henüz mesaj bulunmuyor.</p></div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
