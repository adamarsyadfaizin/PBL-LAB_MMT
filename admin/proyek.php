<?php include 'components/header.php'; ?>

<div class="header-flex">
    <h1>Manajemen Proyek</h1>
    <a href="proyek_form.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Proyek</a>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Tahun</th>
                <th>Statistik</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Gunakan materialized view untuk statistik proyek
            try {
                $pdo->exec("REFRESH MATERIALIZED VIEW CONCURRENTLY mv_project_details");
                $stmt = $pdo->query("SELECT * FROM mv_project_details ORDER BY created_at DESC");
            } catch (Exception $e) {
                // Fallback ke query biasa
                $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                        FROM projects p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        ORDER BY p.created_at DESC";
                $stmt = $pdo->query($sql);
            }
            
            while ($row = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                    <small class="text-muted">/<?= htmlspecialchars($row['slug']) ?></small>
                </td>
                <td>
                    <?php if (!empty($row['category_name'])): ?>
                        <span class="badge badge-info"><?= htmlspecialchars($row['category_name']) ?></span>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['year']) ?></td>
                <td>
                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <span class="badge badge-primary" style="font-size: 11px;">
                            <i class="fas fa-users"></i> <?= number_format($row['total_members'] ?? 0) ?>
                        </span>
                        <span class="badge badge-info" style="font-size: 11px;">
                            <i class="fas fa-comment"></i> <?= number_format($row['total_comments'] ?? 0) ?>
                        </span>
                        <?php if (!empty($row['avg_rating']) && $row['avg_rating'] > 0): ?>
                            <span class="badge badge-warning" style="font-size: 11px;">
                                <i class="fas fa-star"></i> <?= number_format($row['avg_rating'], 1) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($row['rating'])): ?>
                            <span class="badge badge-success" style="font-size: 11px;">
                                <i class="fas fa-trophy"></i> <?= $row['rating'] ?>
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
                <td>
                    <a href="proyek_form.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                    <a href="process_proyek.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus proyek?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'components/footer.php'; ?>