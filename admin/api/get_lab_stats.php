<?php
// api/get_lab_stats.php - API endpoint untuk get_lab_stats function
header('Content-Type: application/json');
include '../config/db.php';

try {
    // Call function get_lab_stats()
    $stmt = $pdo->query("SELECT * FROM get_lab_stats()");
    $stats = $stmt->fetch();
    
    if ($stats) {
        echo json_encode([
            'success' => true,
            'data' => [
                'total_news' => (int)$stats['total_news'],
                'total_projects' => (int)$stats['total_projects'],
                'total_members' => (int)$stats['total_members'],
                'total_comments' => (int)$stats['total_comments'],
                'total_unread_feedback' => (int)$stats['total_unread_feedback'],
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception('No stats data available');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
