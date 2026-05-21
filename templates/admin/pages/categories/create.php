<?php
/**
 * templates/admin/pages/categories/create.php
 * URL: /admin/categories/create
 */

$pageTitle  = 'Yeni Kategori';
$activeMenu = 'categories';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Yeni Kategori</h2>
                <a href="<?= ROOT_URL ?>admin/categories" class="btn sm outline">
                    <i class="uil uil-arrow-left"></i> Geri
                </a>
            </div>

            <div class="dashboard__panel">
                <div class="dashboard__panel-header">
                    <h3>Kategori Bilgileri</h3>
                </div>
                <div style="padding:var(--space-5);">
                    <form action="<?= ROOT_URL ?>admin/categories/create"
                          method="POST"
                          style="display:flex;flex-direction:column;gap:var(--space-4);">

                        <?= csrfField() ?>

                        <div class="form__control">
                            <label for="title">Başlık *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= e($old['title'] ?? '') ?>"
                                   placeholder="Kategori başlığı"
                                   required maxlength="100">
                        </div>

                        <div class="form__control">
                            <label for="description">Açıklama *</label>
                            <textarea id="description" name="description"
                                      rows="4"
                                      placeholder="Kategori açıklaması"
                                      required><?= e($old['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form__control">
                            <label for="meta_desc">
                                Meta Description
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 160 karakter, boş = açıklamadan üretilir)</small>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="160"
                                      placeholder="Boş bırakılırsa açıklamadan üretilir"><?= e($old['meta_desc'] ?? '') ?></textarea>
                        </div>

                        <div class="form__control inline">
                            <input type="checkbox" id="is_noindex" name="is_noindex" value="1"
                                   <?= !empty($old['is_noindex']) ? 'checked' : '' ?>>
                            <label for="is_noindex">
                                noindex
                                <small style="color:var(--color-text-faint);">
                                    (Bu kategori arama motorlarına kapatılsın)
                                </small>
                            </label>
                        </div>

                        <div style="display:flex;gap:var(--space-3);">
                            <button type="submit" name="submit" class="btn">
                                <i class="uil uil-check"></i> Kategori Ekle
                            </button>
                            <a href="<?= ROOT_URL ?>admin/categories" class="btn btn--outline">İptal</a>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
