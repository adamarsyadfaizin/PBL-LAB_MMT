<?php 
include 'components/header.php'; 
require_once '../config/db.php';

$profile = $pdo->query("SELECT * FROM lab_profile LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$members = $pdo->query("SELECT * FROM members ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$activeTab = $_GET['tab'] ?? 'profil'; 
?>

<div class="header-flex">
    <h1>Manajemen Profil Laboratorium</h1>
</div>

<div style="margin-bottom: 20px; border-bottom: 2px solid #ddd;">
    <button onclick="switchTab('profil')" id="btn-profil" class="btn-tab <?= $activeTab=='profil'?'active':'' ?>">
        <i class="fas fa-university"></i> Visi, Misi & Sejarah
    </button>
    <button onclick="switchTab('tim')" id="btn-tim" class="btn-tab <?= $activeTab=='tim'?'active':'' ?>">
        <i class="fas fa-users"></i> Anggota Tim (Struktur)
    </button>
</div>

<div id="tab-profil" style="display: <?= $activeTab=='profil'?'block':'none' ?>;">
    <div class="table-card">
        <form action="process_profil_lab.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Visi Laboratorium</label>
                <textarea name="visi" class="form-control" rows="3"><?= htmlspecialchars($profile['visi'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Misi Laboratorium</label>
                <small style="color:#888;">Gunakan enter untuk poin baru</small>
                <textarea name="misi" class="form-control" rows="5"><?= htmlspecialchars($profile['misi'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Sejarah Singkat</label>
                <textarea name="sejarah" class="form-control" rows="5"><?= htmlspecialchars($profile['sejarah'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Gambar Bagan Struktur Organisasi</label>
                <?php if(!empty($profile['struktur_org_path'])): ?>
                    <br><img src="../<?= $profile['struktur_org_path'] ?>" style="max-width: 200px; border:1px solid #ddd; padding:5px; border-radius:5px; margin:5px 0;">
                <?php endif; ?>
                <input type="file" name="struktur_org" class="form-control">
            </div>
            <div style="margin-top: 20px;">
                <button type="submit" name="update_profile" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Profil</button>
            </div>
        </form>
    </div>
</div>

<div id="tab-tim" style="display: <?= $activeTab=='tim'?'block':'none' ?>;">
    <div style="margin-bottom: 15px; text-align: right;">
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Tambah Anggota
        </button>
    </div>
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th width="80">Foto</th>
                    <th>Nama & Jabatan</th>
                    <th>Akses Sosmed</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($members as $m): 
                    $img = !empty($m['avatar_url']) ? "../".$m['avatar_url'] : "../assets/images/placeholder-team.jpg";
                ?>
                <tr>
                    <td><img src="<?= $img ?>" style="width:50px; height:50px; border-radius:50%; object-fit:cover;"></td>
                    <td>
                        <strong><?= htmlspecialchars($m['name']) ?></strong><br>
                        <span class="badge badge-secondary"><?= htmlspecialchars($m['role']) ?></span>
                    </td>
                    <td style="font-size: 16px;">
                        <i class="fab fa-linkedin" style="color: <?= !empty($m['linkedin_url']) ? '#0077b5' : '#ccc' ?>;"></i>
                        <i class="fas fa-graduation-cap" style="color: <?= !empty($m['scholar_url']) ? '#4285f4' : '#ccc' ?>;"></i>
                        <i class="fab fa-instagram" style="color: <?= !empty($m['instagram']) ? '#E1306C' : '#ccc' ?>;"></i>
                        <i class="fab fa-youtube" style="color: <?= !empty($m['youtube']) ? '#ff0000' : '#ccc' ?>;"></i>
                        <i class="fab fa-facebook" style="color: <?= !empty($m['facebook']) ? '#1877f2' : '#ccc' ?>;"></i>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='openEditModal(<?= json_encode($m) ?>)'><i class="fas fa-edit"></i></button>
                        <a href="process_profil_lab.php?delete_member=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus anggota ini?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="memberModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y:auto;">
    <div class="modal-content" style="background:#fff; margin:2% auto; padding:20px; width:600px; border-radius:8px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3 id="modalTitle">Tambah Anggota</h3>
            <span onclick="document.getElementById('memberModal').style.display='none'" style="cursor:pointer; font-size:24px;">&times;</span>
        </div>
        
        <form action="process_profil_lab.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="m_id">
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="m_name" class="form-control" required>
            </div>
            
            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Jabatan / Role</label>
                    <input type="text" name="role" id="m_role" class="form-control" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Tags / Keahlian</label>
                    <input type="text" name="tags" id="m_tags" class="form-control" placeholder="Contoh: UI/UX, Android">
                </div>
            </div>

            <hr style="margin: 15px 0; border:0; border-top:1px solid #eee;">
            <h5 style="margin-bottom:10px; color:#555;">Tautan Sosial Media (Opsional)</h5>

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                    <input type="text" name="linkedin" id="m_linkedin" class="form-control" placeholder="https://linkedin.com/in/...">
                </div>
                <div class="form-group" style="flex:1;">
                    <label><i class="fas fa-graduation-cap"></i> Google Scholar URL</label>
                    <input type="text" name="scholar" id="m_scholar" class="form-control" placeholder="https://scholar.google.com/...">
                </div>
            </div>

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label><i class="fab fa-instagram"></i> Instagram URL</label>
                    <input type="text" name="instagram" id="m_instagram" class="form-control" placeholder="https://instagram.com/...">
                </div>
                <div class="form-group" style="flex:1;">
                    <label><i class="fab fa-facebook"></i> Facebook URL</label>
                    <input type="text" name="facebook" id="m_facebook" class="form-control" placeholder="https://facebook.com/...">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fab fa-youtube"></i> YouTube Channel URL</label>
                <input type="text" name="youtube" id="m_youtube" class="form-control" placeholder="https://youtube.com/...">
            </div>

            <hr style="margin: 15px 0; border:0; border-top:1px solid #eee;">

            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="avatar" class="form-control">
                <small id="fotoHelp">Upload foto baru untuk mengganti.</small>
            </div>

            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('memberModal').style.display='none'">Batal</button>
                <button type="submit" name="add_member" id="btnSubmit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-tab { padding: 10px 20px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; cursor: pointer; color: #666; font-size: 16px; }
    .btn-tab.active { border-bottom: 3px solid #007bff; color: #007bff; }
    .btn-tab:hover { color: #007bff; }
    .row { display: flex; gap: 15px; }
</style>

<script>
function switchTab(tabName) {
    document.getElementById('tab-profil').style.display = 'none';
    document.getElementById('tab-tim').style.display = 'none';
    document.getElementById('btn-profil').classList.remove('active');
    document.getElementById('btn-tim').classList.remove('active');

    document.getElementById('tab-' + tabName).style.display = 'block';
    document.getElementById('btn-' + tabName).classList.add('active');
}

function openAddModal() {
    document.getElementById('memberModal').style.display = 'block';
    document.getElementById('modalTitle').innerText = 'Tambah Anggota';
    document.getElementById('btnSubmit').name = 'add_member';
    document.getElementById('fotoHelp').innerText = 'Format JPG/PNG. Kotak (1:1) lebih baik.';
    
    // Reset form field
    ['m_id', 'm_name', 'm_role', 'm_tags', 'm_linkedin', 'm_scholar', 'm_instagram', 'm_youtube', 'm_facebook'].forEach(id => {
        document.getElementById(id).value = '';
    });
}

function openEditModal(data) {
    document.getElementById('memberModal').style.display = 'block';
    document.getElementById('modalTitle').innerText = 'Edit Anggota';
    document.getElementById('btnSubmit').name = 'edit_member';
    document.getElementById('fotoHelp').innerText = 'Biarkan kosong jika tidak ingin mengganti foto.';

    // Isi form dengan data
    document.getElementById('m_id').value = data.id;
    document.getElementById('m_name').value = data.name;
    document.getElementById('m_role').value = data.role;
    document.getElementById('m_tags').value = data.tags;
    
    // Isi sosmed (pastikan key JSON sesuai nama kolom di database)
    document.getElementById('m_linkedin').value = data.linkedin_url || '';
    document.getElementById('m_scholar').value = data.scholar_url || '';
    document.getElementById('m_instagram').value = data.instagram || '';
    document.getElementById('m_youtube').value = data.youtube || '';
    document.getElementById('m_facebook').value = data.facebook || '';
}

// Tutup modal saat klik luar
window.onclick = function(event) {
    var modal = document.getElementById('memberModal');
    if (event.target == modal) modal.style.display = "none";
}
</script>

<?php include 'components/footer.php'; ?>