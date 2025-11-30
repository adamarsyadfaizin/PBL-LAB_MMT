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
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Join tabel projects dengan categories
            $sql = "SELECT p.*, c.name as category_name 
                    FROM projects p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    ORDER BY p.created_at DESC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['year']) ?></td>
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