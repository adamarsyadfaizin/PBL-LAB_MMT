<?php 
// admin/berita_form.php
include 'components/header.php'; 

// Cek apakah sedang mode Edit (ada ID di URL)
$id = $_GET['id'] ?? null;
$data = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}
?>

<div class="header-flex">
    <h1><?= $id ? 'Edit Berita' : 'Tambah Berita Baru' ?></h1>
    <a href="berita.php" class="btn btn-danger">Kembali</a>
</div>

<div class="table-card" style="max-width: 800px;">
    <form action="process_berita.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Judul Berita</label>
            <input type="text" name="title" class="form-control" required placeholder="Masukkan judul berita..." value="<?= htmlspecialchars($data['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="category" class="form-control" required>
                <option value="" disabled <?= empty($data['category']) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                <option value="berita" <?= ($data['category'] ?? '') == 'berita' ? 'selected' : '' ?>>Berita</option>
                <option value="kegiatan" <?= ($data['category'] ?? '') == 'kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                <option value="workshop" <?= ($data['category'] ?? '') == 'workshop' ? 'selected' : '' ?>>Workshop</option>
                <option value="lomba" <?= ($data['category'] ?? '') == 'lomba' ? 'selected' : '' ?>>Lomba / Prestasi</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ringkasan (Summary)</label>
            <textarea name="summary" class="form-control" rows="3" placeholder="Tulis ringkasan singkat untuk ditampilkan di kartu berita..."><?= htmlspecialchars($data['summary'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Konten Lengkap</label>
            <textarea name="content" class="form-control" rows="10" required placeholder="Tulis isi berita lengkap di sini..."><?= htmlspecialchars($data['content'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Gambar Sampul</label>
            <?php if (!empty($data['cover_image'])): ?>
                <div style="margin-bottom:10px;">
                    <p style="font-size: 12px; color: #666; margin-bottom: 5px;">Gambar Saat Ini:</p>
                    <img src="../<?= htmlspecialchars($data['cover_image']) ?>" class="preview" style="max-width: 200px; border-radius: 8px;">
                </div>
            <?php endif; ?>
            <input type="file" name="cover_image" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
            <small style="color: #888; font-size: 12px;">Format: JPG, PNG, WEBP. Biarkan kosong jika tidak ingin mengganti gambar.</small>
        </div>

        <div class="form-group">
            <label>Status Publikasi</label>
            <select name="status" class="form-control">
                <option value="published" <?= ($data['status'] ?? '') == 'published' || ($data['status'] ?? '') == '1' ? 'selected' : '' ?>>Published (Tampil)</option>
                <option value="draft" <?= ($data['status'] ?? '') == 'draft' || ($data['status'] ?? '') == '0' ? 'selected' : '' ?>>Draft (Sembunyikan)</option>
            </select>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<?php include 'components/footer.php'; ?>