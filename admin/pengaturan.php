<?php 
include 'components/header.php'; 

// Ambil data profil (ID 1 atau sesuaikan dengan ID di database kamu)
$stmt = $pdo->query("SELECT * FROM lab_profile ORDER BY id ASC LIMIT 1");
$data = $stmt->fetch();
?>

<div class="header-flex">
    <h1>Pengaturan Tampilan Website (CMS)</h1>
</div>

<form action="process_pengaturan.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <div class="stats-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 20px;">
        
        <div class="table-card">
            <h3>1. Header & Hero Section</h3>
            
            <div class="form-group">
                <label>Logo Website</label>
                <?php if(!empty($data['logo_path'])): ?>
                    <br><img src="../<?= $data['logo_path'] ?>" height="50" style="background:#ccc; padding:5px; border-radius:4px;">
                <?php endif; ?>
                <input type="file" name="logo" class="form-control" style="margin-top:10px;">
                <small>Format: PNG (Transparan) disarankan.</small>
            </div>

            <div class="form-group">
                <label>Gambar Background Hero (Beranda)</label>
                <?php if(!empty($data['hero_image_path'])): ?>
                    <br><img src="../<?= $data['hero_image_path'] ?>" height="80" style="border-radius:4px;">
                <?php endif; ?>
                <input type="file" name="hero_image" class="form-control" style="margin-top:10px;">
            </div>

            <div class="form-group">
                <label>Judul Besar (Hero Title)</label>
                <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($data['hero_title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Deskripsi Singkat (Hero Description)</label>
                <textarea name="hero_description" class="form-control" rows="3"><?= htmlspecialchars($data['hero_description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="table-card">
            <h3>2. Footer & Kontak</h3>
            
            <div class="form-group">
                <label>Alamat Lab</label>
                <textarea name="alamat_lab" class="form-control" rows="3"><?= htmlspecialchars($data['alamat_lab'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email_lab" class="form-control" value="<?= htmlspecialchars($data['email_lab'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="telepon_lab" class="form-control" value="<?= htmlspecialchars($data['telepon_lab'] ?? '') ?>">
            </div>

            <h4 style="margin-top:20px;">Sosial Media</h4>
            <div class="form-group">
                <label>Link Facebook</label>
                <input type="text" name="fb_link" class="form-control" value="<?= htmlspecialchars($data['fb_link'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Link Instagram</label>
                <input type="text" name="ig_link" class="form-control" value="<?= htmlspecialchars($data['ig_link'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Link LinkedIn</label>
                <input type="text" name="linkedin" class="form-control" value="<?= htmlspecialchars($data['linkedin'] ?? '') ?>">
            </div>
        </div>

    </div>

    <button type="submit" name="save" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px;">
        <i class="fas fa-save"></i> SIMPAN PERUBAHAN
    </button>
</form>

<div style="margin-bottom: 50px;"></div>

<?php include 'components/footer.php'; ?>