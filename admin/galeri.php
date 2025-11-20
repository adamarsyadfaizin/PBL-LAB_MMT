<?php include 'components/header.php'; ?>

<div class="header-flex">
    <h1>Manajemen Galeri (Media Assets)</h1>
</div>

<div class="table-card" style="margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 8px;">
    <h3 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Upload Media Baru</h3>
    <form action="process_galeri.php" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <div class="form-group">
            <label>Caption / Judul</label>
            <input type="text" name="caption" class="form-control" required placeholder="Contoh: Dokumentasi Workshop AI">
        </div>

        <div class="form-group">
            <label>Jenis Media</label>
            <select name="type" class="form-control">
                <option value="foto">Foto</option>
                <option value="video">Video</option>
                <option value="animasi">Animasi</option>
            </select>
        </div>

        <div class="form-group" style="grid-column: span 2;">
            <label>Deskripsi (Opsional)</label>
            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi singkat..."></textarea>
        </div>

        <div class="form-group" style="grid-column: span 2;">
            <label>File Gambar/Video</label>
            <input type="file" name="media_file" class="form-control" required>
            <small>Format: JPG, PNG, MP4 (Max 5MB)</small>
        </div>

        <div style="grid-column: span 2;">
            <button type="submit" name="upload" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Media</button>
        </div>
    </form>
</div>

<div class="table-card">
    <h3>Daftar Media</h3>
    <table>
        <thead>
            <tr>
                <th width="50">No</th>
                <th width="120">Preview</th>
                <th>Caption</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $stmt = $pdo->query("SELECT * FROM media_assets ORDER BY created_at DESC");
            while ($row = $stmt->fetch()):
                // Tentukan apakah url gambar lokal atau link luar
                $is_local = !filter_var($row['url'], FILTER_VALIDATE_URL);
                $img_src = $is_local ? "../" . $row['url'] : $row['url'];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?php if ($row['type'] == 'video'): ?>
                        <div style="background: #eee; padding: 10px; text-align: center; border-radius: 4px;">
                            <i class="fas fa-video"></i> Video
                        </div>
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($img_src) ?>" width="100" style="border-radius: 4px; object-fit: cover;">
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= htmlspecialchars($row['caption']) ?></strong><br>
                    <small style="color: #666;"><?= htmlspecialchars(substr($row['deskripsi'] ?? '', 0, 50)) ?></small>
                </td>
                <td><span class="badge badge-success"><?= htmlspecialchars($row['type']) ?></span></td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="process_galeri.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus media ini?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'components/footer.php'; ?>