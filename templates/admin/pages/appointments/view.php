<?php
/**
 * templates/admin/pages/appointments/view.php
 * URL: /admin/appointments/view?id=N
 */

$pageTitle  = 'Randevu Detayı';
$activeMenu = 'appointments';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/csrf.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz randevu.');
    header('Location: ' . ROOT_URL . 'admin/appointments');
    exit;
}

$stmt = $connection->prepare(
    "SELECT * FROM appointments WHERE id = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Randevu bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/appointments');
    exit;
}

$apt = $result->fetch_assoc();

$statusOptions = [
    'pending'   => 'Bekliyor',
    'confirmed' => 'Onaylandı',
    'cancelled' => 'İptal',
    'completed' => 'Tamamlandı',
    'no_show'   => 'Gelmedi',
];
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Randevu Detayı</h2>
                <a href="<?= ROOT_URL ?>admin/appointments" class="btn sm outline">
                    <i class="uil uil-arrow-left"></i> Geri
                </a>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6);">

                <!-- Randevu Bilgileri -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3>Danışan Bilgileri</h3>
                    </div>
                    <div style="padding:var(--space-5);">
                        <div class="message__detail">
                            <div class="message__detail-row">
                                <span class="message__detail-label">Ad Soyad</span>
                                <span class="message__detail-value"><?= e($apt['full_name']) ?></span>
                            </div>
                            <div class="message__detail-row">
                                <span class="message__detail-label">Telefon</span>
                                <span class="message__detail-value">
                                    <a href="tel:<?= e(preg_replace('/\s/', '', $apt['phone'])) ?>">
                                        <?= e($apt['phone']) ?>
                                    </a>
                                </span>
                            </div>
                            <?php if (!empty($apt['email'])): ?>
                            <div class="message__detail-row">
                                <span class="message__detail-label">E-posta</span>
                                <span class="message__detail-value">
                                    <a href="mailto:<?= e($apt['email']) ?>"><?= e($apt['email']) ?></a>
                                </span>
                            </div>
                            <?php endif; ?>
                            <div class="message__detail-row">
                                <span class="message__detail-label">Tarih</span>
                                <span class="message__detail-value">
                                    <?= turkceTarih($apt['preferred_date'], 'd F Y') ?>
                                </span>
                            </div>
                            <div class="message__detail-row">
                                <span class="message__detail-label">Saat</span>
                                <span class="message__detail-value"><?= e($apt['preferred_time']) ?></span>
                            </div>
                            <div class="message__detail-row">
                                <span class="message__detail-label">Tip</span>
                                <span class="message__detail-value">
                                    <?= $apt['session_type'] === 'online' ? 'Online' : 'Yüz yüze' ?>
                                </span>
                            </div>
                            <div class="message__detail-row">
                                <span class="message__detail-label">Başvuru</span>
                                <span class="message__detail-value">
                                    <?= turkceTarih($apt['created_at'], 'd F Y H:i') ?>
                                </span>
                            </div>
                            <div class="message__detail-row">
                                <span class="message__detail-label">KVKK Onayı</span>
                                <span class="message__detail-value">
                                    <?= $apt['privacy_notice_accepted'] ? '✅ Evet' : '❌ Hayır' ?>
                                </span>
                            </div>
                        </div>

                        <!-- Hızlı İletişim -->
                        <div style="margin-top:var(--space-5);display:flex;gap:var(--space-3);flex-wrap:wrap;">
                            <a href="tel:<?= e(preg_replace('/\s/', '', $apt['phone'])) ?>"
                               class="btn sm">
                                <i class="uil uil-phone"></i> Ara
                            </a>
                            <a href="https://wa.me/<?= e(preg_replace('/[^\d]/', '', $apt['phone'])) ?>?text=<?= urlencode('Merhaba ' . $apt['full_name'] . ', randevunuz hakkında görüşmek istiyorum.') ?>"
                               class="btn sm btn--whatsapp"
                               target="_blank" rel="noopener noreferrer">
                                <i class="uil uil-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Durum Güncelle -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3>Durum Güncelle</h3>
                    </div>
                    <div style="padding:var(--space-5);">
                        <form action="<?= ROOT_URL ?>admin/appointments/update-status"
                              method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$apt['id'] ?>">

                            <div class="form__control">
                                <label for="status">Randevu Durumu</label>
                                <select id="status" name="status">
                                    <?php foreach ($statusOptions as $val => $label): ?>
                                    <option value="<?= e($val) ?>"
                                        <?= $apt['status'] === $val ? 'selected' : '' ?>>
                                        <?= e($label) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form__control">
                                <label for="admin_notes">Notlar (dahili)</label>
                                <textarea id="admin_notes" name="admin_notes"
                                          rows="4"
                                          placeholder="Danışana gösterilmez"><?= e($apt['admin_notes'] ?? '') ?></textarea>
                            </div>

                            <button type="submit" class="btn">
                                <i class="uil uil-check"></i> Güncelle
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
