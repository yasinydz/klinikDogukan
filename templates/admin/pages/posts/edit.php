<?php
/**
 * templates/admin/pages/posts/edit.php
 * URL: /admin/posts/edit?id=N
 */

$pageTitle  = 'Yazıyı Düzenle';
$activeMenu = 'posts';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz yazı.');
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

$stmt = $connection->prepare("
    SELECT id, title, slug, body, thumbnail, category_id, is_featured,
           is_published, meta_title, meta_desc, related_service_slug
    FROM posts WHERE id = ? LIMIT 1
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Yazı bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/posts');
    exit;
}

$post       = $result->fetch_assoc();
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
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Yazıyı Düzenle</h2>
                <div style="display:flex;gap:var(--space-2);">
                    <a href="<?= ROOT_URL ?>blog/<?= urlencode($post['slug']) ?>"
                       target="_blank" class="btn sm outline">
                        <i class="uil uil-external-link-alt"></i> Siteye Git
                    </a>
                    <a href="<?= ROOT_URL ?>admin/posts" class="btn sm outline">
                        <i class="uil uil-arrow-left"></i> Geri
                    </a>
                </div>
            </div>

            <form action="<?= ROOT_URL ?>admin/posts/edit"
                  enctype="multipart/form-data"
                  method="POST"
                  style="display:flex;flex-direction:column;gap:var(--space-5);">

                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                <input type="hidden" name="previous_thumbnail"
                       value="<?= e($post['thumbnail']) ?>">

                <!-- URL -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-link"></i> URL</h3>
                    </div>
                    <div style="padding:var(--space-4) var(--space-5);">
                        <input type="text"
                               value="/blog/<?= e($post['slug']) ?>"
                               readonly
                               style="opacity:0.6;cursor:not-allowed;font-size:0.85rem;">
                    </div>
                </div>

                <!-- Temel Bilgiler -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-pen"></i> Yazı Bilgileri</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">

                        <div class="form__control">
                            <label for="title">Başlık *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= e($post['title']) ?>"
                                   required maxlength="255"
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
                                        <?= (int)$post['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= e($cat['title']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form__control">
                                <label for="related_service_slug">İlgili Hizmet</label>
                                <select id="related_service_slug" name="related_service_slug">
                                    <option value="">— Seçiniz —</option>
                                    <?php if ($servicesList && mysqli_num_rows($servicesList) > 0):
                                        while ($svc = mysqli_fetch_assoc($servicesList)): ?>
                                    <option value="<?= e($svc['slug']) ?>"
                                        <?= ($post['related_service_slug'] ?? '') === $svc['slug'] ? 'selected' : '' ?>>
                                        <?= e($svc['title']) ?>
                                    </option>
                                    <?php endwhile; endif; ?>
                                </select>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control inline">
                                <input type="checkbox" id="is_published" name="is_published" value="1"
                                       <?= $post['is_published'] ? 'checked' : '' ?>>
                                <label for="is_published">Yayınla</label>
                            </div>
                            <div class="form__control inline">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                       <?= $post['is_featured'] ? 'checked' : '' ?>>
                                <label for="is_featured">Öne Çıkar</label>
                            </div>
                        </div>

                        <!-- Mevcut Görsel -->
                        <div class="form__control">
                            <label>Kapak Görseli</label>
                            <div style="display:flex;align-items:center;gap:var(--space-4);">
                                <?php if (!empty($post['thumbnail'])): ?>
                                <img src="<?= ROOT_URL ?>images/uploads/<?= e($post['thumbnail']) ?>"
                                     alt="Mevcut kapak"
                                     style="width:80px;height:50px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--color-border);"
                                     onerror="this.style.display='none'">
                                <?php endif; ?>
                                <div class="form__control" style="margin:0;flex:1;">
                                    <label for="thumbnail" style="font-size:0.8rem;">
                                        Değiştirmek için seç (boş = mevcut kalır)
                                    </label>
                                    <input type="file" id="thumbnail" name="thumbnail"
                                           accept=".jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SEO -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-search"></i> SEO Ayarları</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div class="form__control">
                            <label for="meta_title">Meta Title
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 70 karakter)</small>
                            </label>
                            <input type="text" id="meta_title" name="meta_title"
                                   value="<?= e($post['meta_title'] ?? '') ?>"
                                   maxlength="70">
                            <small id="meta_title_count" style="color:var(--color-text-faint);">0 / 70</small>
                        </div>
                        <div class="form__control">
                            <label for="meta_desc">Meta Description
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 160 karakter)</small>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="160"><?= e($post['meta_desc'] ?? '') ?></textarea>
                            <small id="meta_desc_count" style="color:var(--color-text-faint);">0 / 160</small>
                        </div>
                    </div>
                </div>

                <!-- İçerik -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-file-alt"></i> Yazı İçeriği</h3>
                    </div>
                    <div style="padding:var(--space-5);">
                        <textarea id="tinymce-editor" name="body"><?= e($post['body']) ?></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:var(--space-3);">
                    <button type="submit" name="submit" class="btn">
                        <i class="uil uil-check"></i> Kaydet
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

    /* Thumbnail live preview */
    (function(){
        var thumbInput = document.getElementById('thumbnail');
        if (!thumbInput) return;
        thumbInput.addEventListener('change', function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                var existing = document.querySelector('.thumb-preview-edit');
                if (!existing) {
                    existing = document.createElement('div');
                    existing.className = 'thumb-preview-edit';
                    thumbInput.parentNode.insertBefore(existing, thumbInput.nextSibling);
                }
                existing.innerHTML = '<img src="'+e.target.result+'" style="max-width:100%;max-height:180px;border-radius:var(--radius-md);border:1.5px solid var(--color-border);margin-top:var(--space-3);display:block;object-fit:cover;">';
                var img = existing.querySelector('img');
                img.onload = function() {
                    var ratio = img.naturalWidth / img.naturalHeight;
                    var ideal = 1200/675;
                    if (Math.abs(ratio - ideal) > 0.1) {
                        var warn = existing.querySelector('.thumb-ratio-warn');
                        if (!warn) {
                            warn = document.createElement('div');
                            warn.className = 'thumb-ratio-warn';
                            warn.innerHTML = '<i class="uil uil-exclamation-triangle"></i> Önerilen: 1200×675 (16:9).';
                            existing.appendChild(warn);
                        }
                        warn.style.display = 'flex';
                    }
                };
            };
            reader.readAsDataURL(this.files[0]);
        });
    })();
</script>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
