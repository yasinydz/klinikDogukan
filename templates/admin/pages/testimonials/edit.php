<?php
/**
 * templates/admin/pages/testimonials/edit.php
 * GET /admin/testimonials/edit?id=N
 */

$pageTitle  = 'Yorum Düzenle';
$activeMenu = 'testimonials';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . ROOT_URL . 'admin/testimonials');
    exit;
}

$stmt = $connection->prepare("SELECT * FROM testimonials WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) {
    flashSet('error', 'Yorum bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/testimonials');
    exit;
}

require_once BASE_PATH . '/templates/admin/partials/header.php';
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Yorum Düzenle</h2>
                <a href="<?= ROOT_URL ?>admin/testimonials" class="btn btn--outline">← Yorumlara Dön</a>
            </div>

            <div class="form__section-container form__section-container--wide" style="max-width:640px;">
                <form action="<?= ROOT_URL ?>admin/testimonials/edit"
                      method="POST">

                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">

                    <div class="form__control">
                        <label for="author_name">Ad Soyad *</label>
                        <input type="text" id="author_name" name="author_name"
                               value="<?= e($t['author_name']) ?>" required maxlength="100">
                    </div>

                    <div class="form__control">
                        <label for="author_title">Unvan / Tanım</label>
                        <input type="text" id="author_title" name="author_title"
                               value="<?= e($t['author_title']) ?>" maxlength="150">
                    </div>

                    <div class="form__control">
                        <label for="content">Yorum Metni *</label>
                        <textarea id="content" name="content" rows="4" required><?= e($t['content']) ?></textarea>
                    </div>

                    <div class="form__control">
                        <label for="rating">Puan (1-5)</label>
                        <select id="rating" name="rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= (int)$t['rating'] === $i ? 'selected' : '' ?>>
                                <?= str_repeat('★', $i) . str_repeat('☆', 5-$i) ?> (<?= $i ?>)
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form__control">
                        <label for="service_type">Hizmet Tipi</label>
                        <input type="text" id="service_type" name="service_type"
                               value="<?= e($t['service_type'] ?? '') ?>" maxlength="100">
                    </div>

                    <div class="form__control">
                        <label for="display_order">Sıra No</label>
                        <input type="number" id="display_order" name="display_order"
                               value="<?= (int)$t['display_order'] ?>" min="0" max="255">
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                               <?= $t['is_featured'] ? 'checked' : '' ?>>
                        <label for="is_featured">Öne çıkan yorum</label>
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_google" name="is_google" value="1"
                               <?= $t['is_google'] ? 'checked' : '' ?>>
                        <label for="is_google">Google yorumu</label>
                    </div>

                    <div class="form__control" id="google_url_wrap"
                         style="<?= $t['is_google'] ? '' : 'display:none;' ?>">
                        <label for="google_url">Google Yorum URL</label>
                        <input type="url" id="google_url" name="google_url"
                               value="<?= e($t['google_url'] ?? '') ?>" maxlength="500">
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               <?= $t['is_active'] ? 'checked' : '' ?>>
                        <label for="is_active">Aktif (sitede göster)</label>
                    </div>

                    <button type="submit" class="btn btn--full">
                        <i class="uil uil-check" aria-hidden="true"></i>
                        Güncelle
                    </button>
                </form>
            </div>

            <script>
            document.getElementById('is_google').addEventListener('change', function() {
                document.getElementById('google_url_wrap').style.display = this.checked ? 'flex' : 'none';
            });
            </script>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
