<?php
// api/backup_lab_data.php - API endpoint untuk backup data lab
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Gunakan metode POST.'], 405);
}

try {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];

    // Default backup type is 'full'
    $backup_type = $input['backup_type'] ?? 'full';

    if (!in_array($backup_type, ['full', 'minimal'], true)) {
        throw new Exception('Tipe backup harus "full" atau "minimal".');
    }

    // Jalankan stored procedure (mencatat aktivitas backup)
    $result = callStoredProcedure($pdo, 'backup_lab_data', [$backup_type]);

    if (!$result['success']) {
        throw new Exception($result['error'] ?? 'Backup gagal dijalankan.');
    }

    // Hitung ringkasan dari sumber aslinya supaya selalu tersedia
    $summary = [
        'total_members'  => (int) $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn(),
        'total_projects' => (int) $pdo->query("SELECT COUNT(*) FROM projects WHERE status IN ('published', '1')")->fetchColumn(),
        'total_news'     => (int) $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'")->fetchColumn(),
    ];

    $dashboard_stats = $pdo->query("SELECT row_to_json(s) FROM mv_lab_dashboard_stats s")->fetchColumn();

    if ($backup_type === 'full') {
        $backup_data = [
            'timestamp'        => date('Y-m-d H:i:s'),
            'lab_profile'      => $pdo->query("SELECT json_agg(row_to_json(lp)) FROM lab_profile lp")->fetchColumn(),
            'members'          => $pdo->query("SELECT json_agg(row_to_json(m)) FROM members m")->fetchColumn(),
            'projects_summary' => $pdo->query("SELECT json_agg(row_to_json(p)) FROM projects p WHERE status IN ('published', '1')")->fetchColumn(),
            'news_summary'     => $pdo->query("SELECT json_agg(row_to_json(n)) FROM news n WHERE status = 'published'")->fetchColumn(),
            'dashboard_stats'  => $dashboard_stats,
            'summary'          => $summary,
        ];
    } else {
        $backup_data = [
            'timestamp'       => date('Y-m-d H:i:s'),
            'summary'         => $summary,
            'dashboard_stats' => $dashboard_stats,
        ];
    }

    $json = json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $backup_filename = 'backup_' . $backup_type . '_' . date('Y-m-d_H-i-s') . '.json';
    $backup_dir = __DIR__ . '/../../backups';
    $file_path = null;

    // Di hosting serverless folder project bersifat read-only.
    // Kalau tidak bisa menulis, backup tetap dikirim lewat respons (unduh).
    if (is_dir($backup_dir) || @mkdir($backup_dir, 0755, true)) {
        if (is_writable($backup_dir)) {
            $written = @file_put_contents($backup_dir . '/' . $backup_filename, $json);
            if ($written !== false) {
                $file_path = 'backups/' . $backup_filename;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $file_path
            ? 'Backup berhasil dibuat.'
            : 'Backup dibuat, tetapi tidak dapat disimpan ke server (mode demo).',
        'data' => [
            'backup_type' => $backup_type,
            'filename'    => $backup_filename,
            'file_path'   => $file_path,
            'size'        => strlen($json),
            'timestamp'   => date('Y-m-d H:i:s'),
            'summary'     => $summary,
            'download'    => 'data:application/json;base64,' . base64_encode($json),
        ],
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
