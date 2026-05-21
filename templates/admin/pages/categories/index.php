<?php
/**
 * templates/admin/pages/categories/index.php
 * URL: /admin/categories
 */

$pageTitle  = 'Kategoriler';
$activeMenu = 'categories';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$categories = mysqli_query($connection, "
    SELECT c.id, c.title, c.slug, c.description, c.is_noindex,
           COUNT(p.id) AS post_count
    FROM post_categories c
    LEFT JOIN posts p ON p.category_id = c.id AND p.is_published = 1
    GROUP BY c.id
    ORDER BY c.title ASC
");
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Kategoriler</h2>
                <a href="<?= ROOT_URL ?>admin/categories/create" class="btn">
                    <i class="uil uil-folder-plus"></i> Yeni Kategori
                </a>
            </div>

            <?php if ($categories && mysqli_num_rows($categories) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>Slug</th>
                            <th>Yazı Sayısı</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <tr>
                            <td><?= e($cat['title']) ?></td>
                            <td>
                                <code class="code-muted">
                                    <?= e($cat['slug']) ?>
                                </code>
                            </td>
                            <td><?= (int)$cat['post_count'] ?></td>
                            <td>
                                <?php if ($cat['is_noindex']): ?>
                                <span class="status status--muted">noindex</span>
                                <?php else: ?>
                                <span class="status status--success">index</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table__actions">
                                    <a href="<?= ROOT_URL ?>kategori/<?= urlencode($cat['slug']) ?>"
                                       target="_blank" class="btn sm outline">
                                        <i class="uil uil-external-link-alt"></i>
                                    </a>
                                    <a href="<?= ROOT_URL ?>admin/categories/edit?id=<?= (int)$cat['id'] ?>"
                                       class="btn sm">Düzenle</a>
                                    <form method="POST"
                                          action="<?= ROOT_URL ?>admin/categories/delete"
                                          style="display:inline;"
                                          onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz? İçindeki yazılar kategorisiz kalır.')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
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
            <div class="alert__message error"><p>Henüz kategori bulunamadı.</p></div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
