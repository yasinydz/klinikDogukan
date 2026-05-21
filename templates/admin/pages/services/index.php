<?php
/**
 * templates/admin/pages/services/index.php
 * URL: /admin/services
 */

$pageTitle  = 'Hizmetler';
$activeMenu = 'services';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$services = mysqli_query($connection, "
    SELECT id, slug, title, icon_class, display_order, is_active
    FROM services ORDER BY display_order ASC
");
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Hizmetler</h2>
                <a href="<?= ROOT_URL ?>admin/services/create" class="btn">
                    <i class="uil uil-plus"></i> Yeni Hizmet
                </a>
            </div>

            <?php if ($services && mysqli_num_rows($services) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Sıra</th>
                            <th>Başlık</th>
                            <th>Slug</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($s = mysqli_fetch_assoc($services)): ?>
                        <tr>
                            <td><?= (int)$s['display_order'] ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <i class="<?= e($s['icon_class'] ?? 'uil uil-heart') ?>"
                                       style="color:var(--color-accent);" aria-hidden="true"></i>
                                    <?= e($s['title']) ?>
                                </div>
                            </td>
                            <td>
                                <code class="code-muted">
                                    <?= e($s['slug']) ?>
                                </code>
                            </td>
                            <td>
                                <?php if ($s['is_active']): ?>
                                <span class="status status--success">● Yayında</span>
                                <?php else: ?>
                                <span class="status status--muted">● Gizli</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table__actions">
                                    <a href="<?= ROOT_URL ?>hizmetler/<?= urlencode($s['slug']) ?>"
                                       target="_blank" class="btn sm outline">
                                        <i class="uil uil-external-link-alt"></i>
                                    </a>
                                    <a href="<?= ROOT_URL ?>admin/services/edit?id=<?= (int)$s['id'] ?>"
                                       class="btn sm">Düzenle</a>
                                    <form method="POST"
                                          action="<?= ROOT_URL ?>admin/services/delete"
                                          style="display:inline;"
                                          onsubmit="return confirm('Bu hizmeti silmek istediğinize emin misiniz?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                                        <button type="submit" class="btn sm danger">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert__message error">
                <p>Henüz hizmet bulunamadı. <a href="<?= ROOT_URL ?>admin/services/create">Hizmet ekle →</a></p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
