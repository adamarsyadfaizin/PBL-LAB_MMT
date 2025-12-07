<?php 
include 'components/header.php'; 

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM lab_profile ORDER BY id ASC LIMIT 1");
$data = $stmt->fetch();
?>

<div class="header-flex">
    <h1>Pengaturan Tampilan Website (CMS)</h1>
</div>

<form action="process_pengaturan.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <div class="table-card" style="margin-bottom: 30px;">
        <h3 style="border-bottom: 2px solid var(--primary); padding-bottom:10px; margin-bottom:20px;">1. Identitas & Footer (Global)</h3>
        
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="form-group">
                <label>Logo Website</label>
                <?php if(!empty($data['logo_path'])): ?>
                    <img src="../<?= $data['logo_path'] ?>" height="50" style="background:#eee; padding:5px; border-radius:4px; margin-bottom:5px;">
                <?php endif; ?>
                <input type="file" name="logo" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Alamat Lab</label>
                <textarea name="alamat_lab" class="form-control" rows="3"><?= htmlspecialchars($data['alamat_lab'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Deskripsi Footer (Tentang Lab)</label>
                <textarea name="footer_desc" class="form-control" rows="2" placeholder="Teks singkat di pojok kiri bawah footer..."><?= htmlspecialchars($data['footer_desc'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email_lab" class="form-control" value="<?= htmlspecialchars($data['email_lab'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="telepon_lab" class="form-control" value="<?= htmlspecialchars($data['telepon_lab'] ?? '') ?>">
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Teks Copyright</label>
                <input type="text" name="copyright_text" class="form-control" placeholder="Contoh: © 2025 Laboratorium Mobile. All Rights Reserved." value="<?= htmlspecialchars($data['copyright_text'] ?? '') ?>">
            </div>
        </div>

        <h4 style="margin-top:15px;">Sosial Media</h4>
        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:15px;">
            <input type="text" name="fb_link" class="form-control" placeholder="Facebook Link" value="<?= htmlspecialchars($data['fb_link'] ?? '') ?>">
            <input type="text" name="x_link" class="form-control" placeholder="Twitter/X Link" value="<?= htmlspecialchars($data['x_link'] ?? '') ?>">
            <input type="text" name="ig_link" class="form-control" placeholder="Instagram Link" value="<?= htmlspecialchars($data['ig_link'] ?? '') ?>">
            <input type="text" name="yt_link" class="form-control" placeholder="YouTube Link" value="<?= htmlspecialchars($data['yt_link'] ?? '') ?>">
            <input type="text" name="linkedin" class="form-control" placeholder="LinkedIn Link" value="<?= htmlspecialchars($data['linkedin'] ?? '') ?>">
        </div>
    </div>

    <div class="table-card" style="margin-bottom: 30px;">
        <h3 style="border-bottom: 2px solid var(--primary); padding-bottom:10px; margin-bottom:20px;">2. Hero Image & Judul Halaman</h3>
        
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px;">
            <div class="form-group" style="background:#f0f8ff; padding:15px; border-radius:8px; border:1px solid #ceeaff;">
                <label style="font-weight:bold; color:var(--primary);">1. Halaman BERANDA</label>
                <div style="margin-top:10px;">
                    <label>Gambar Background</label>
                    <?php if(!empty($data['hero_image_path'])): ?>
                        <img src="../<?= $data['hero_image_path'] ?>" height="60" style="display:block; margin:5px 0; border-radius:4px;">
                    <?php endif; ?>
                    <input type="file" name="hero_image" class="form-control">
                </div>
                <div style="margin-top:10px;">
                    <label>Judul Besar</label>
                    <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($data['hero_title'] ?? '') ?>">
                </div>
                <div style="margin-top:10px;">
                    <label>Deskripsi</label>
                    <textarea name="hero_description" class="form-control" rows="2"><?= htmlspecialchars($data['hero_description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-group" style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #eee;">
                <label style="font-weight:bold; color:#555;">2. Halaman PROFIL</label>
                <div style="margin-top:10px;">
                    <label>Gambar Background</label>
                    <?php if(!empty($data['about_hero_image'])): ?>
                        <img src="../<?= $data['about_hero_image'] ?>" height="60" style="display:block; margin:5px 0; border-radius:4px;">
                    <?php endif; ?>
                    <input type="file" name="about_hero" class="form-control">
                </div>
                <div style="margin-top:10px;">
                    <label>Judul Halaman</label>
                    <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($data['about_title'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group" style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #eee;">
                <label style="font-weight:bold; color:#555;">3. Halaman BERITA</label>
                <div style="margin-top:10px;">
                    <label>Gambar Background</label>
                    <?php if(!empty($data['news_hero_image'])): ?>
                        <img src="../<?= $data['news_hero_image'] ?>" height="60" style="display:block; margin:5px 0; border-radius:4px;">
                    <?php endif; ?>
                    <input type="file" name="news_hero" class="form-control">
                </div>
                <div style="margin-top:10px;">
                    <label>Judul Halaman</label>
                    <input type="text" name="news_title" class="form-control" value="<?= htmlspecialchars($data['news_title'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group" style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #eee;">
                <label style="font-weight:bold; color:#555;">4. Halaman PROYEK</label>
                <div style="margin-top:10px;">
                    <label>Gambar Background</label>
                    <?php if(!empty($data['project_hero_image'])): ?>
                        <img src="../<?= $data['project_hero_image'] ?>" height="60" style="display:block; margin:5px 0; border-radius:4px;">
                    <?php endif; ?>
                    <input type="file" name="project_hero" class="form-control">
                </div>
                <div style="margin-top:10px;">
                    <label>Judul Halaman</label>
                    <input type="text" name="project_title" class="form-control" value="<?= htmlspecialchars($data['project_title'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group" style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #eee;">
                <label style="font-weight:bold; color:#555;">5. Halaman GALERI</label>
                <div style="margin-top:10px;">
                    <label>Gambar Background</label>
                    <?php if(!empty($data['gallery_hero_image'])): ?>
                        <img src="../<?= $data['gallery_hero_image'] ?>" height="60" style="display:block; margin:5px 0; border-radius:4px;">
                    <?php endif; ?>
                    <input type="file" name="gallery_hero" class="form-control">
                </div>
                <div style="margin-top:10px;">
                    <label>Judul Halaman</label>
                    <input type="text" name="gallery_title" class="form-control" value="<?= htmlspecialchars($data['gallery_title'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group" style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #eee;">
                <label style="font-weight:bold; color:#555;">6. Halaman KONTAK</label>
                <div style="margin-top:10px;">
                    <label>Gambar Background</label>
                    <?php if(!empty($data['contact_hero_image'])): ?>
                        <img src="../<?= $data['contact_hero_image'] ?>" height="60" style="display:block; margin:5px 0; border-radius:4px;">
                    <?php endif; ?>
                    <input type="file" name="contact_hero" class="form-control">
                </div>
                <div style="margin-top:10px;">
                    <label>Judul Halaman</label>
                    <input type="text" name="contact_title" class="form-control" value="<?= htmlspecialchars($data['contact_title'] ?? '') ?>">
                </div>
            </div>

        </div>
    </div>

    <div style="text-align: center; margin-top: 40px; margin-bottom: 50px;">
        <button type="submit" name="save" class="btn btn-primary" style="padding: 12px 50px; font-size: 16px; font-weight: bold; border-radius: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i class="fas fa-save"></i> SIMPAN SEMUA PERUBAHAN
        </button>
    </div>
</form>

<div style="margin-bottom: 80px;"></div>

<?php include 'components/footer.php'; ?>