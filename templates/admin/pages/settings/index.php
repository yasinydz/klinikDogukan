<?php
/**
 * templates/admin/pages/settings/index.php
 * URL: /admin/settings
 *
 * İki bölüm:
 *  1. Profil Ayarları (admin kullanıcı bilgileri)
 *  2. Site Ayarları (iletişim, analytics, sosyal medya, GBP)
 */

$pageTitle  = 'Ayarlar';
$activeMenu = 'settings';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';
require_once BASE_PATH . '/app/helpers/image.php';

// ── Mevcut admin verisini al ────────────────────────────────────
$adminId = (int) $_SESSION['admin_id'];
$adminStmt = $connection->prepare(
    "SELECT username, email, first_name, last_name, avatar FROM admins WHERE id = ? LIMIT 1"
);
$adminStmt->bind_param('i', $adminId);
$adminStmt->execute();
$admin = $adminStmt->get_result()->fetch_assoc();

$avatarUrl = ROOT_URL . 'images/uploads/' . ($admin['avatar'] ?? 'default.png');
if (($admin['avatar'] ?? 'default.png') === 'default.png') {
    $avatarUrl = ROOT_URL . 'images/default-avatar.png';
    // Dosya yoksa fallback
    if (!file_exists(PUBLIC_PATH . '/images/default-avatar.png')) {
        $avatarUrl = ROOT_URL . 'images/dogukan.png';
    }
}

// ── Site ayarlarını çek ─────────────────────────────────────────
$settingsRaw = mysqli_query($connection,
    "SELECT setting_key, setting_value, setting_group FROM settings ORDER BY setting_group, setting_key"
);
$settings = [];
while ($row = mysqli_fetch_assoc($settingsRaw)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

function settingVal(array $settings, string $key, string $default = ''): string {
    return $settings[$key] ?? $default;
}
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Ayarlar</h2>
            </div>

            <!-- ═══════════════════════════════════════════════════
                 PROFİL AYARLARI
                 ═══════════════════════════════════════════════════ -->
            <form action="<?= ROOT_URL ?>admin/profile"
                  method="POST"
                  enctype="multipart/form-data"
                  id="profile"
                  style="display:flex;flex-direction:column;gap:var(--space-5);margin-bottom:var(--space-8);">

                <?= csrfField() ?>

                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-user-circle"></i> Profil Bilgileri</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-5);">

                        <!-- Avatar -->
                        <div style="display:flex;align-items:center;gap:var(--space-5);flex-wrap:wrap;">
                            <img id="avatar-preview"
                                 src="<?= e($avatarUrl) ?>"
                                 alt="Profil fotoğrafı"
                                 style="width:80px;height:80px;border-radius:var(--radius-full);object-fit:cover;border:2px solid var(--color-border);">
                            <div class="form__control" style="flex:1;min-width:200px;">
                                <label for="avatar">Profil Fotoğrafı
                                    <small style="font-weight:400;color:var(--color-text-faint);">
                                        (jpg/png/webp, max 2MB)
                                    </small>
                                </label>
                                <input type="file" id="avatar" name="avatar"
                                       accept=".jpg,.jpeg,.png,.webp"
                                       onchange="previewAvatar(this)">
                            </div>
                        </div>

                        <!-- Ad / Soyad -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label for="first_name">Ad *</label>
                                <input type="text" id="first_name" name="first_name"
                                       value="<?= e($admin['first_name'] ?? '') ?>"
                                       placeholder="Adınız" required maxlength="50">
                            </div>
                            <div class="form__control">
                                <label for="last_name">Soyad *</label>
                                <input type="text" id="last_name" name="last_name"
                                       value="<?= e($admin['last_name'] ?? '') ?>"
                                       placeholder="Soyadınız" required maxlength="50">
                            </div>
                        </div>

                        <!-- Username / Email -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label for="username">Kullanıcı Adı *</label>
                                <input type="text" id="username" name="username"
                                       value="<?= e($admin['username'] ?? '') ?>"
                                       placeholder="admin" required maxlength="50" minlength="3">
                            </div>
                            <div class="form__control">
                                <label for="email">E-posta *</label>
                                <input type="email" id="email" name="email"
                                       value="<?= e($admin['email'] ?? '') ?>"
                                       placeholder="admin@example.com" required maxlength="150">
                            </div>
                        </div>

                        <!-- Şifre Değiştir (opsiyonel) -->
                        <details style="border:1px solid var(--color-border);border-radius:var(--radius-md);padding:var(--space-4);">
                            <summary style="cursor:pointer;font-weight:600;font-size:0.9rem;color:var(--color-text-muted);user-select:none;">
                                <i class="uil uil-lock" aria-hidden="true"></i>
                                Şifre Değiştir
                                <small style="font-weight:400;color:var(--color-text-faint);"> — boş bırakılırsa değişmez</small>
                            </summary>
                            <div style="display:flex;flex-direction:column;gap:var(--space-4);margin-top:var(--space-4);">
                                <div class="form__control">
                                    <label for="current_password">Mevcut Şifre</label>
                                    <input type="password" id="current_password" name="current_password"
                                           autocomplete="current-password">
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                                    <div class="form__control">
                                        <label for="new_password">Yeni Şifre</label>
                                        <input type="password" id="new_password" name="new_password"
                                               minlength="6" autocomplete="new-password">
                                    </div>
                                    <div class="form__control">
                                        <label for="confirm_password">Yeni Şifre (Tekrar)</label>
                                        <input type="password" id="confirm_password" name="confirm_password"
                                               autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                        </details>

                        <div>
                            <button type="submit" name="submit" class="btn">
                                <i class="uil uil-check"></i> Profili Güncelle
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ═══════════════════════════════════════════════════
                 SİTE AYARLARI
                 ═══════════════════════════════════════════════════ -->
            <form action="<?= ROOT_URL ?>admin/settings"
                  method="POST"
                  style="display:flex;flex-direction:column;gap:var(--space-5);">

                <?= csrfField() ?>

                <!-- İletişim Bilgileri -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-phone"></i> İletişim Bilgileri (NAP)</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label>Telefon</label>
                                <input type="text" name="contact_phone"
                                       value="<?= e(settingVal($settings, 'contact_phone', CONTACT_PHONE)) ?>"
                                       placeholder="+90 (500) 000 00 00">
                            </div>
                            <div class="form__control">
                                <label>WhatsApp Numarası</label>
                                <input type="text" name="contact_whatsapp"
                                       value="<?= e(settingVal($settings, 'contact_whatsapp', CONTACT_WHATSAPP)) ?>"
                                       placeholder="905000000000">
                                <small style="color:var(--color-text-faint);">Başında 90 ile, boşluksuz</small>
                            </div>
                            <div class="form__control">
                                <label>E-posta</label>
                                <input type="email" name="contact_email"
                                       value="<?= e(settingVal($settings, 'contact_email', CONTACT_EMAIL)) ?>">
                            </div>
                            <div class="form__control">
                                <label>Adres</label>
                                <input type="text" name="address_full"
                                       value="<?= e(settingVal($settings, 'address_full', ADDRESS_DISTRICT . ', ' . ADDRESS_CITY)) ?>">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label>Çalışma Saati (Hafta içi)</label>
                                <input type="text" name="work_hours_weekday"
                                       value="<?= e(settingVal($settings, 'work_hours_weekday', WORK_HOURS_WEEKDAY)) ?>">
                            </div>
                            <div class="form__control">
                                <label>Çalışma Saati (Cumartesi)</label>
                                <input type="text" name="work_hours_saturday"
                                       value="<?= e(settingVal($settings, 'work_hours_saturday', WORK_HOURS_SATURDAY)) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-analytics"></i> Analytics</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label>Google Analytics 4 ID</label>
                                <input type="text" name="ga4_id"
                                       value="<?= e(settingVal($settings, 'ga4_id', GA4_ID)) ?>"
                                       placeholder="G-XXXXXXXXXX">
                            </div>
                            <div class="form__control">
                                <label>Google Ads Conversion ID</label>
                                <input type="text" name="aw_id"
                                       value="<?= e(settingVal($settings, 'aw_id', AW_ID)) ?>"
                                       placeholder="AW-XXXXXXXXX">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sosyal Medya -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-instagram"></i> Sosyal Medya</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label>Instagram URL</label>
                                <input type="url" name="social_instagram"
                                       value="<?= e(settingVal($settings, 'social_instagram', SOCIAL_INSTAGRAM)) ?>"
                                       placeholder="https://www.instagram.com/...">
                            </div>
                            <div class="form__control">
                                <label>Facebook URL</label>
                                <input type="url" name="social_facebook"
                                       value="<?= e(settingVal($settings, 'social_facebook', SOCIAL_FACEBOOK)) ?>">
                            </div>
                            <div class="form__control">
                                <label>LinkedIn URL</label>
                                <input type="url" name="social_linkedin"
                                       value="<?= e(settingVal($settings, 'social_linkedin', SOCIAL_LINKEDIN)) ?>">
                            </div>
                            <div class="form__control">
                                <label>YouTube URL</label>
                                <input type="url" name="social_youtube"
                                       value="<?= e(settingVal($settings, 'social_youtube', SOCIAL_YOUTUBE)) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Business Profile -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-map-marker"></i> Google Business Profile</h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <div class="form__control">
                            <label>GBP Profile URL</label>
                            <input type="url" name="gbp_profile_url"
                                   value="<?= e(settingVal($settings, 'gbp_profile_url', GBP_PROFILE_URL)) ?>"
                                   placeholder="https://g.page/...">
                        </div>
                        <div class="form__control">
                            <label>GBP Review URL</label>
                            <input type="url" name="gbp_review_url"
                                   value="<?= e(settingVal($settings, 'gbp_review_url', GBP_REVIEW_URL)) ?>"
                                   placeholder="Yorum bırakma linki">
                        </div>
                    </div>
                </div>

                <!-- Public Uzman Profili -->
                <div class="dashboard__panel">
                    <div class="dashboard__panel-header">
                        <h3><i class="uil uil-user-md"></i> Public Uzman Profili
                            <small style="font-weight:400;font-size:0.8rem;color:var(--color-text-faint);">
                                — site üzerinde görünen uzman bilgileri
                            </small>
                        </h3>
                    </div>
                    <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:var(--space-4);">
                        <p style="font-size:0.82rem;color:var(--color-text-faint);margin-bottom:var(--space-2);">
                            <i class="uil uil-info-circle" aria-hidden="true"></i>
                            Ad, soyad ve avatar "Profil Bilgileri" bölümünden yönetilir.
                            Aşağıdaki alanlar site üzerinde görünen uzman içeriklerini kontrol eder.
                        </p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                            <div class="form__control">
                                <label>Unvan / Title</label>
                                <input type="text" name="public_title"
                                       value="<?= e(settingVal($settings, 'public_title', PSYCHOLOGIST_TITLE)) ?>"
                                       placeholder="Klinik Psikolog" maxlength="100">
                            </div>
                            <div class="form__control">
                                <label>Deneyim (yıl)</label>
                                <input type="number" name="public_experience_years"
                                       value="<?= e(settingVal($settings, 'public_experience_years', (string)PSYCHOLOGIST_EXPERIENCE_YEARS)) ?>"
                                       min="0" max="50">
                            </div>
                        </div>
                        <div class="form__control">
                            <label>Kısa Tagline</label>
                            <input type="text" name="public_tagline"
                                   value="<?= e(settingVal($settings, 'public_tagline', 'Klinik Psikolog | Bilişsel Davranışçı Terapi Uzmanı')) ?>"
                                   placeholder="Klinik Psikolog | BDT Uzmanı" maxlength="200">
                        </div>
                        <div class="form__control">
                            <label>Terapi Yaklaşımı</label>
                            <textarea name="public_approach" rows="3"
                                      maxlength="1000"
                                      placeholder="Bilişsel Davranışçı Terapi (BDT), EMDR ve şema terapi..."><?= e(settingVal($settings, 'public_approach', PSYCHOLOGIST_APPROACH)) ?></textarea>
                        </div>
                        <div class="form__control">
                            <label>Uzmanlık Alanları
                                <small style="font-weight:400;color:var(--color-text-faint);">(virgülle ayırın)</small>
                            </label>
                            <input type="text" name="public_specialties"
                                   value="<?= e(settingVal($settings, 'public_specialties', PSYCHOLOGIST_SPECIALTIES)) ?>"
                                   placeholder="Anksiyete, Depresyon, Travma, OKB">
                        </div>
                        <div class="form__control">
                            <label>Sertifikalar
                                <small style="font-weight:400;color:var(--color-text-faint);">(virgülle ayırın)</small>
                            </label>
                            <input type="text" name="public_certifications"
                                   value="<?= e(settingVal($settings, 'public_certifications', PSYCHOLOGIST_CERTIFICATIONS)) ?>"
                                   placeholder="Klinik Psikoloji YL, BDT Sertifikası, EMDR Sertifikası">
                        </div>
                        <div class="form__control">
                            <label>Hakkımda / Bio</label>
                            <textarea name="public_bio" rows="4"
                                      maxlength="2000"
                                      placeholder="İzmit ve Kocaeli'de bireysel terapi..."><?= e(settingVal($settings, 'public_bio', PSYCHOLOGIST_BIO)) ?></textarea>
                        </div>
                        <div class="form__control">
                            <label>Hero / Tanıtım Metni
                                <small style="font-weight:400;color:var(--color-text-faint);">(hakkımda sayfası intro)</small>
                            </label>
                            <textarea name="public_hero_intro" rows="3"
                                      maxlength="1000"><?= e(settingVal($settings, 'public_hero_intro', '')) ?></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" name="submit" class="btn">
                        <i class="uil uil-check"></i> Ayarları Kaydet
                    </button>
                </div>

            </form>
        </main>
    </div>
</section>

<script>
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('avatar-preview').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
