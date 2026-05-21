<?php
/**
 * templates/admin/pages/services/create.php
 * URL: /admin/services/create
 */

$pageTitle  = 'Yeni Hizmet';
$activeMenu = 'services';

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
                <h2>Yeni Hizmet</h2>
                <a href="<?= ROOT_URL ?>admin/services" class="btn sm outline">
                    <i class="uil uil-arrow-left"></i> Geri
                </a>
            </div>

            <form action="<?= ROOT_URL ?>admin/services/create"
                  enctype="multipart/form-data"
                  method="POST"
                  style="display:flex;flex-direction:column;gap:var(--space-5);">

                <?= csrfField() ?>

                <!-- Temel Bilgiler -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-info-circle"></i> Temel Bilgiler</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">

                        <div class="form__control">
                            <label for="title">Hizmet Başlığı *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= e($old['title'] ?? '') ?>"
                                   placeholder="ör: Anksiyete Terapisi"
                                   required maxlength="200">
                        </div>

                        <div class="form__control">
                            <label for="summary">
                                Kısa Açıklama *
                                <small style="font-weight:400;color:var(--color-text-faint);">
                                    (kart ve liste görünümleri, max 300 karakter)
                                </small>
                            </label>
                            <textarea id="summary" name="summary"
                                      rows="3" maxlength="300"
                                      placeholder="Bu hizmet hakkında kısa bir açıklama..."
                                      required><?= e($old['summary'] ?? '') ?></textarea>
                            <small id="summary_count" style="color:var(--color-text-faint);">0 / 300</small>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 80px;gap:var(--space-4);">
                            <div class="form__control">
                                <label for="icon_class">
                                    İkon Class
                                    <small style="font-weight:400;color:var(--color-text-faint);">
                                        (<a href="https://iconscout.com/unicons/explore/line"
                                            target="_blank" rel="noopener">Unicons</a>)
                                    </small>
                                </label>
                                <input type="text" id="icon_class" name="icon_class"
                                       value="<?= e($old['icon_class'] ?? '') ?>"
                                       placeholder="uil uil-heart-rate">
                            </div>
                            <div class="form__control">
                                <label for="display_order">Sıralama</label>
                                <input type="number" id="display_order" name="display_order"
                                       value="<?= e($old['display_order'] ?? '0') ?>"
                                       min="0" max="99">
                            </div>
                            <div class="form__control" style="justify-content:flex-end;padding-top:var(--space-6);">
                                <label class="form__control inline" style="margin-bottom:0;">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           <?= !isset($old['is_active']) || $old['is_active'] ? 'checked' : '' ?>>
                                    <span>Yayında</span>
                                </label>
                            </div>
                        </div>

                        <!-- İkon önizleme -->
                        <div style="display:flex;align-items:center;gap:var(--space-3);">
                            <span style="font-size:0.85rem;color:var(--color-text-faint);">Önizleme:</span>
                            <i id="icon-preview"
                               class="uil uil-heart"
                               style="font-size:1.5rem;color:var(--color-accent);"
                               aria-hidden="true"></i>
                        </div>

                    </div>
                </div>

                <!-- Hizmet Görseli -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-image"></i> Hizmet Görseli
                            <small style="font-weight:400;font-size:0.8rem;color:var(--color-text-faint);">
                                — opsiyonel, kart ve detay sayfasında gösterilir
                            </small>
                        </h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div class="form__control">
                            <label for="service_image">
                                Görsel
                                <small style="font-weight:400;color:var(--color-text-faint);">
                                    (jpg/jpeg/png, max 2MB)
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
                        <h3><i class="uil uil-search"></i> SEO Ayarları
                            <small style="font-weight:400;font-size:0.8rem;color:var(--color-text-faint);">
                                — boş = otomatik üretilir
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
                                   placeholder="ör: Anksiyete Terapisi | Psikolog Doğukan Kopuk — İzmit">
                            <small id="meta_title_count" style="color:var(--color-text-faint);">0 / 70</small>
                        </div>

                        <div class="form__control">
                            <label for="meta_desc">
                                Meta Description
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 160 karakter)</small>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="160"
                                      placeholder="Google arama sonuçlarında görünecek açıklama..."><?= e($old['meta_desc'] ?? '') ?></textarea>
                            <small id="meta_desc_count" style="color:var(--color-text-faint);">0 / 160</small>
                        </div>

                    </div>
                </div>

                <!-- Hizmet İçeriği -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-file-alt"></i> Hizmet İçeriği
                            <small style="font-weight:400;font-size:0.8rem;color:var(--color-text-faint);">
                                (hizmet detay sayfasında görünür)
                            </small>
                        </h3>
                    </div>
                    <div style="padding:var(--space-5);">
                        <textarea id="tinymce-editor"
                                  name="content"><?= e($old['content'] ?? '') ?></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:var(--space-3);">
                    <button type="submit" name="submit" class="btn">
                        <i class="uil uil-check" aria-hidden="true"></i>
                        Hizmeti Kaydet
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
    // Karakter sayaçları
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
            counter.style.color = len > pair[1] * 0.9
                ? 'var(--color-warning)'
                : 'var(--color-text-faint)';
        }
        el.addEventListener('input', update);
        update();
    });

    // İkon önizleme
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
