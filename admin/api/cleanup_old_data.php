<?php
// api/cleanup_old_data.php - API endpoint untuk cleanup data lama
header('Content-Type: application/json');
include '../config/db.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Default days_old is 365 (1 year)
    $days_old = $input['days_old'] ?? 365;
    
    // Validate days_old
    if (!is_numeric($days_old) || $days_old < 1) {
        throw new Exception('days_old harus berupa angka positif');
    }
    
    // Get statistics before cleanup
    $before_stats = [
        'old_comments' => $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending' AND created_at < NOW() - INTERVAL '30 days'")->fetchColumn(),
        'old_feedback' => $pdo->query("SELECT COUNT(*) FROM feedback WHERE is_read = true AND created_at < NOW() - INTERVAL '180 days'")->fetchColumn(),
        'old_projects' => $pdo->query("SELECT COUNT(*) FROM projects WHERE status = '1' AND updated_at < NOW() - INTERVAL '{$days_old} days'")->fetchColumn(),
        'old_news' => $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published' AND updated_at < NOW() - INTERVAL '{$days_old} days'")->fetchColumn()
    ];
    
    // Call stored procedure
    $result = callStoredProcedure($pdo, 'cleanup_old_data', [(int)$days_old]);
    
    if ($result['success']) {
        // Get statistics after cleanup
        $after_stats = [
            'old_comments' => $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending' AND created_at < NOW() - INTERVAL '30 days'")->fetchColumn(),
            'old_feedback' => $pdo->query("SELECT COUNT(*) FROM feedback WHERE is_read = true AND created_at < NOW() - INTERVAL '180 days'")->fetchColumn(),
            'old_projects' => $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'archived' AND updated_at < NOW() - INTERVAL '{$days_old} days'")->fetchColumn(),
            'old_news' => $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'archived' AND updated_at < NOW() - INTERVAL '{$days_old} days'")->fetchColumn()
        ];
        
        // Calculate differences
        $cleaned = [
            'comments_cleaned' => $before_stats['old_comments'] - $after_stats['old_comments'],
            'feedback_cleaned' => $before_stats['old_feedback'] - $after_stats['old_feedback'],
            'projects_archived' => $before_stats['old_projects'] - $after_stats['old_projects'],
            'news_archived' => $before_stats['old_news'] - $after_stats['old_news']
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Cleanup data lama berhasil dilakukan',
            'data' => [
                'days_old' => (int)$days_old,
                'cleaned_items' => $cleaned,
                'total_cleaned' => array_sum($cleaned),
                'before_stats' => $before_stats,
                'after_stats' => $after_stats,
                'timestamp' => date('Y-m-d H:i:s'),
                'details' => [
                    'comments_deleted' => $cleaned['comments_cleaned'] . ' komentar pending (>30 hari)',
                    'feedback_deleted' => $cleaned['feedback_cleaned'] . ' feedback yang sudah dibaca (>180 hari)',
                    'projects_archived' => $cleaned['projects_archived'] . ' proyek di-arsipkan (>' . $days_old . ' hari)',
                    'news_archived' => $cleaned['news_archived'] . ' berita di-arsipkan (>' . $days_old . ' hari)'
                ]
            ]
        ]);
    } else {
        throw new Exception($result['error'] ?? 'Cleanup failed');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
