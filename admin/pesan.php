<?php include 'components/header.php'; ?>

<div class="header-flex">
    <h1>Pesan Masuk (Feedback)</h1>
</div>

<?php
// Handle Mark as Read
if(isset($_GET['mark_read'])) {
    $pdo->prepare("UPDATE feedback SET is_read = true WHERE id = ?")->execute([$_GET['mark_read']]);
    echo "<script>window.location='pesan.php';</script>";
}

// Handle Delete
if(isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM feedback WHERE id = ?")->execute([$_GET['delete']]);
    echo "<script>window.location='pesan.php';</script>";
}
?>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Pengirim</th>
                <th>Email</th>
                <th>Subjek</th>
                <th>Pesan</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
            while ($row = $stmt->fetch()):
                $bg = $row['is_read'] ? '' : '#f0fdf4';
                $weight = $row['is_read'] ? 'normal' : 'bold';
            ?>
            <tr style="background-color: <?= $bg ?>; font-weight: <?= $weight ?>">
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['subjek']) ?></td>
                <td><?= htmlspecialchars(substr($row['pesan'], 0, 50)) ?>...</td>
                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <?php if(!$row['is_read']): ?>
                        <a href="?mark_read=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Tandai sudah dibaca"><i class="fas fa-check"></i></a>
                    <?php endif; ?>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pesan ini?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'components/footer.php'; ?>