<?php 
// admin/galeri_form.php
include 'components/header.php'; 
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
$data = null;

// Jika ada ID, berarti mode EDIT -> Ambil data lama
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM media_assets WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}
?>

<div class="header-flex">
    <h1><?= $id ? 'Edit Media' : 'Upload Media Baru' ?></h1>
    <a href="galeri.php" class="btn btn-danger">Kembali</a>
</div>

<div class="table-card" style="max-width: 800px;">
    <form action="process_galeri.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Judul / Caption</label>
            <input type="text" name="caption" class="form-control" required placeholder="Contoh: Dokumentasi Lomba" value="<?= htmlspecialchars($data['caption'] ?? '') ?>">
        </div>

        <div class="row" style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label>Tanggal Kegiatan</label>
                <input type="date" name="tanggal" class="form-control" required value="<?= isset($data['created_at']) ? date('Y-m-d', strtotime($data['created_at'])) : date('Y-m-d') ?>">
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>Jenis Media</label>
                <select name="type" class="form-control">
                    <option value="foto" <?= ($data['type'] ?? '') == 'foto' ? 'selected' : '' ?>>Foto</option>
                    <option value="video" <?= ($data['type'] ?? '') == 'video' ? 'selected' : '' ?>>Video</option>
                    <option value="animasi" <?= ($data['type'] ?? '') == 'animasi' ? 'selected' : '' ?>>Animasi</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Nama Acara / Event <small>(Opsional)</small></label>
            <input type="text" name="event_name" class="form-control" placeholder="Contoh: Dies Natalis" value="<?= htmlspecialchars($data['event_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Deskripsi <small>(Opsional)</small></label>
            <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>File Media</label>
            <?php if (!empty($data['url'])): ?>
                <div style="margin-bottom:10px; background: #f8f9fa; padding: 10px; border-radius: 5px; display: inline-block;">
                    <p style="font-size: 12px; color: #666; margin-bottom: 5px;">File Saat Ini:</p>
                    <?php 
                        $preview_src = filter_var($data['url'], FILTER_VALIDATE_URL) ? $data['url'] : "../" . str_replace('../', '', $data['url']);
                        if($data['type'] == 'video') {
                            echo '<video width="200" controls src="'.$preview_src.'"></video>';
                        } else {
                            echo '<img src="'.$preview_src.'" style="max-width: 200px; border-radius: 5px;">';
                        }
                    ?>
                </div>
            <?php endif; ?>
            
            <input type="file" name="media_file" class="form-control">
            <small style="color: #888;">Biarkan kosong jika tidak ingin mengganti file.</small>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<?php include 'components/footer.php'; ?>