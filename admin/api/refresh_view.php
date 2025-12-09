<?php
// api/refresh_view.php - API endpoint untuk refresh materialized view
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
    
    if (!$input || empty($input['view_name'])) {
        throw new Exception('view_name is required');
    }
    
    $view_name = $input['view_name'];
    
    // Valid view names
    $valid_views = [
        'mv_lab_dashboard_stats',
        'mv_news_with_stats',
        'mv_project_details',
        'mv_feedback_summary',
        'mv_monthly_activity'
    ];
    
    if (!in_array($view_name, $valid_views)) {
        throw new Exception('Invalid view name');
    }
    
    // Check if view exists
    $check = $pdo->query("SELECT to_regclass('$view_name')")->fetchColumn();
    if (!$check) {
        throw new Exception("View $view_name does not exist");
    }
    
    // Try concurrent refresh first, fallback to regular refresh
    try {
        $pdo->exec("REFRESH MATERIALIZED VIEW CONCURRENTLY $view_name");
        $method = 'concurrent';
    } catch (Exception $e) {
        $pdo->exec("REFRESH MATERIALIZED VIEW $view_name");
        $method = 'regular';
    }
    
    echo json_encode([
        'success' => true,
        'message' => "View $view_name berhasil di-refresh menggunakan method $method",
        'data' => [
            'view_name' => $view_name,
            'refresh_method' => $method,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
