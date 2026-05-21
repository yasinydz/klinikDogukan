<?php
/**
 * templates/admin/pages/gallery/create.php
 * GET /admin/gallery/create
 */

$pageTitle  = 'Görsel Ekle';
$activeMenu = 'gallery';

require_once BASE_PATH . '/templates/admin/partials/header.php';

$categories = ['ofis', 'bekleme-alani', 'calisma-odasi', 'bina-girisi', 'uzman', 'genel'];
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Görsel Ekle</h2>
                <a href="<?= ROOT_URL ?>admin/gallery" class="btn btn--outline">← Galeriye Dön</a>
            </div>

            <div class="form__section-container form__section-container--wide" style="max-width:640px;">
                <form action="<?= ROOT_URL ?>admin/gallery/create"
                      method="POST"
                      enctype="multipart/form-data">

                    <?= csrfField() ?>

                    <div class="form__control">
                        <label for="image">Görsel *</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png" required>
                        <small class="text-muted">JPG veya PNG, max 2 MB</small>
                    </div>

                    <div class="form__control">
                        <label for="title">Başlık</label>
                        <input type="text" id="title" name="title" placeholder="ör: Terapi Odası" maxlength="200">
                    </div>

                    <div class="form__control">
                        <label for="alt_text">Alt Metin (SEO)</label>
                        <input type="text" id="alt_text" name="alt_text" placeholder="ör: İzmit psikolog ofisi terapi odası" maxlength="300">
                    </div>

                    <div class="form__control">
                        <label for="category">Kategori</label>
                        <select id="category" name="category">
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat) ?>"><?= e(ucfirst(str_replace('-', ' ', $cat))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form__control">
                        <label for="display_order">Sıra No</label>
                        <input type="number" id="display_order" name="display_order" value="0" min="0" max="255">
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1">
                        <label for="is_featured">Öne çıkan görsel</label>
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label for="is_active">Aktif (sitede göster)</label>
                    </div>

                    <button type="submit" class="btn btn--full">
                        <i class="uil uil-upload" aria-hidden="true"></i>
                        Görseli Yükle
                    </button>
                </form>
            </div>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
