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
                <th>Status</th>
                <th>Tanggal</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
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