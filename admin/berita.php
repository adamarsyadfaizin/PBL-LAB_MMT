<?php include 'components/header.php'; ?>

<div class="header-flex">
    <h1>Manajemen Berita</h1>
    <a href="berita_form.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Berita</a>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th width="50">No</th>
                <th width="100">Gambar</th> <th>Judul</th>
                <th>Statistik</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $pdo->exec("REFRESH MATERIALIZED VIEW CONCURRENTLY mv_news_with_stats");
                $stmt = $pdo->query("SELECT * FROM mv_news_with_stats ORDER BY created_at DESC");
            } catch (Exception $e) {
                // Fallback ke query biasa
                $stmt = $pdo->query("SELECT n.*, u.name as author_name FROM news n LEFT JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC");
            }

            $no = 1;
            while ($row = $stmt->fetch()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?php if (!empty($row['cover_image'])): ?>
                        <img src="../<?= htmlspecialchars($row['cover_image']) ?>" width="60" height="40" style="object-fit: cover; border-radius: 4px;">
                    <?php else: ?>
                        <span style="color: #ccc; font-size: 12px;">No Image</span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                    <small class="text-muted">/<?= htmlspecialchars($row['slug']) ?></small>
                    <?php if (!empty($row['author_name'])): ?>
                        <br><small class="text-muted">Oleh: <?= htmlspecialchars($row['author_name']) ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span class="badge badge-info" style="font-size: 11px;">
                            <i class="fas fa-comment"></i> <?= number_format($row['total_comments'] ?? 0) ?>
                        </span>
                        <?php if (!empty($row['avg_rating']) && $row['avg_rating'] > 0): ?>
                            <span class="badge badge-warning" style="font-size: 11px;">
                                <i class="fas fa-star"></i> <?= number_format($row['avg_rating'], 1) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($row['total_tags']) && $row['total_tags'] > 0): ?>
                            <span class="badge badge-secondary" style="font-size: 11px;">
                                <i class="fas fa-tag"></i> <?= $row['total_tags'] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <?php if($row['status'] == 'published' || $row['status'] == '1'): ?>
                        <span class="badge badge-success">Published</span>
                    <?php else: ?>
                        <span class="badge badge-draft">Draft</span>
                    <?php endif; ?>
                </td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="berita_form.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                    <a href="process_berita.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus berita ini?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'components/footer.php'; ?>