<?php
// api/generate_report.php
header('Content-Type: application/json');
include '../config/db.php';

try {
    // Call stored procedure generate_monthly_report
    $stmt = $pdo->prepare("CALL generate_monthly_report()");
    $stmt->execute();
    
    // Ambil data terbaru dari materialized view
    $stmt = $pdo->query("
        SELECT * FROM mv_monthly_activity 
        ORDER BY month DESC 
        LIMIT 3
    ");
    $recent_data = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'message' => 'Laporan bulanan berhasil dibuat',
        'data' => $recent_data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
