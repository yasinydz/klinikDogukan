<?php
/**
 * templates/admin/pages/gallery/index.php
 * URL: /admin/gallery
 */

$pageTitle  = 'Galeri';
$activeMenu = 'gallery';

require_once BASE_PATH . '/templates/admin/partials/header.php';

$images = mysqli_query($connection, "
    SELECT id, title, alt_text, filename, category, display_order, is_featured, is_active
    FROM gallery
    ORDER BY display_order ASC, id DESC
");
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Galeri</h2>
                <a href="<?= ROOT_URL ?>admin/gallery/create" class="btn">
                    <i class="uil uil-image-plus"></i> Görsel Ekle
                </a>
            </div>

            <?php if ($images && mysqli_num_rows($images) > 0): ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:var(--space-4);">
                <?php while ($img = mysqli_fetch_assoc($images)): ?>
                <div style="background:var(--color-bg-2);border:1.5px solid var(--color-border);border-radius:var(--radius-lg);overflow:hidden;position:relative;">
                    <img src="<?= ROOT_URL ?>images/uploads/<?= e($img['filename']) ?>"
                         alt="<?= e($img['alt_text'] ?: $img['title']) ?>"
                         style="width:100%;height:150px;object-fit:cover;"
                         loading="lazy">
                    <div style="padding:var(--space-3) var(--space-4);">
                        <p style="font-size:0.82rem;font-weight:600;margin-bottom:var(--space-1);">
                            <?= e($img['title'] ?: 'Adsız') ?>
                        </p>
                        <div style="display:flex;gap:var(--space-1);flex-wrap:wrap;margin-bottom:var(--space-2);">
                            <span class="badge badge--muted"><?= e($img['category']) ?></span>
                            <?php if ($img['is_featured']): ?>
                            <span class="badge badge--accent">Öne Çıkan</span>
                            <?php endif; ?>
                            <?php if (!$img['is_active']): ?>
                            <span class="badge badge--muted">Gizli</span>
                            <?php endif; ?>
                        </div>
                        <form method="POST"
                              action="<?= ROOT_URL ?>admin/gallery/delete"
                              onsubmit="return confirm('Bu görseli silmek istediğinize emin misiniz?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                            <div style="display:flex;gap:var(--space-2);">
                                <a href="<?= ROOT_URL ?>admin/gallery/edit?id=<?= (int)$img['id'] ?>"
                                   class="btn sm" style="flex:1;justify-content:center;">Düzenle</a>
                                <button type="submit" class="btn sm danger" style="flex:1;">Sil</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="alert__message info">
                <p>Henüz galeri görseli yok. <a href="<?= ROOT_URL ?>admin/gallery/create">İlk görseli yükleyin →</a></p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
