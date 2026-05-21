<?php
/**
 * templates/admin/pages/testimonials/index.php
 * URL: /admin/testimonials
 */

$pageTitle  = 'Yorumlar';
$activeMenu = 'testimonials';

require_once BASE_PATH . '/templates/admin/partials/header.php';

$testimonials = mysqli_query($connection, "
    SELECT id, author_name, author_title, content, rating,
           service_type, is_featured, is_active, is_google, display_order
    FROM testimonials
    ORDER BY display_order ASC, id DESC
");
?>

<section class="dashboard">
    <?php flashRender(); ?>
    <div class="container dashboard__container">
        <?php require BASE_PATH . '/templates/admin/partials/sidebar.php'; ?>
        <main>
            <div class="dashboard__page-header">
                <h2>Danışan Yorumları</h2>
                <a href="<?= ROOT_URL ?>admin/testimonials/create" class="btn">
                    <i class="uil uil-comment-plus"></i> Yorum Ekle
                </a>
            </div>

            <?php if ($testimonials && mysqli_num_rows($testimonials) > 0): ?>
            <div class="table__wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Sıra</th>
                            <th>Ad</th>
                            <th>Yorum</th>
                            <th>Puan</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($t = mysqli_fetch_assoc($testimonials)): ?>
                        <tr>
                            <td><?= (int)$t['display_order'] ?></td>
                            <td>
                                <?= e($t['author_name']) ?>
                                <?php if ($t['is_google']): ?>
                                <span class="badge badge--primary">Google</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width:300px;">
                                <p style="font-size:0.82rem;color:var(--color-text-muted);line-height:1.5;">
                                    <?= e(mb_substr($t['content'], 0, 100, 'UTF-8')) ?><?= mb_strlen($t['content'], 'UTF-8') > 100 ? '...' : '' ?>
                                </p>
                            </td>
                            <td>
                                <span style="color:var(--color-warning);">
                                    <?= str_repeat('★', (int)$t['rating']) ?><?= str_repeat('☆', 5 - (int)$t['rating']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($t['is_active']): ?>
                                <span class="status status--success">● Aktif</span>
                                <?php else: ?>
                                <span class="status status--muted">● Gizli</span>
                                <?php endif; ?>
                                <?php if ($t['is_featured']): ?>
                                <span class="badge badge--accent">Öne Çıkan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table__actions">
                                    <a href="<?= ROOT_URL ?>admin/testimonials/edit?id=<?= (int)$t['id'] ?>"
                                       class="btn sm">Düzenle</a>
                                    <form method="POST"
                                          action="<?= ROOT_URL ?>admin/testimonials/delete"
                                          style="display:inline;"
                                          onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
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
            <div class="alert__message info">
                <p>Henüz yorum eklenmemiş. <a href="<?= ROOT_URL ?>admin/testimonials/create">İlk yorumu ekleyin →</a></p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require BASE_PATH . '/templates/admin/partials/footer.php'; ?>
