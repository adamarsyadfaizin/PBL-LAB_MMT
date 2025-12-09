<?php
// api/backup_lab_data.php - API endpoint untuk backup data lab
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
    
    // Default backup type is 'full'
    $backup_type = $input['backup_type'] ?? 'full';
    
    // Validate backup type
    if (!in_array($backup_type, ['full', 'minimal'])) {
        throw new Exception('Invalid backup type. Must be: full or minimal');
    }
    
    // Call stored procedure
    $result = callStoredProcedure($pdo, 'backup_lab_data', [$backup_type]);
    
    if ($result['success']) {
        // Get backup data for response
        if ($backup_type === 'full') {
            $backup_data = [
                'timestamp' => date('Y-m-d H:i:s'),
                'lab_profile' => $pdo->query("SELECT json_agg(row_to_json(lp)) FROM lab_profile lp")->fetchColumn(),
                'members' => $pdo->query("SELECT json_agg(row_to_json(m)) FROM members m")->fetchColumn(),
                'projects_summary' => $pdo->query("SELECT json_agg(row_to_json(p)) FROM projects p WHERE status IN ('published', '1')")->fetchColumn(),
                'news_summary' => $pdo->query("SELECT json_agg(row_to_json(n)) FROM news n WHERE status = 'published'")->fetchColumn(),
                'stats' => $pdo->query("SELECT row_to_json(s) FROM mv_lab_dashboard_stats s")->fetch()
            ];
        } else {
            $backup_data = [
                'timestamp' => date('Y-m-d H:i:s'),
                'members_count' => $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn(),
                'projects_count' => $pdo->query("SELECT COUNT(*) FROM projects WHERE status IN ('published', '1')")->fetchColumn(),
                'news_count' => $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'")->fetchColumn(),
                'dashboard_stats' => $pdo->query("SELECT row_to_json(s) FROM mv_lab_dashboard_stats s")->fetch()
            ];
        }
        
        // Save backup to file (optional)
        $backup_filename = 'backup_' . $backup_type . '_' . date('Y-m-d_H-i-s') . '.json';
        $backup_path = '../backups/' . $backup_filename;
        
        // Create backups directory if not exists
        if (!file_exists('../backups')) {
            mkdir('../backups', 0777, true);
        }
        
        file_put_contents($backup_path, json_encode($backup_data, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'success' => true,
            'message' => 'Backup berhasil dibuat',
            'data' => [
                'backup_type' => $backup_type,
                'filename' => $backup_filename,
                'file_path' => $backup_path,
                'size' => filesize($backup_path),
                'timestamp' => date('Y-m-d H:i:s'),
                'summary' => [
                    'total_members' => is_array($backup_data['members']) ? count($backup_data['members']) : $backup_data['members_count'],
                    'total_projects' => is_array($backup_data['projects_summary']) ? count($backup_data['projects_summary']) : $backup_data['projects_count'],
                    'total_news' => is_array($backup_data['news_summary']) ? count($backup_data['news_summary']) : $backup_data['news_count']
                ]
            ]
        ]);
    } else {
        throw new Exception($result['error'] ?? 'Backup failed');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
