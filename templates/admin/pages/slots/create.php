<?php
/**
 * templates/admin/pages/slots/create.php
 * URL: /admin/slots/create  (GET)
 * Tekil slot eklemek için form.
 * Toplu slot için slots/index.php içindeki form kullanılır.
 */

$pageTitle  = 'Slot Ekle';
$activeMenu = 'slots';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Tekil Slot Ekle</h2>
                <a href="<?= ROOT_URL ?>admin/slots" class="btn sm outline">
                    <i class="uil uil-arrow-left"></i> Geri
                </a>
            </div>

            <div class="dashboard__panel" style="max-width:480px;">
                <div class="dashboard__panel-header">
                    <h3><i class="uil uil-clock"></i> Slot Bilgileri</h3>
                </div>
                <div style="padding:var(--space-5);">
                    <form action="<?= ROOT_URL ?>admin/slots/create"
                          method="POST"
                          style="display:flex;flex-direction:column;gap:var(--space-4);">

                        <?= csrfField() ?>

                        <div class="form__control">
                            <label for="slot_date">Tarih *</label>
                            <input type="date"
                                   id="slot_date"
                                   name="slot_date"
                                   min="<?= date('Y-m-d') ?>"
                                   required>
                        </div>

                        <div class="form__control">
                            <label for="slot_time">Saat *</label>
                            <input type="time"
                                   id="slot_time"
                                   name="slot_time"
                                   required>
                        </div>

                        <div style="display:flex;gap:var(--space-3);">
                            <button type="submit" name="submit" class="btn">
                                <i class="uil uil-check"></i> Slot Ekle
                            </button>
                            <a href="<?= ROOT_URL ?>admin/slots" class="btn btn--outline">
                                İptal
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            <div style="margin-top:var(--space-6);padding:var(--space-4) var(--space-5);
                        background:var(--color-bg-3);border:1px solid var(--color-border);
                        border-radius:var(--radius-md);max-width:480px;">
                <p style="font-size:0.875rem;color:var(--color-text-muted);">
                    <i class="uil uil-info-circle"></i>
                    Birden fazla slot eklemek için
                    <a href="<?= ROOT_URL ?>admin/slots">Slot Yönetimi</a>
                    sayfasındaki toplu oluşturma formunu kullanın.
                </p>
            </div>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
