<?php
/**
 * templates/admin/pages/posts/index.php
 * URL: /admin/posts
 */

$pageTitle  = 'Yazılar';
$activeMenu = 'posts';

require_once BASE_PATH . '/templates/admin/partials/header.php';
require_once BASE_PATH . '/app/helpers/flash.php';

$posts = mysqli_query($connection, "
    SELECT p.id, p.title, p.slug, p.views, p.is_published, p.is_featured, p.published_at,
           c.title AS category_title
    FROM posts p
    LEFT JOIN post_categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Yazılar</h2>
                <a href="<?= ROOT_URL ?>admin/posts/create" class="btn">
                    <i class="uil uil-pen"></i> Yeni Yazı
                </a>
            </div>

            <?php if ($posts && mysqli_num_rows($posts) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>Kategori</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                            <th>Görüntülenme</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                        <tr>
                            <td>
                                <?= e($post['title']) ?>
                                <?php if ($post['is_featured']): ?>
                                <span class="badge badge--accent" style="margin-left:4px;">Öne Çıkan</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($post['category_title'] ?? 'Kategorisiz') ?></td>
                            <td>
                                <?php if ($post['is_published']): ?>
                                <span class="status status--success">● Yayında</span>
                                <?php else: ?>
                                <span class="status status--muted">● Taslak</span>
                                <?php endif; ?>
                            </td>
                            <td><?= turkceTarih($post['published_at'], 'd F Y') ?></td>
                            <td><?= number_format((int)$post['views']) ?></td>
                            <td>
                                <div class="table__actions">
                                    <a href="<?= ROOT_URL ?>blog/<?= urlencode($post['slug']) ?>"
                                       target="_blank"
                                       class="btn sm outline"
                                       title="Siteye git">
                                        <i class="uil uil-external-link-alt"></i>
                                    </a>
                                    <a href="<?= ROOT_URL ?>admin/posts/edit?id=<?= (int)$post['id'] ?>"
                                       class="btn sm">Düzenle</a>
                                    <form method="POST"
                                          action="<?= ROOT_URL ?>admin/posts/delete"
                                          style="display:inline;"
                                          onsubmit="return confirm('Bu yazıyı silmek istediğinize emin misiniz?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
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
            <div class="alert__message error"><p>Henüz yazı bulunamadı.</p></div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
