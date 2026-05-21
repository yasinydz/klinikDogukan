<?php
/**
 * templates/admin/pages/local-trust/index.php
 * URL: /admin/local-trust
 *
 * Google Business Profile, Maps, NAP, çalışma saatleri — tek ekran.
 */

$pageTitle  = 'Konum & GBP';
$activeMenu = 'local-trust';

require_once BASE_PATH . '/templates/admin/partials/header.php';

// Tüm settings'i çek
$settingsRaw = mysqli_query($connection, "SELECT setting_key, setting_value FROM settings");
$s = [];
while ($row = mysqli_fetch_assoc($settingsRaw)) {
    $s[$row['setting_key']] = $row['setting_value'];
}

function sv(array $s, string $key, string $default = ''): string {
    return $s[$key] ?? $default;
}

// Durum kartları için kontroller
$checks = [
    ['label' => 'GBP Profil URL',   'ok' => !empty($s['gbp_profile_url']),  'icon' => 'uil-google'],
    ['label' => 'GBP Review URL',   'ok' => !empty($s['gbp_review_url']),   'icon' => 'uil-star'],
    ['label' => 'GBP Place ID',     'ok' => !empty($s['gbp_place_id']),     'icon' => 'uil-map-pin'],
    ['label' => 'Harita Embed URL', 'ok' => !empty($s['maps_embed_url']),   'icon' => 'uil-map'],
    ['label' => 'Adres',            'ok' => !empty($s['address_full']),      'icon' => 'uil-map-marker'],
    ['label' => 'Telefon',          'ok' => !empty($s['contact_phone']),     'icon' => 'uil-phone'],
];
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Konum & Google Business Profile</h2>
            </div>

            <!-- Durum Kartları -->
            <div class="dashboard__stats" style="margin-bottom:var(--space-7);">
                <?php foreach ($checks as $check): ?>
                <div class="dashboard__stat-card" style="border-left:3px solid <?= $check['ok'] ? 'var(--color-success)' : 'var(--color-danger)' ?>;">
                    <i class="uil <?= e($check['icon']) ?>" style="color:<?= $check['ok'] ? 'var(--color-success)' : 'var(--color-danger)' ?>;"></i>
                    <div class="stat-label"><?= e($check['label']) ?></div>
                    <div style="font-size:0.82rem;font-weight:600;color:<?= $check['ok'] ? 'var(--color-success)' : 'var(--color-danger)' ?>;">
                        <?= $check['ok'] ? '✓ Tanımlı' : '✗ Eksik' ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Form -->
            <form action="<?= ROOT_URL ?>admin/local-trust" method="POST">
                <?= csrfField() ?>

                <!-- GBP -->
                <div class="dashboard__panel" style="margin-bottom:var(--space-6);">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-google"></i> Google Business Profile</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="gbp_profile_url">GBP Profil URL</label>
                            <input type="url" id="gbp_profile_url" name="gbp_profile_url"
                                   value="<?= e(sv($s, 'gbp_profile_url')) ?>"
                                   placeholder="https://g.page/psikolog-dogukan-kopuk">
                        </div>
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="gbp_review_url">GBP Yorum URL</label>
                            <input type="url" id="gbp_review_url" name="gbp_review_url"
                                   value="<?= e(sv($s, 'gbp_review_url')) ?>"
                                   placeholder="https://g.page/psikolog-dogukan-kopuk/review">
                        </div>
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="gbp_place_id">GBP Place ID</label>
                            <input type="text" id="gbp_place_id" name="gbp_place_id"
                                   value="<?= e(sv($s, 'gbp_place_id')) ?>"
                                   placeholder="ChIJ...">
                            <small class="text-muted">Google Maps > Paylaş > Yer kimliğinden alınır</small>
                        </div>
                    </div>
                </div>

                <!-- Harita -->
                <div class="dashboard__panel" style="margin-bottom:var(--space-6);">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-map"></i> Harita Ayarları</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="maps_embed_url">Google Maps Embed URL</label>
                            <input type="url" id="maps_embed_url" name="maps_embed_url"
                                   value="<?= e(sv($s, 'maps_embed_url')) ?>"
                                   placeholder="https://www.google.com/maps/embed?pb=...">
                            <small class="text-muted">Google Maps > Paylaş > Harita yerleştir > src URL'si</small>
                        </div>
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="maps_directions_url">Yol Tarifi URL</label>
                            <input type="url" id="maps_directions_url" name="maps_directions_url"
                                   value="<?= e(sv($s, 'maps_directions_url')) ?>"
                                   placeholder="https://www.google.com/maps/dir//...">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-3);">
                            <div class="form__control inline" style="margin-bottom:0;">
                                <input type="checkbox" id="maps_show_home" name="maps_show_home" value="1"
                                       <?= sv($s, 'maps_show_home', '1') === '1' ? 'checked' : '' ?>>
                                <label for="maps_show_home">Anasayfa</label>
                            </div>
                            <div class="form__control inline" style="margin-bottom:0;">
                                <input type="checkbox" id="maps_show_contact" name="maps_show_contact" value="1"
                                       <?= sv($s, 'maps_show_contact', '1') === '1' ? 'checked' : '' ?>>
                                <label for="maps_show_contact">İletişim</label>
                            </div>
                            <div class="form__control inline" style="margin-bottom:0;">
                                <input type="checkbox" id="maps_show_footer" name="maps_show_footer" value="1"
                                       <?= sv($s, 'maps_show_footer', '0') === '1' ? 'checked' : '' ?>>
                                <label for="maps_show_footer">Footer</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ulaşım Notları -->
                <div class="dashboard__panel" style="margin-bottom:var(--space-6);">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-bus"></i> Ulaşım Notları</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="maps_transport_note">Toplu Taşıma Notu</label>
                            <input type="text" id="maps_transport_note" name="maps_transport_note"
                                   value="<?= e(sv($s, 'maps_transport_note')) ?>"
                                   placeholder="ör: İzmit tramvay durağına 2 dakika yürüme mesafesinde">
                        </div>
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="maps_parking_note">Otopark Notu</label>
                            <input type="text" id="maps_parking_note" name="maps_parking_note"
                                   value="<?= e(sv($s, 'maps_parking_note')) ?>"
                                   placeholder="ör: Bina altı açık otopark mevcuttur">
                        </div>
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="maps_inperson_note">Yüz Yüze Görüşme Notu</label>
                            <input type="text" id="maps_inperson_note" name="maps_inperson_note"
                                   value="<?= e(sv($s, 'maps_inperson_note')) ?>"
                                   placeholder="ör: İzmit merkezde sessiz ve huzurlu ofis ortamı">
                        </div>
                        <div class="form__control" style="margin-bottom:0;">
                            <label for="maps_online_note">Online Görüşme Notu</label>
                            <input type="text" id="maps_online_note" name="maps_online_note"
                                   value="<?= e(sv($s, 'maps_online_note', 'Türkiye genelinde online görüşme imkânı mevcuttur.')) ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn--full" style="max-width:400px;">
                    <i class="uil uil-check" aria-hidden="true"></i>
                    Kaydet
                </button>
            </form>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
