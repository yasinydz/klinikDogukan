<?php
/**
 * templates/admin/pages/gallery/edit.php
 * GET /admin/gallery/edit?id=N
 */

$pageTitle  = 'Görsel Düzenle';
$activeMenu = 'gallery';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

$stmt = $connection->prepare("SELECT * FROM gallery WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$img = $stmt->get_result()->fetch_assoc();

if (!$img) {
    flashSet('error', 'Görsel bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/gallery');
    exit;
}

require_once BASE_PATH . '/templates/admin/partials/header.php';

$categories = ['ofis', 'bekleme-alani', 'calisma-odasi', 'bina-girisi', 'uzman', 'genel'];
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Görsel Düzenle</h2>
                <a href="<?= ROOT_URL ?>admin/gallery" class="btn btn--outline">← Galeriye Dön</a>
            </div>

            <div class="form__section-container form__section-container--wide" style="max-width:640px;">

                <!-- Mevcut görsel önizleme -->
                <div style="margin-bottom:var(--space-5);text-align:center;">
                    <img src="<?= ROOT_URL ?>images/uploads/<?= e($img['filename']) ?>"
                         alt="<?= e($img['alt_text'] ?: $img['title']) ?>"
                         style="max-width:100%;max-height:300px;border-radius:var(--radius-md);border:1.5px solid var(--color-border);">
                </div>

                <form action="<?= ROOT_URL ?>admin/gallery/edit"
                      method="POST"
                      enctype="multipart/form-data">

                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                    <input type="hidden" name="current_filename" value="<?= e($img['filename']) ?>">

                    <div class="form__control">
                        <label for="image">Yeni Görsel (opsiyonel)</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png">
                        <small class="text-muted">Boş bırakırsanız mevcut görsel korunur</small>
                    </div>

                    <div class="form__control">
                        <label for="title">Başlık</label>
                        <input type="text" id="title" name="title"
                               value="<?= e($img['title']) ?>" maxlength="200">
                    </div>

                    <div class="form__control">
                        <label for="alt_text">Alt Metin (SEO)</label>
                        <input type="text" id="alt_text" name="alt_text"
                               value="<?= e($img['alt_text']) ?>" maxlength="300">
                    </div>

                    <div class="form__control">
                        <label for="description">Açıklama</label>
                        <textarea id="description" name="description" rows="3"><?= e($img['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form__control">
                        <label for="category">Kategori</label>
                        <select id="category" name="category">
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat) ?>" <?= $img['category'] === $cat ? 'selected' : '' ?>>
                                <?= e(ucfirst(str_replace('-', ' ', $cat))) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form__control">
                        <label for="display_order">Sıra No</label>
                        <input type="number" id="display_order" name="display_order"
                               value="<?= (int)$img['display_order'] ?>" min="0" max="255">
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                               <?= $img['is_featured'] ? 'checked' : '' ?>>
                        <label for="is_featured">Öne çıkan görsel</label>
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               <?= $img['is_active'] ? 'checked' : '' ?>>
                        <label for="is_active">Aktif (sitede göster)</label>
                    </div>

                    <button type="submit" class="btn btn--full">
                        <i class="uil uil-check" aria-hidden="true"></i>
                        Güncelle
                    </button>
                </form>
            </div>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
