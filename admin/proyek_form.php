<?php 
include 'components/header.php'; 
$id = $_GET['id'] ?? null;
$data = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}
// Ambil list kategori
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="header-flex">
    <h1><?= $id ? 'Edit Proyek' : 'Tambah Proyek' ?></h1>
    <a href="proyek.php" class="btn btn-danger">Kembali</a>
</div>

<div class="table-card" style="max-width: 800px;">
    <form action="process_proyek.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Judul Proyek</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($data['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="category_id" class="form-control">
                <option value="">Pilih Kategori</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($data['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Tahun</label>
            <input type="number" name="year" class="form-control" value="<?= $data['year'] ?? date('Y') ?>">
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Link Demo (URL)</label>
            <input type="url" name="demo_url" class="form-control" value="<?= htmlspecialchars($data['demo_url'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Gambar Proyek</label>
            <?php if (!empty($data['cover_image'])): ?>
                <br><img src="../../<?= htmlspecialchars($data['cover_image']) ?>" class="preview">
            <?php endif; ?>
            <input type="file" name="cover_image" class="form-control">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="published" <?= ($data['status'] ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($data['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
            </select>
        </div>

        <button type="submit" name="save" class="btn btn-primary">Simpan Proyek</button>
    </form>
</div>
<?php include 'components/footer.php'; ?>