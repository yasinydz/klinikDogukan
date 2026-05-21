<?php
/**
 * templates/admin/pages/city-pages/index.php
 * URL: /admin/city-pages
 */

$pageTitle  = 'Şehir Sayfaları';
$activeMenu = 'city-pages';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$cities = mysqli_query($connection,
    "SELECT * FROM city_pages ORDER BY city_key ASC"
);
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Şehir Sayfaları</h2>
            </div>

            <?php if ($cities && mysqli_num_rows($cities) > 0): ?>

            <?php while ($city = mysqli_fetch_assoc($cities)): ?>
            <div class="dashboard__panel" style="margin-bottom:var(--space-5);">
                <div class="dashboard__panel-header">
                    <h3>
                        <i class="uil uil-map-marker"></i>
                        <?= e($city['city_name']) ?> — /<?= e($city['city_key']) ?>-psikolog
                    </h3>
                    <div style="display:flex;gap:var(--space-2);">
                        <a href="<?= ROOT_URL . $city['city_key'] ?>-psikolog"
                           target="_blank"
                           class="btn sm outline">
                            <i class="uil uil-external-link-alt"></i> Görüntüle
                        </a>
                    </div>
                </div>
                <div style="padding:var(--space-5);">
                    <form action="<?= ROOT_URL ?>admin/city-pages"
                          method="POST"
                          style="display:flex;flex-direction:column;gap:var(--space-4);">

                        <?= csrfField() ?>
                        <input type="hidden" name="city_key"
                               value="<?= e($city['city_key']) ?>">

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label>H1 Başlığı</label>
                                <input type="text" name="h1_text"
                                       value="<?= e($city['h1_text'] ?? '') ?>"
                                       maxlength="200">
                            </div>
                            <div class="form__control">
                                <label>Meta Title
                                    <small style="font-weight:400;color:var(--color-text-faint);">(max 70)</small>
                                </label>
                                <input type="text" name="meta_title"
                                       value="<?= e($city['meta_title'] ?? '') ?>"
                                       maxlength="70">
                            </div>
                        </div>

                        <div class="form__control">
                            <label>Meta Description
                                <small style="font-weight:400;color:var(--color-text-faint);">(max 160)</small>
                            </label>
                            <textarea name="meta_desc" rows="2"
                                      maxlength="160"><?= e($city['meta_desc'] ?? '') ?></textarea>
                        </div>

                        <div class="form__control">
                            <label>Sayfa İçeriği
                                <small style="font-weight:400;color:var(--color-text-faint);">
                                    (opsiyonel — boş bırakılırsa varsayılan içerik kullanılır)
                                </small>
                            </label>
                            <textarea name="content" rows="4"><?= e($city['content'] ?? '') ?></textarea>
                        </div>

                        <div class="form__control inline">
                            <input type="checkbox" name="is_active" value="1"
                                   id="active_<?= e($city['city_key']) ?>"
                                   <?= $city['is_active'] ? 'checked' : '' ?>>
                            <label for="active_<?= e($city['city_key']) ?>">Aktif</label>
                        </div>

                        <div>
                            <button type="submit" class="btn sm">
                                <i class="uil uil-check"></i>
                                <?= e($city['city_name']) ?> Kaydet
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            <?php endwhile; ?>

            <?php else: ?>
            <div class="alert__message error">
                <p>Şehir verisi bulunamadı.
                   <code>database.sql</code>'i çalıştırarak varsayılan verileri yükleyin.
                </p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
