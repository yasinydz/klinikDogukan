<?php
/**
 * templates/admin/pages/testimonials/create.php
 * GET /admin/testimonials/create
 */

$pageTitle  = 'Yorum Ekle';
$activeMenu = 'testimonials';

require_once BASE_PATH . '/templates/admin/partials/header.php';
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Yorum Ekle</h2>
                <a href="<?= ROOT_URL ?>admin/testimonials" class="btn btn--outline">← Yorumlara Dön</a>
            </div>

            <div class="form__section-container form__section-container--wide" style="max-width:640px;">
                <form action="<?= ROOT_URL ?>admin/testimonials/create"
                      method="POST">

                    <?= csrfField() ?>

                    <div class="form__control">
                        <label for="author_name">Ad Soyad *</label>
                        <input type="text" id="author_name" name="author_name"
                               placeholder="ör: A.K." required maxlength="100">
                        <small class="text-muted">Gizlilik için baş harfler kullanılabilir</small>
                    </div>

                    <div class="form__control">
                        <label for="author_title">Unvan / Tanım</label>
                        <input type="text" id="author_title" name="author_title"
                               placeholder="ör: Bireysel Terapi Danışanı" maxlength="150">
                    </div>

                    <div class="form__control">
                        <label for="content">Yorum Metni *</label>
                        <textarea id="content" name="content" rows="4"
                                  placeholder="Danışan yorumu..." required></textarea>
                    </div>

                    <div class="form__control">
                        <label for="rating">Puan (1-5)</label>
                        <select id="rating" name="rating">
                            <option value="5">★★★★★ (5)</option>
                            <option value="4">★★★★☆ (4)</option>
                            <option value="3">★★★☆☆ (3)</option>
                            <option value="2">★★☆☆☆ (2)</option>
                            <option value="1">★☆☆☆☆ (1)</option>
                        </select>
                    </div>

                    <div class="form__control">
                        <label for="service_type">Hizmet Tipi</label>
                        <input type="text" id="service_type" name="service_type"
                               placeholder="ör: Anksiyete Terapisi" maxlength="100">
                    </div>

                    <div class="form__control">
                        <label for="display_order">Sıra No</label>
                        <input type="number" id="display_order" name="display_order" value="0" min="0" max="255">
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1">
                        <label for="is_featured">Öne çıkan yorum</label>
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_google" name="is_google" value="1">
                        <label for="is_google">Google yorumu</label>
                    </div>

                    <div class="form__control" id="google_url_wrap" style="display:none;">
                        <label for="google_url">Google Yorum URL</label>
                        <input type="url" id="google_url" name="google_url"
                               placeholder="https://g.page/..." maxlength="500">
                    </div>

                    <div class="form__control inline">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label for="is_active">Aktif (sitede göster)</label>
                    </div>

                    <button type="submit" class="btn btn--full">
                        <i class="uil uil-check" aria-hidden="true"></i>
                        Yorumu Kaydet
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
