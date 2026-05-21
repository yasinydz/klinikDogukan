<?php
/**
 * templates/admin/pages/categories/edit.php
 * URL: /admin/categories/edit?id=N
 */

$pageTitle  = 'Kategori Düzenle';
$activeMenu = 'categories';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz kategori.');
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

$stmt = $connection->prepare(
    "SELECT * FROM post_categories WHERE id = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Kategori bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/categories');
    exit;
}

$category = $result->fetch_assoc();
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Kategori Düzenle</h2>
                <a href="<?= ROOT_URL ?>admin/categories" class="btn sm outline">
                    <i class="uil uil-arrow-left"></i> Geri
                </a>
            </div>

            <!-- Slug readonly -->
            <div class="dashboard__panel" style="margin-bottom:var(--space-5);">
                <div class="dashboard__panel-header">
                    <h3><i class="uil uil-link"></i> URL (değiştirilemez)</h3>
                </div>
                <div style="padding:var(--space-4) var(--space-5);">
                    <input type="text"
                           value="/kategori/<?= e($category['slug']) ?>"
                           readonly
                           style="opacity:0.6;cursor:not-allowed;">
                </div>
            </div>

            <div class="dashboard__panel">
                <div class="dashboard__panel-header">
                    <h3>Kategori Bilgileri</h3>
                </div>
                <div style="padding:var(--space-5);">
                    <form action="<?= ROOT_URL ?>admin/categories/edit"
                          method="POST"
                          style="display:flex;flex-direction:column;gap:var(--space-4);">

                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">

                        <div class="form__control">
                            <label for="title">Başlık *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= e($category['title']) ?>"
                                   required maxlength="100">
                        </div>

                        <div class="form__control">
                            <label for="description">Açıklama *</label>
                            <textarea id="description" name="description"
                                      rows="4" required><?= e($category['description']) ?></textarea>
                        </div>

                        <div class="form__control">
                            <label for="meta_desc">Meta Description
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 160 karakter)</small>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="160"><?= e($category['meta_desc'] ?? '') ?></textarea>
                        </div>

                        <div class="form__control inline">
                            <input type="checkbox" id="is_noindex" name="is_noindex" value="1"
                                   <?= $category['is_noindex'] ? 'checked' : '' ?>>
                            <label for="is_noindex">noindex</label>
                        </div>

                        <div style="display:flex;gap:var(--space-3);">
                            <button type="submit" name="submit" class="btn">
                                <i class="uil uil-check"></i> Güncelle
                            </button>
                            <a href="<?= ROOT_URL ?>kategori/<?= urlencode($category['slug']) ?>"
                               target="_blank" class="btn btn--outline">
                                <i class="uil uil-external-link-alt"></i> Siteye Git
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
