<?php include 'components/header.php'; ?>

<div class="header-flex">
    <h1>Manajemen Galeri (Media Assets)</h1>
</div>

<div class="table-card" style="margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 8px;">
    <h3 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Upload Media Baru</h3>
    <form action="process_galeri.php" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <div class="form-group">
            <label>Judul / Caption</label>
            <input type="text" name="caption" class="form-control" required placeholder="Contoh: Suasana Lomba Game Dev">
        </div>

        <div class="form-group">
            <label>Nama Acara / Event</label>
            <input type="text" name="event_name" class="form-control" placeholder="Contoh: Dies Natalis 2025">
            <small style="color:#888;">Kosongkan jika bukan bagian dari acara khusus.</small>
        </div>

        <div class="form-group">
            <label>Jenis Media</label>
            <select name="type" class="form-control">
                <option value="foto">Foto</option>
                <option value="video">Video</option>
                <option value="animasi">Animasi</option>
            </select>
        </div>

        <div class="form-group">
            <label>File Gambar/Video</label>
            <input type="file" name="media_file" class="form-control" required>
            <small>Format: JPG, PNG, MP4 (Max 10MB)</small>
        </div>

        <div class="form-group" style="grid-column: span 2;">
            <label>Deskripsi (Opsional)</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi detail media..."></textarea>
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
                <th>Info Media</th>
                <th>Acara</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $stmt = $pdo->query("SELECT * FROM media_assets ORDER BY created_at DESC");
            while ($row = $stmt->fetch()):
                // Cek apakah link luar atau file lokal
                $is_link = filter_var($row['url'], FILTER_VALIDATE_URL);
                // Jika lokal, tambah ../ agar admin bisa preview
                $img_src = $is_link ? $row['url'] : "../" . str_replace('../', '', $row['url']);
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?php if ($row['type'] == 'video'): ?>
                        <div style="background: #eee; padding: 10px; text-align: center; border-radius: 4px; font-size: 20px;">
                            <i class="fas fa-video"></i>
                        </div>
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($img_src) ?>" width="100" height="70" style="border-radius: 4px; object-fit: cover;">
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= htmlspecialchars($row['caption']) ?></strong><br>
                    <span class="badge badge-success"><?= htmlspecialchars($row['type']) ?></span>
                </td>
                <td>
                    <?= htmlspecialchars($row['event_name'] ?? '-') ?>
                </td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="process_galeri.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus media ini? File fisik juga akan dihapus.')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'components/footer.php'; ?>