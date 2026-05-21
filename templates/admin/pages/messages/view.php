<?php
/**
 * templates/admin/pages/messages/view.php
 * URL: /admin/messages/view?id=N
 */

$pageTitle  = 'Mesaj Detayı';
$activeMenu = 'messages';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz mesaj.');
    header('Location: ' . ROOT_URL . 'admin/messages');
    exit;
}

$stmt = $connection->prepare(
    "SELECT * FROM contact_messages WHERE id = ? AND is_deleted = 0 LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Mesaj bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/messages');
    exit;
}

$message = $result->fetch_assoc();

// Okundu işaretle
if (!(int)$message['is_read']) {
    $upd = $connection->prepare(
        "UPDATE contact_messages SET is_read = 1, read_at = NOW() WHERE id = ?"
    );
    $upd->bind_param('i', $id);
    $upd->execute();
}
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Mesaj Detayı</h2>
                <a href="<?= ROOT_URL ?>admin/messages" class="btn sm outline">
                    <i class="uil uil-arrow-left"></i> Geri
                </a>
            </div>

            <div class="dashboard__panel">
                <div class="dashboard__panel-header">
                    <h3><?= e($message['subject']) ?></h3>
                </div>
                <div style="padding:var(--space-5);">
                    <div class="message__detail">
                        <div class="message__detail-row">
                            <span class="message__detail-label">Gönderen</span>
                            <span class="message__detail-value"><?= e($message['full_name']) ?></span>
                        </div>
                        <div class="message__detail-row">
                            <span class="message__detail-label">Telefon</span>
                            <span class="message__detail-value">
                                <a href="tel:<?= e(preg_replace('/\s/', '', $message['phone'])) ?>">
                                    <?= e($message['phone']) ?>
                                </a>
                            </span>
                        </div>
                        <?php if (!empty($message['email'])): ?>
                        <div class="message__detail-row">
                            <span class="message__detail-label">E-posta</span>
                            <span class="message__detail-value">
                                <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>
                            </span>
                        </div>
                        <?php endif; ?>
                        <div class="message__detail-row">
                            <span class="message__detail-label">Tarih</span>
                            <span class="message__detail-value">
                                <?= turkceTarih($message['created_at'], 'd F Y - H:i') ?>
                            </span>
                        </div>
                        <div class="message__detail-row">
                            <span class="message__detail-label">KVKK Onayı</span>
                            <span class="message__detail-value">
                                <?= $message['privacy_notice_accepted'] ? '✅ Evet' : '❌ Hayır' ?>
                            </span>
                        </div>
                        <div class="message__detail-body">
                            <span class="message__detail-label">Mesaj</span>
                            <div class="message__detail-content">
                                <?= nl2br(e($message['message'])) ?>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:var(--space-5);display:flex;gap:var(--space-3);flex-wrap:wrap;">
                        <?php if (!empty($message['phone'])): ?>
                        <a href="tel:<?= e(preg_replace('/\s/', '', $message['phone'])) ?>"
                           class="btn sm">
                            <i class="uil uil-phone"></i> Ara
                        </a>
                        <a href="https://wa.me/<?= e(preg_replace('/[^\d]/', '', $message['phone'])) ?>?text=<?= urlencode('Merhaba ' . $message['full_name'] . ', mesajınız için teşekkürler.') ?>"
                           class="btn sm btn--whatsapp"
                           target="_blank" rel="noopener noreferrer">
                            <i class="uil uil-whatsapp"></i> WhatsApp
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($message['email'])): ?>
                        <a href="mailto:<?= e($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>"
                           class="btn sm outline">
                            <i class="uil uil-envelope"></i> E-posta Yanıtla
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
