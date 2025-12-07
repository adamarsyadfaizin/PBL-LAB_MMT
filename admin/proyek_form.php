<?php 
// admin/proyek_form.php
include 'components/header.php'; 
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
$data = null;

// Ambil data proyek jika mode Edit
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}

// Ambil daftar Kategori untuk Dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="header-flex">
    <h1><?= $id ? 'Edit Proyek' : 'Tambah Proyek Baru' ?></h1>
    <a href="proyek.php" class="btn btn-danger">Kembali</a>
</div>

<div class="table-card" style="max-width: 800px;">
    <form action="process_proyek.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Judul Proyek</label>
            <input type="text" name="title" class="form-control" required placeholder="Nama aplikasi atau proyek..." value="<?= htmlspecialchars($data['title'] ?? '') ?>">
        </div>

        <div class="row" style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label>Tahun Pembuatan</label>
                <input type="number" name="year" class="form-control" required placeholder="Contoh: 2024" value="<?= htmlspecialchars($data['year'] ?? date('Y')) ?>">
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>Kategori</label>
                <select name="category_id" class="form-control" required>
                    <option value="" disabled <?= empty($data['category_id']) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($data['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Ringkasan (Summary)</label>
            <textarea name="summary" class="form-control" rows="3" placeholder="Deskripsi singkat untuk kartu depan..."><?= htmlspecialchars($data['summary'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Deskripsi Lengkap</label>
            <textarea name="description" class="form-control" rows="10" placeholder="Jelaskan fitur, teknologi, dan detail proyek..."><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
        </div>

        <div class="row" style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label>Repository URL <small style="color: #888; font-weight: normal;">(Opsional)</small></label>
                <input type="url" name="repo_url" class="form-control" placeholder="https://github.com/..." value="<?= htmlspecialchars($data['repo_url'] ?? '') ?>">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Demo URL <small style="color: #888; font-weight: normal;">(Opsional)</small></label>
                <input type="url" name="demo_url" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($data['demo_url'] ?? '') ?>">
            </div>
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
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="published" <?= ($data['status'] ?? '') == '1' || ($data['status'] ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($data['status'] ?? '') == '0' || ($data['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
            </select>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Proyek
            </button>
        </div>
    </form>
</div>

<?php include 'components/footer.php'; ?>