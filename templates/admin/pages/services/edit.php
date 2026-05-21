<?php
/**
 * templates/admin/pages/services/edit.php
 * URL: /admin/services/edit?id=N
 */

$pageTitle  = 'Hizmeti Düzenle';
$activeMenu = 'services';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    flashSet('error', 'Geçersiz hizmet.');
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

$stmt = $connection->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    flashSet('error', 'Hizmet bulunamadı.');
    header('Location: ' . ROOT_URL . 'admin/services');
    exit;
}

$service = $result->fetch_assoc();
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Hizmeti Düzenle</h2>
                <div style="display:flex;gap:var(--space-2);">
                    <a href="<?= ROOT_URL ?>hizmetler/<?= urlencode($service['slug']) ?>"
                       target="_blank" class="btn sm outline">
                        <i class="uil uil-external-link-alt"></i> Siteye Git
                    </a>
                    <a href="<?= ROOT_URL ?>admin/services" class="btn sm outline">
                        <i class="uil uil-arrow-left"></i> Geri
                    </a>
                </div>
            </div>

            <!-- Slug (readonly) -->
            <div class="dashboard__panel" style="margin-bottom:var(--space-5);">
                <div class="dashboard__panel-header">
                    <h3><i class="uil uil-link"></i> URL Slug (değiştirilemez — SEO için sabit)</h3>
                </div>
                <div style="padding:var(--space-4) var(--space-5);">
                    <input type="text"
                           value="/hizmetler/<?= e($service['slug']) ?>"
                           readonly
                           style="opacity:0.6;cursor:not-allowed;">
                </div>
            </div>

            <form action="<?= ROOT_URL ?>admin/services/edit"
                  enctype="multipart/form-data"
                  method="POST"
                  style="display:flex;flex-direction:column;gap:var(--space-5);">

                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int)$service['id'] ?>">

                <!-- Temel Bilgiler -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-info-circle"></i> Temel Bilgiler</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">

                        <div class="form__control">
                            <label for="title">Hizmet Başlığı *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= e($service['title']) ?>"
                                   required maxlength="200">
                        </div>

                        <div class="form__control">
                            <label for="summary">
                                Kısa Açıklama *
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 300 karakter)</small>
                            </label>
                            <textarea id="summary" name="summary"
                                      rows="3" maxlength="300"
                                      required><?= e($service['summary'] ?? '') ?></textarea>
                            <small id="summary_count" style="color:var(--color-text-faint);">0 / 300</small>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 80px;gap:var(--space-4);">
                            <div class="form__control">
                                <label for="icon_class">İkon Class</label>
                                <input type="text" id="icon_class" name="icon_class"
                                       value="<?= e($service['icon_class'] ?? '') ?>"
                                       placeholder="uil uil-heart-rate">
                            </div>
                            <div class="form__control">
                                <label for="display_order">Sıralama</label>
                                <input type="number" id="display_order" name="display_order"
                                       value="<?= (int)$service['display_order'] ?>"
                                       min="0" max="99">
                            </div>
                            <div class="form__control" style="justify-content:flex-end;padding-top:var(--space-6);">
                                <label class="form__control inline" style="margin-bottom:0;">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           <?= $service['is_active'] ? 'checked' : '' ?>>
                                    <span>Yayında</span>
                                </label>
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;gap:var(--space-3);">
                            <span style="font-size:0.85rem;color:var(--color-text-faint);">Önizleme:</span>
                            <i id="icon-preview"
                               class="<?= e($service['icon_class'] ?? 'uil uil-heart') ?>"
                               style="font-size:1.5rem;color:var(--color-accent);"
                               aria-hidden="true"></i>
                        </div>

                    </div>
                </div>

                <!-- Hizmet Görseli -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-image"></i> Hizmet Görseli</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <?php if (!empty($service['image'])): ?>
                        <div>
                            <img src="<?= getImageUrl($service['image'], 'thumb') ?>"
                                 alt="Mevcut görsel"
                                 style="max-width:240px;border-radius:var(--radius-md);border:1.5px solid var(--color-border);">
                            <div style="margin-top:var(--space-2);">
                                <label class="form__control inline" style="margin:0;">
                                    <input type="checkbox" name="remove_image" value="1">
                                    <span style="font-size:0.85rem;">Görseli kaldır</span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form__control">
                            <label for="service_image">
                                <?= !empty($service['image']) ? 'Yeni Görsel Yükle' : 'Görsel Yükle' ?>
                                <small style="font-weight:400;color:var(--color-text-faint);">
                                    (jpg/jpeg/png, max 2MB<?= !empty($service['image']) ? ' — boş bırakılırsa mevcut korunur' : '' ?>)
                                </small>
                            </label>
                            <input type="file" id="service_image" name="service_image"
                                   accept=".jpg,.jpeg,.png">
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
                                   value="<?= e($service['meta_title'] ?? '') ?>"
                                   maxlength="70">
                            <small id="meta_title_count" style="color:var(--color-text-faint);">0 / 70</small>
                        </div>

                        <div class="form__control">
                            <label for="meta_desc">Meta Description
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 160 karakter)</small>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="160"><?= e($service['meta_desc'] ?? '') ?></textarea>
                            <small id="meta_desc_count" style="color:var(--color-text-faint);">0 / 160</small>
                        </div>

                    </div>
                </div>

                <!-- İçerik -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-file-alt"></i> Hizmet İçeriği</h3>
                    </div>
                    <div style="padding:var(--space-5);">
                        <textarea id="tinymce-editor"
                                  name="content"><?= e($service['content'] ?? '') ?></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:var(--space-3);">
                    <button type="submit" name="submit" class="btn">
                        <i class="uil uil-check" aria-hidden="true"></i>
                        Değişiklikleri Kaydet
                    </button>
                    <a href="<?= ROOT_URL ?>admin/services" class="btn btn--outline">İptal</a>
                </div>

            </form>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/tinymce.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    [
        ['summary',    300],
        ['meta_title', 70],
        ['meta_desc',  160],
    ].forEach(function (pair) {
        var el      = document.getElementById(pair[0]);
        var counter = document.getElementById(pair[0] + '_count');
        if (!el || !counter) return;
        function update() {
            var len = el.value.length;
            counter.textContent = len + ' / ' + pair[1];
            counter.style.color = len > pair[1] * 0.9 ? 'var(--color-warning)' : 'var(--color-text-faint)';
        }
        el.addEventListener('input', update);
        update();
    });

    var iconInput   = document.getElementById('icon_class');
    var iconPreview = document.getElementById('icon-preview');
    if (iconInput && iconPreview) {
        iconInput.addEventListener('input', function () {
            iconPreview.className = this.value.trim() || 'uil uil-heart';
        });
    }

    initTinyMCE('#tinymce-editor');
});
</script>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
