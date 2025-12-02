<?php
// get_feedback_detail_mv.php
include '../config/db.php';

header('Content-Type: text/html; charset=utf-8');

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM feedback WHERE id = ?");
        $stmt->execute([$id]);
        $feedback = $stmt->fetch();
        
        if($feedback):
            // Update status dan refresh MV
            $pdo->prepare("UPDATE feedback SET is_read = true WHERE id = ?")->execute([$id]);
            if($pdo->query("SELECT to_regclass('mv_feedback_summary')")->fetchColumn()) {
                $pdo->exec("REFRESH MATERIALIZED VIEW mv_feedback_summary");
            }
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .feedback-detail {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .detail-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-section {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .header-section h2 {
            color: #333;
            font-size: 22px;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-read {
            background: #4CAF50;
            color: white;
        }
        .status-unread {
            background: #FF9800;
            color: white;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border-left: 3px solid #007bff;
        }
        .card-title {
            color: #555;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .card-content {
            color: #333;
            font-size: 14px;
        }
        .message-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e3f2fd;
            margin-bottom: 25px;
        }
        .message-content {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-top: 12px;
            line-height: 1.5;
            font-size: 14px;
            white-space: pre-wrap;
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ddd;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            min-width: 120px;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .email-link {
            color: #007bff;
            text-decoration: none;
        }
        .email-link:hover {
            text-decoration: underline;
        }
        .timestamp {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="feedback-detail">
        <div class="detail-container">
            <div class="header-section">
                <h2>Detail Pesan</h2>
                <span class="status-badge <?= $feedback['is_read'] ? 'status-read' : 'status-unread' ?>">
                    <?= $feedback['is_read'] ? 'Sudah Dibaca' : 'Belum Dibaca' ?>
                </span>
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
                            <strong>Status:</strong> <?= $feedback['is_read'] ? 'Sudah dibaca' : 'Belum dibaca' ?>
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
        const modal = window.parent.document.getElementById('detailModal');
        if(modal) {
            modal.style.display = 'none';
        }
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
            echo "<div style='padding: 30px; text-align: center;'>";
            echo "<div style='color: #dc3545; margin-bottom: 15px;'>Pesan tidak ditemukan</div>";
            echo "<button onclick='closeModal()' style='padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;'>Tutup</button>";
            echo "</div>";
        endif;
        
    } catch (PDOException $e) {
        echo "<div style='padding: 30px; text-align: center;'>";
        echo "<div style='color: #dc3545; margin-bottom: 15px;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<button onclick='closeModal()' style='padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;'>Tutup</button>";
        echo "</div>";
    }
} else {
    echo "<div style='padding: 30px; text-align: center;'>";
    echo "<div style='color: #dc3545; margin-bottom: 15px;'>ID tidak valid</div>";
    echo "<button onclick='closeModal()' style='padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;'>Tutup</button>";
    echo "</div>";
}
?>