<?php
/**
 * templates/admin/pages/slots/index.php
 * URL: /admin/slots
 */

$pageTitle  = 'Slot Yönetimi';
$activeMenu = 'slots';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/csrf.php';

// Bugün ve sonrası slotlar
$slots = mysqli_query($connection, "
    SELECT s.id, s.slot_date, s.slot_time,
           s.is_available, s.is_blocked, s.block_reason,
           (SELECT COUNT(*) FROM appointments a
            WHERE a.preferred_date = s.slot_date
              AND a.preferred_time = s.slot_time
              AND a.status NOT IN ('cancelled')) AS booking_count
    FROM appointment_slots s
    WHERE s.slot_date >= CURDATE()
    ORDER BY s.slot_date ASC, s.slot_time ASC
    LIMIT 200
");

// Aylık özet
$monthSummary = mysqli_query($connection, "
    SELECT slot_date,
           COUNT(*) AS total,
           SUM(is_available) AS available,
           SUM(is_blocked) AS blocked
    FROM appointment_slots
    WHERE slot_date >= CURDATE()
      AND slot_date < DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    GROUP BY slot_date
    ORDER BY slot_date ASC
");
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Slot Yönetimi</h2>
                <a href="<?= ROOT_URL ?>admin/slots/create" class="btn">
                    <i class="uil uil-plus"></i> Slot Ekle
                </a>
            </div>

            <!-- Toplu Slot Oluştur -->
            <div class="dashboard__panel" style="margin-bottom:var(--space-6);">
                <div class="dashboard__panel-header">
                    <h3><i class="uil uil-repeat"></i> Haftalık Şablon ile Toplu Slot Oluştur</h3>
                </div>
                <div style="padding:var(--space-5);">
                    <form action="<?= ROOT_URL ?>admin/slots/create"
                          method="POST"
                          style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:var(--space-4);align-items:end;">

                        <?= csrfField() ?>
                        <input type="hidden" name="bulk" value="1">

                        <div class="form__control" style="margin-bottom:0;">
                            <label for="start_date">Başlangıç Tarihi</label>
                            <input type="date" id="start_date" name="start_date"
                                   min="<?= date('Y-m-d') ?>"
                                   value="<?= date('Y-m-d') ?>"
                                   required>
                        </div>

                        <div class="form__control" style="margin-bottom:0;">
                            <label for="end_date">Bitiş Tarihi</label>
                            <input type="date" id="end_date" name="end_date"
                                   min="<?= date('Y-m-d') ?>"
                                   value="<?= date('Y-m-d', strtotime('+4 weeks')) ?>"
                                   required>
                        </div>

                        <div class="form__control" style="margin-bottom:0;">
                            <label for="start_time">Başlangıç Saati</label>
                            <input type="time" id="start_time" name="start_time"
                                   value="09:00" required>
                        </div>

                        <div class="form__control" style="margin-bottom:0;">
                            <label for="end_time">Bitiş Saati</label>
                            <input type="time" id="end_time" name="end_time"
                                   value="18:00" required>
                        </div>

                        <div class="form__control" style="margin-bottom:0;">
                            <label for="interval_minutes">Aralık (dakika)</label>
                            <select id="interval_minutes" name="interval_minutes">
                                <option value="50">50 dk</option>
                                <option value="60" selected>60 dk</option>
                                <option value="90">90 dk</option>
                            </select>
                        </div>

                        <div class="form__control" style="margin-bottom:0;">
                            <label>Günler</label>
                            <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);">
                                <?php
                                $dayNames = ['Mon'=>'Pzt','Tue'=>'Sal','Wed'=>'Çar','Thu'=>'Per','Fri'=>'Cum','Sat'=>'Cmt'];
                                foreach ($dayNames as $val => $label): ?>
                                <label style="display:flex;align-items:center;gap:4px;font-size:0.85rem;">
                                    <input type="checkbox" name="days[]" value="<?= $val ?>"
                                           <?= in_array($val, ['Mon','Tue','Wed','Thu','Fri']) ? 'checked' : '' ?>>
                                    <?= $label ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn" style="width:100%;">
                                <i class="uil uil-calender"></i> Slotları Oluştur
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Slot Listesi -->
            <?php if ($slots && mysqli_num_rows($slots) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Saat</th>
                            <th>Durum</th>
                            <th>Rezervasyon</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($slot = mysqli_fetch_assoc($slots)): ?>
                        <tr>
                            <td><?= turkceTarih($slot['slot_date'], 'd F Y, l') ?></td>
                            <td><?= date('H:i', strtotime($slot['slot_time'])) ?></td>
                            <td>
                                <?php if ($slot['is_blocked']): ?>
                                <span style="color:var(--color-danger);font-size:0.78rem;font-weight:600;">
                                    ● Bloke (<?= e($slot['block_reason'] ?? '') ?>)
                                </span>
                                <?php elseif ($slot['is_available']): ?>
                                <span style="color:var(--color-success);font-size:0.78rem;font-weight:600;">
                                    ● Müsait
                                </span>
                                <?php else: ?>
                                <span style="color:var(--color-text-faint);font-size:0.78rem;font-weight:600;">
                                    ● Doldu
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= (int)$slot['booking_count'] > 0
                                    ? '<span style="color:var(--color-primary);">Rezerve</span>'
                                    : '—'
                                ?>
                            </td>
                            <td>
                                <div class="table__actions">
                                    <form method="POST"
                                          action="<?= ROOT_URL ?>admin/slots/delete"
                                          style="display:inline;"
                                          onsubmit="return confirm('Bu slotu silmek istediğinize emin misiniz?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int)$slot['id'] ?>">
                                        <button type="submit" class="btn sm danger">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert__message error">
                <p>Henüz slot tanımlanmamış. Yukarıdaki formu kullanarak slot oluşturun.</p>
            </div>
            <?php endif; ?>

        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
