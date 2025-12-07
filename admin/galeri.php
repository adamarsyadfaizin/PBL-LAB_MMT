<?php 
// admin/galeri.php
include 'components/header.php'; 
require_once '../config/db.php';
?>

<div class="header-flex">
    <h1>Manajemen Galeri</h1>
    <a href="galeri_form.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Media</a>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th width="50">No</th>
                <th width="120">Preview</th>
                <th>Info Media</th>
                <th>Event & Tanggal</th>
                <th width="120">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $stmt = $pdo->query("SELECT * FROM media_assets ORDER BY created_at DESC");
            while ($row = $stmt->fetch()):
                $is_link = filter_var($row['url'], FILTER_VALIDATE_URL);
                $img_src = $is_link ? $row['url'] : "../" . str_replace('../', '', $row['url']);
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?php if ($row['type'] == 'video'): ?>
                        <video width="120" height="70" controls preload="metadata" style="border-radius: 4px; object-fit: cover; background: #000;">
                            <source src="<?= htmlspecialchars($img_src) ?>#t=0.5" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($img_src) ?>" width="100" height="70" style="border-radius: 4px; object-fit: cover;">
                    <?php endif; ?> 
                </td>
                <td>
                    <strong><?= htmlspecialchars($row['caption']) ?></strong><br>
                    <span class="badge badge-secondary"><?= ucfirst($row['type']) ?></span>
                </td>
                <td>
                    <?= htmlspecialchars($row['event_name'] ?? '-') ?><br>
                    <small style="color:#666;"><?= date('d M Y', strtotime($row['created_at'])) ?></small>
                </td>
                <td>
                    <a href="galeri_form.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                    <a href="process_galeri.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus media ini?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'components/footer.php'; ?>