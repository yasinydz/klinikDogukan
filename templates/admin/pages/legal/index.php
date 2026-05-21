<?php
/**
 * templates/admin/pages/legal/index.php
 * URL: /admin/legal
 */

$pageTitle  = 'Hukuki Metinler';
$activeMenu = 'legal';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/csrf.php';
require_once BASE_PATH . '/app/helpers/flash.php';

// legal_pages tablosu var mı?
$tableExists = false;
$chk = mysqli_query($connection, "SHOW TABLES LIKE 'legal_pages'");
if ($chk && mysqli_num_rows($chk) > 0) {
    $tableExists = true;
}

// Mevcut metinleri çek
$legalPages = [];
if ($tableExists) {
    $result = mysqli_query($connection,
        "SELECT * FROM legal_pages ORDER BY page_key ASC"
    );
    while ($row = mysqli_fetch_assoc($result)) {
        $legalPages[$row['page_key']] = $row;
    }
}

$pageKeys = [
    'kvkk'       => 'KVKK Aydınlatma Metni',
    'privacy'    => 'Gizlilik Politikası',
    'cookie'     => 'Çerez Politikası',
    'consent'    => 'Açık Rıza Metni',
    'commercial' => 'Ticari İleti Onayı',
];

$urlMap = [
    'kvkk'       => 'kvkk-aydinlatma',
    'privacy'    => 'gizlilik-politikasi',
    'cookie'     => 'cerez-politikasi',
    'consent'    => 'acik-riza',
    'commercial' => 'ticari-ileti',
];
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Hukuki Metinler</h2>
                <p style="font-size:0.85rem;color:var(--color-text-muted);margin:0;">
                    Sayfalar statik PHP olarak yönetilmektedir.
                    Bu panel sürüm ve etki tarihi bilgilerini tutar.
                </p>
            </div>

            <?php if (!$tableExists): ?>
            <div class="alert__message error">
                <p>
                    <code>legal_pages</code> tablosu bulunamadı.
                    <code>database.sql</code>'i çalıştırın.
                </p>
            </div>
            <?php else: ?>

            <?php foreach ($pageKeys as $key => $label): ?>
            <?php $page = $legalPages[$key] ?? null; ?>
            <div class="dashboard__panel" style="margin-bottom:var(--space-5);">
                <div class="dashboard__panel-header">
                    <h3><?= e($label) ?></h3>
                    <div style="display:flex;gap:var(--space-2);align-items:center;">
                        <?php if (isset($urlMap[$key])): ?>
                        <a href="<?= ROOT_URL . $urlMap[$key] ?>"
                           target="_blank"
                           class="btn sm outline">
                            <i class="uil uil-external-link-alt"></i> Görüntüle
                        </a>
                        <?php endif; ?>
                        <?php if ($page): ?>
                        <span style="font-size:0.78rem;color:var(--color-success);">
                            v<?= e($page['version']) ?> —
                            <?= $page['effective_date']
                                ? date('d.m.Y', strtotime($page['effective_date']))
                                : 'Tarih yok' ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="padding:var(--space-5);">
                    <form action="<?= ROOT_URL ?>admin/legal"
                          method="POST"
                          style="display:flex;gap:var(--space-4);align-items:flex-end;flex-wrap:wrap;">

                        <?= csrfField() ?>
                        <input type="hidden" name="page_key" value="<?= e($key) ?>">
                        <input type="hidden" name="title"
                               value="<?= e($page['title'] ?? $label) ?>">

                        <div class="form__control" style="margin-bottom:0;flex:1;min-width:120px;">
                            <label>Versiyon</label>
                            <input type="text" name="version"
                                   value="<?= e($page['version'] ?? '1.0') ?>"
                                   placeholder="1.0"
                                   style="max-width:100px;">
                        </div>

                        <div class="form__control" style="margin-bottom:0;flex:1;min-width:160px;">
                            <label>Yürürlük Tarihi</label>
                            <input type="date" name="effective_date"
                                   value="<?= e($page['effective_date'] ?? date('Y-m-d')) ?>">
                        </div>

                        <div class="form__control inline" style="margin-bottom:0;">
                            <input type="checkbox" name="is_active" value="1"
                                   id="active_<?= e($key) ?>"
                                   <?= (!$page || $page['is_active']) ? 'checked' : '' ?>>
                            <label for="active_<?= e($key) ?>">Aktif</label>
                        </div>

                        <button type="submit" class="btn sm">
                            <i class="uil uil-check"></i> Kaydet
                        </button>

                    </form>

                    <p style="margin-top:var(--space-4);font-size:0.8rem;color:var(--color-text-faint);">
                        İçerik düzenlemek için:
                        <code>templates/pages/legal/<?= e(str_replace(['-aydinlatma','-politikasi'], ['',''], $urlMap[$key] ?? $key)) ?>.php</code>
                        dosyasını düzenleyin.
                    </p>
                </div>
            </div>
            <?php endforeach; ?>

            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
