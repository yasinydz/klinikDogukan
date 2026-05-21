<?php
/**
 * templates/admin/pages/posts/create.php
 * URL: /admin/posts/create
 */

$pageTitle  = 'Yeni Yazı';
$activeMenu = 'posts';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$categories = mysqli_query($connection,
    "SELECT id, title FROM post_categories ORDER BY title ASC"
);

$servicesList = null;
$chkSvc = mysqli_query($connection, "SHOW TABLES LIKE 'services'");
if ($chkSvc && mysqli_num_rows($chkSvc) > 0) {
    $servicesList = mysqli_query($connection,
        "SELECT slug, title FROM services WHERE is_active = 1 ORDER BY display_order ASC"
    );
}

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Yeni Yazı</h2>
                <a href="<?= ROOT_URL ?>admin/posts" class="btn sm outline">
                    <i class="uil uil-arrow-left"></i> Geri
                </a>
            </div>

            <form action="<?= ROOT_URL ?>admin/posts/create"
                  enctype="multipart/form-data"
                  method="POST"
                  style="display:flex;flex-direction:column;gap:var(--space-5);">

                <?= csrfField() ?>

                <!-- Temel Bilgiler -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-pen"></i> Yazı Bilgileri</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">

                        <div class="form__control">
                            <label for="title">Başlık *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= e($old['title'] ?? '') ?>"
                                   placeholder="Yazı başlığı" required maxlength="255"
                                   aria-describedby="title_count">
                            <small id="title_count" style="color:var(--color-text-faint);">0 / 255</small>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label for="category_id">Kategori *</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Kategori seçin</option>
                                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= (int)$cat['id'] ?>"
                                        <?= ((string)($old['category_id'] ?? '')) === (string)$cat['id'] ? 'selected' : '' ?>>
                                        <?= e($cat['title']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form__control">
                                <label for="related_service_slug">
                                    İlgili Hizmet
                                    <small style="font-weight:400;color:var(--color-text-faint);">
                                        (yazı sonunda CTA gösterir)
                                    </small>
                                </label>
                                <select id="related_service_slug" name="related_service_slug">
                                    <option value="">— Seçiniz —</option>
                                    <?php if ($servicesList && mysqli_num_rows($servicesList) > 0):
                                        while ($svc = mysqli_fetch_assoc($servicesList)): ?>
                                    <option value="<?= e($svc['slug']) ?>"
                                        <?= ($old['related_service_slug'] ?? '') === $svc['slug'] ? 'selected' : '' ?>>
                                        <?= e($svc['title']) ?>
                                    </option>
                                    <?php endwhile; endif; ?>
                                </select>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control inline">
                                <input type="checkbox" id="is_published" name="is_published" value="1"
                                       <?= !empty($old['is_published']) ? 'checked' : 'checked' ?>>
                                <label for="is_published">Yayınla</label>
                            </div>
                            <div class="form__control inline">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                       <?= !empty($old['is_featured']) ? 'checked' : '' ?>>
                                <label for="is_featured">Öne Çıkar</label>
                            </div>
                        </div>

                        <div class="form__control">
                            <label for="thumbnail">
                                Kapak Görseli *
                                <small style="font-weight:400;color:var(--color-text-faint);">
                                    (jpg/jpeg/png, max 2MB)
                                </small>
                            </label>
                            <input type="file" id="thumbnail" name="thumbnail"
                                   accept=".jpg,.jpeg,.png" required
                                   onchange="previewThumb(this)">
                            <div id="thumb-preview" style="display:none;margin-top:var(--space-3);">
                                <img id="thumb-img" src="" alt="Kapak önizleme"
                                     style="max-width:100%;max-height:180px;border-radius:var(--radius-md);border:1.5px solid var(--color-border);object-fit:cover;">
                                <div id="thumb-ratio-warn" class="thumb-ratio-warn">
                                    <i class="uil uil-exclamation-triangle"></i>
                                    Önerilen: 1200×675 (16:9). Bu görsel farklı oranda.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SEO -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-search"></i> SEO Ayarları
                            <small style="font-weight:400;font-size:0.8rem;color:var(--color-text-faint);">
                                — boş bırakılırsa otomatik üretilir
                            </small>
                        </h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div class="form__control">
                            <label for="meta_title">
                                Meta Title
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 70 karakter)</small>
                            </label>
                            <input type="text" id="meta_title" name="meta_title"
                                   value="<?= e($old['meta_title'] ?? '') ?>"
                                   maxlength="70"
                                   placeholder="Boş bırakılırsa yazı başlığı kullanılır">
                            <small id="meta_title_count" style="color:var(--color-text-faint);">0 / 70</small>
                        </div>
                        <div class="form__control">
                            <label for="meta_desc">
                                Meta Description
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 160 karakter)</small>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="160"
                                      placeholder="Boş bırakılırsa içerikten otomatik üretilir"><?= e($old['meta_desc'] ?? '') ?></textarea>
                            <small id="meta_desc_count" style="color:var(--color-text-faint);">0 / 160</small>
                        </div>
                    </div>
                </div>

                <!-- İçerik -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-file-alt"></i> Yazı İçeriği *</h3>
                    </div>
                    <div style="padding:var(--space-5);">
                        <textarea id="tinymce-editor" name="body"><?= e($old['body'] ?? '') ?></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:var(--space-3);">
                    <button type="submit" name="submit" class="btn">
                        <i class="uil uil-check"></i> Yazıyı Ekle
                    </button>
                    <a href="<?= ROOT_URL ?>admin/posts" class="btn btn--outline">İptal</a>
                </div>

            </form>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/tinymce.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Thumbnail live preview */
    window.previewThumb = function(input) {
        var preview = document.getElementById('thumb-preview');
        var img = document.getElementById('thumb-img');
        var warn = document.getElementById('thumb-ratio-warn');
        if (!input.files || !input.files[0]) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
            img.onload = function() {
                var ratio = img.naturalWidth / img.naturalHeight;
                var ideal = 1200 / 675;
                warn.style.display = Math.abs(ratio - ideal) > 0.1 ? 'flex' : 'none';
            };
        };
        reader.readAsDataURL(input.files[0]);
    };

    ['title','meta_title','meta_desc'].forEach(function (id) {
        var el = document.getElementById(id);
        var counter = document.getElementById(id + '_count');
        if (!el || !counter) return;
        function update() {
            var len = el.value.length;
            counter.textContent = len + ' / ' + el.maxLength;
            counter.style.color = len > el.maxLength * 0.9 ? 'var(--color-warning)' : 'var(--color-text-faint)';
        }
        el.addEventListener('input', update);
        update();
    });

    initTinyMCE('#tinymce-editor');
});
</script>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
