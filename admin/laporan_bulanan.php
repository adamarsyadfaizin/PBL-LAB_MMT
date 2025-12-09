<?php include 'components/header.php'; ?>

<div class="header-flex">
    <h1>Laporan Aktivitas Bulanan</h1>
    <div>
        <button onclick="refreshData()" class="btn btn-primary">Refresh Data</button>
        <button onclick="generateReport()" class="btn btn-success">
            <i class="fas fa-file-download"></i> Generate Laporan
        </button>
    </div>
</div>

<?php
// Refresh dan ambil data dari materialized view
try {
    $pdo->exec("REFRESH MATERIALIZED VIEW CONCURRENTLY mv_monthly_activity");
    $stmt = $pdo->query("SELECT * FROM mv_monthly_activity ORDER BY month DESC LIMIT 24");
    $monthly_data = $stmt->fetchAll();
} catch (Exception $e) {
    // Fallback query jika materialized view gagal
    $stmt = $pdo->query("
        SELECT 
            date_trunc('month', created_at) as month,
            COUNT(*) FILTER (WHERE entity_type = 'news') as news_comments,
            COUNT(*) FILTER (WHERE entity_type = 'media') as media_comments,
            COUNT(*) FILTER (WHERE entity_type IS NULL) as other_comments,
            COUNT(*) as total_comments
        FROM comments 
        WHERE status = 'approved'
        GROUP BY date_trunc('month', created_at)
        ORDER BY month DESC
        LIMIT 24
    ");
    $monthly_data = $stmt->fetchAll();
}

// Ambil statistik tambahan
$stats_query = $pdo->query("
    SELECT 
        COUNT(DISTINCT DATE_TRUNC('month', created_at)) as total_months,
        SUM(total_comments) as total_all_comments,
        ROUND(AVG(total_comments)) as avg_comments_per_month
    FROM mv_monthly_activity
");
$stats = $stats_query->fetch();
?>

<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $stats['total_months'] ?? 0 ?></h3>
            <p>Total Bulan Aktif</p>
        </div>
        <div class="stat-icon"><i class="fas fa-calendar"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= number_format($stats['total_all_comments'] ?? 0) ?></h3>
            <p>Total Komentar</p>
        </div>
        <div class="stat-icon"><i class="fas fa-comments"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= number_format($stats['avg_comments_per_month'] ?? 0) ?></h3>
            <p>Rata-rata/Bulan</p>
        </div>
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
    </div>
</div>

<div class="table-card">
    <h3 style="margin-bottom: 15px;">Detail Aktivitas Bulanan</h3>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Komentar Berita</th>
                <th>Komentar Media</th>
                <th>Komentar Lainnya</th>
                <th>Total Komentar</th>
                <th>Trend</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $prev_total = null;
            foreach ($monthly_data as $index => $row): 
                $month_name = date('F Y', strtotime($row['month']));
                $trend = '';
                $trend_class = '';
                
                if ($prev_total !== null) {
                    if ($row['total_comments'] > $prev_total) {
                        $trend = '↑ ' . number_format(($row['total_comments'] - $prev_total) / $prev_total * 100, 1) . '%';
                        $trend_class = 'text-success';
                    } elseif ($row['total_comments'] < $prev_total) {
                        $trend = '↓ ' . number_format(($prev_total - $row['total_comments']) / $prev_total * 100, 1) . '%';
                        $trend_class = 'text-danger';
                    } else {
                        $trend = '→ 0%';
                        $trend_class = 'text-muted';
                    }
                }
            ?>
            <tr>
                <td><strong><?= $month_name ?></strong></td>
                <td>
                    <span class="badge badge-info"><?= number_format($row['news_comments']) ?></span>
                </td>
                <td>
                    <span class="badge badge-primary"><?= number_format($row['media_comments']) ?></span>
                </td>
                <td>
                    <span class="badge badge-secondary"><?= number_format($row['other_comments']) ?></span>
                </td>
                <td>
                    <strong><?= number_format($row['total_comments']) ?></strong>
                </td>
                <td>
                    <?php if (!empty($trend)): ?>
                        <span class="<?= $trend_class ?>"><?= $trend ?></span>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php 
            $prev_total = $row['total_comments'];
            endforeach; 
            
            if (empty($monthly_data)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    Tidak ada data aktivitas bulanan
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Chart Section -->
<div class="table-card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 15px;">Visualisasi Trend</h3>
    <div style="height: 300px; position: relative;">
        <canvas id="activityChart"></canvas>
    </div>
</div>

<style>
.text-success { color: #28a745; }
.text-danger { color: #dc3545; }
.text-muted { color: #6c757d; }
.badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; color: white; }
.badge-info { background: #17a2b8; }
.badge-primary { background: #007bff; }
.badge-secondary { background: #6c757d; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data untuk chart
const chartData = <?= json_encode(array_reverse(array_slice($monthly_data, 0, 12))) ?>;
const labels = chartData.map(row => {
    const date = new Date(row.month);
    return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
});

const ctx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Komentar',
            data: chartData.map(row => parseInt(row.total_comments)),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
        }, {
            label: 'Komentar Berita',
            data: chartData.map(row => parseInt(row.news_comments)),
            borderColor: '#17a2b8',
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            tension: 0.4
        }, {
            label: 'Komentar Media',
            data: chartData.map(row => parseInt(row.media_comments)),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function refreshData() {
    if(confirm('Refresh data laporan?')) {
        location.reload();
    }
}

function generateReport() {
    // Call stored procedure untuk generate laporan
    fetch('api/generate_report.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Laporan berhasil dibuat! Check console untuk detail.');
            console.log('Report:', data.report);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat generate laporan');
    });
}
</script>

<?php include 'components/footer.php'; ?>
