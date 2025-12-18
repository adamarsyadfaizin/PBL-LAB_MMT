<?php
// get_feedback_detail_mv.php
// Note: Nama file dibiarkan tetap ada "_mv" agar tidak perlu mengubah javascript di pesan.php
include '../config/db.php';

header('Content-Type: text/html; charset=utf-8');

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM feedback WHERE id = ?");
        $stmt->execute([$id]);
        $feedback = $stmt->fetch();
        
        if($feedback):
            // Update status menjadi terbaca (tanpa refresh MV)
            if (!$feedback['is_read']) {
                $pdo->prepare("UPDATE feedback SET is_read = true WHERE id = ?")->execute([$id]);
            }
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .feedback-detail { padding: 20px; font-family: Arial, sans-serif; }
        .detail-container { background: white; border-radius: 10px; padding: 25px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header-section { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .header-section h2 { color: #333; font-size: 22px; margin-bottom: 10px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .status-read { background: #4CAF50; color: white; }
        .status-unread { background: #FF9800; color: white; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        @media (max-width: 768px) { .detail-grid { grid-template-columns: 1fr; } }
        .info-card { background: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 3px solid #007bff; }
        .card-title { color: #555; font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; }
        .card-content { color: #333; font-size: 14px; }
        .message-card { background: #fff; border-radius: 8px; padding: 20px; border: 1px solid #e3f2fd; margin-bottom: 25px; }
        .message-content { background: #f9f9f9; padding: 15px; border-radius: 6px; margin-top: 12px; line-height: 1.5; font-size: 14px; white-space: pre-wrap; max-height: 250px; overflow-y: auto; border: 1px solid #ddd; }
        .action-buttons { display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #eee; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 500; min-width: 120px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .email-link { color: #007bff; text-decoration: none; }
        .email-link:hover { text-decoration: underline; }
        .timestamp { color: #666; font-size: 12px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="feedback-detail">
        <div class="detail-container">
            <div class="header-section">
                <h2>Detail Pesan</h2>
                <span class="status-badge status-read">Sudah Dibaca</span>
            </div>
            
            <div class="detail-grid">
                <div class="info-card">
                    <div class="card-title">Informasi Pengirim</div>
                    <div class="card-content">
                        <div style="font-weight: bold; margin-bottom: 5px;">
                            <?= htmlspecialchars($feedback['nama_lengkap']) ?>
                        </div>
                        <div>
                            Email: <a href="mailto:<?= htmlspecialchars($feedback['email']) ?>" class="email-link">
                                <?= htmlspecialchars($feedback['email']) ?>
                            </a>
                        </div>
                        <div class="timestamp">
                            Dikirim: <?= date('d M Y H:i', strtotime($feedback['created_at'])) ?>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="card-title">Informasi Pesan</div>
                    <div class="card-content">
                        <div style="margin-bottom: 8px;">
                            <strong>ID:</strong> #<?= $feedback['id'] ?>
                        </div>
                        <div>
                            <strong>Status:</strong> Sudah dibaca
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="card-title">Subjek Pesan</div>
                <div class="card-content" style="font-size: 15px; font-weight: 500; background: #e3f2fd; padding: 12px; border-radius: 5px;">
                    <?= htmlspecialchars($feedback['subjek']) ?>
                </div>
            </div>
            
            <div class="message-card">
                <div class="card-title">Isi Pesan Lengkap</div>
                <div class="message-content">
                    <?= nl2br(htmlspecialchars($feedback['pesan'])) ?>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="pesan.php?delete=<?= $feedback['id'] ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Yakin hapus pesan ini?')">
                    Hapus Pesan
                </a>
            </div>
        </div>
    </div>

    <script>
    function closeModal() {
        // Refresh parent page saat modal ditutup agar status 'unread' di tabel hilang
        window.parent.location.reload();
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    </script>
</body>
</html>
<?php
        else:
            echo "<div style='padding: 30px; text-align: center; color: #dc3545;'>Pesan tidak ditemukan</div>";
        endif;
        
    } catch (PDOException $e) {
        echo "<div style='padding: 30px; text-align: center; color: #dc3545;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div style='padding: 30px; text-align: center; color: #dc3545;'>ID tidak valid</div>";
}
?>