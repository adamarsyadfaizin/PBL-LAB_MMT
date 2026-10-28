<?php
/**
 * FILE: config/db.php
 * Koneksi database PostgreSQL + kumpulan helper database.
 *
 * Konfigurasi diambil dari environment variable supaya kode yang sama bisa
 * dipakai di lokal (Laragon) maupun di hosting serverless (Vercel + Neon).
 * Salin .env.example menjadi .env lalu sesuaikan nilainya.
 */

require_once __DIR__ . '/env.php';

// Tampilkan error hanya saat pengembangan.
$appDebug = envBool('APP_DEBUG', false);
error_reporting(E_ALL);
ini_set('display_errors', $appDebug ? '1' : '0');
ini_set('log_errors', '1');

/**
 * Menyusun daftar opsi koneksi dari environment.
 * Mendukung dua gaya konfigurasi:
 * 1. DATABASE_URL  -> postgres://user:pass@host:5432/dbname?sslmode=require
 * 2. DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD (dipisah)
 */
if (!function_exists('buildDatabaseConfig')) {
    function buildDatabaseConfig(): array
    {
        $config = [
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', '5432'),
            'dbname'   => env('DB_NAME', 'db_pbl'),
            'user'     => env('DB_USER', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'sslmode'  => env('DB_SSLMODE', ''),
        ];

        $url = env('DATABASE_URL');
        if ($url) {
            $parts = parse_url($url);
            if ($parts && isset($parts['host'])) {
                $config['host'] = $parts['host'];
                $config['port'] = $parts['port'] ?? $config['port'];
                $config['user'] = isset($parts['user']) ? rawurldecode($parts['user']) : $config['user'];
                $config['password'] = isset($parts['pass']) ? rawurldecode($parts['pass']) : $config['password'];
                $config['dbname'] = isset($parts['path']) ? ltrim($parts['path'], '/') : $config['dbname'];

                if (!empty($parts['query'])) {
                    parse_str($parts['query'], $query);
                    if (!empty($query['sslmode'])) {
                        $config['sslmode'] = $query['sslmode'];
                    }
                }
            }
        }

        // Host selain localhost (mis. Neon, Supabase) hampir selalu butuh SSL.
        if ($config['sslmode'] === '') {
            $isLocal = in_array($config['host'], ['localhost', '127.0.0.1', '::1'], true);
            $config['sslmode'] = $isLocal ? 'prefer' : 'require';
        }

        return $config;
    }
}

$dbConfig = buildDatabaseConfig();

try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['dbname'],
        $dbConfig['sslmode']
    );

    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 10,
    ]);
} catch (PDOException $e) {
    error_log('[DB] Koneksi gagal: ' . $e->getMessage());

    http_response_code(500);
    if ($appDebug) {
        die('Koneksi ke database gagal: ' . htmlspecialchars($e->getMessage()));
    }
    die('Maaf, layanan sedang tidak tersedia. Silakan coba beberapa saat lagi.');
}

// --- FUNGSI LOG (DIBUNGKUS IF BIAR GAK ERROR REDECLARE) ---
if (!function_exists('writeLog')) {

    function writeLog($pdo, $username, $action) {
        // Ambil data user
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $device = $_SERVER['HTTP_USER_AGENT'] ?? '';

        try {
            // SQL Query (Syntax PostgreSQL)
            $sql = "INSERT INTO activity_logs (username, action, ip_address, device_info) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $action, $ip, $device]);
        } catch (PDOException $e) {
            // Biarkan kosong agar error log tidak mengganggu user
        }
    }

}

// --- FUNGSI REFRESH MATERIALIZED VIEWS ---
if (!function_exists('refreshMaterializedViews')) {

    function refreshMaterializedViews($pdo, $specific_views = []) {
        $views = [
            'mv_lab_dashboard_stats',
            'mv_news_with_stats',
            'mv_project_details',
            'mv_feedback_summary',
            'mv_monthly_activity'
        ];

        // Jika specific views disediakan, gunakan itu saja
        if (!empty($specific_views)) {
            $views = array_values(array_intersect($views, $specific_views));
        }

        $refreshed = [];
        $errors = [];

        foreach ($views as $view) {
            try {
                // Check if view exists
                $check = $pdo->query("SELECT to_regclass('$view')")->fetchColumn();
                if (!$check) {
                    continue;
                }

                try {
                    // Butuh unique index; kalau belum ada, fallback ke refresh biasa
                    $pdo->exec("REFRESH MATERIALIZED VIEW CONCURRENTLY $view");
                } catch (PDOException $e) {
                    $pdo->exec("REFRESH MATERIALIZED VIEW $view");
                }

                $refreshed[] = $view;
            } catch (Exception $e) {
                $errors[$view] = $e->getMessage();
            }
        }

        return [
            'success' => $refreshed,
            'errors' => $errors
        ];
    }

}

// --- FUNGSI REFRESH OTOMATIS SETELAH CRUD ---
if (!function_exists('autoRefreshAfterCRUD')) {

    function autoRefreshAfterCRUD($pdo, $operation_type) {
        // Mapping operation ke views yang perlu di-refresh
        $refresh_map = [
            'news' => ['mv_lab_dashboard_stats', 'mv_news_with_stats'],
            'project' => ['mv_lab_dashboard_stats', 'mv_project_details'],
            'feedback' => ['mv_lab_dashboard_stats', 'mv_feedback_summary'],
            'comment' => ['mv_lab_dashboard_stats', 'mv_news_with_stats', 'mv_project_details', 'mv_monthly_activity'],
            'member' => ['mv_lab_dashboard_stats', 'mv_project_details'],
            'media' => ['mv_lab_dashboard_stats'],
            'all' => [] // Refresh all if 'all'
        ];

        $views_to_refresh = $refresh_map[$operation_type] ?? [];

        if ($operation_type === 'all') {
            return refreshMaterializedViews($pdo);
        }

        return refreshMaterializedViews($pdo, $views_to_refresh);
    }

}

// --- FUNGSI CALL STORED PROCEDURE ---
if (!function_exists('callStoredProcedure')) {

    function callStoredProcedure($pdo, $procedure_name, $params = []) {
        try {
            // Build parameter placeholders
            $placeholders = empty($params) ? '' : str_repeat('?,', count($params) - 1) . '?';

            $sql = "CALL $procedure_name($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // Auto refresh relevant views after procedure call
            $procedure_view_map = [
                'add_comment' => ['comment'],
                'update_entity_rating' => ['comment'],
                'generate_monthly_report' => ['mv_monthly_activity', 'mv_lab_dashboard_stats'],
                'cleanup_old_data' => ['all'],
                'backup_lab_data' => []
            ];

            $views_to_refresh = $procedure_view_map[$procedure_name] ?? [];
            if (!empty($views_to_refresh)) {
                autoRefreshAfterCRUD($pdo, $views_to_refresh[0] ?? 'all');
            }

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

}
