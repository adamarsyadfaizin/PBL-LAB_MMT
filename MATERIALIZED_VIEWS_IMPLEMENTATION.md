# Implementasi Materialized Views di PBL-LAB_MMT

## Overview
Implementasi materialized views untuk meningkatkan performa query dan menyediakan data agregat yang sudah dihitung sebelumnya.

## Materialized Views yang Diimplementasikan

### 1. `mv_lab_dashboard_stats`
- **Fungsi**: Menyimpan statistik dashboard (total berita, proyek, anggota, dll)
- **Implementasi**: `admin/index.php`
- **Auto-refresh**: Ya, setiap kali halaman dibuka
- **Fitur**: Menampilkan 8 kartu statistik lengkap

### 2. `mv_news_with_stats`
- **Fungsi**: Data berita dengan statistik komentar, rating, dan tags
- **Implementasi**: `admin/berita.php`
- **Auto-refresh**: Ya, setiap kali halaman dibuka
- **Fitur**: Menampilkan jumlah komentar, rating rata-rata, jumlah tags

### 3. `mv_project_details`
- **Fungsi**: Data proyek dengan detail kategori, anggota, komentar, rating
- **Implementasi**: `admin/proyek.php`
- **Auto-refresh**: Ya, setiap kali halaman dibuka
- **Fitur**: Menampilkan jumlah anggota, komentar, rating, kategori

### 4. `mv_feedback_summary`
- **Fungsi**: Ringkasan feedback/pesan masuk
- **Implementasi**: `admin/pesan.php` (sudah ada sebelumnya)
- **Auto-refresh**: Ya, setelah CRUD operations
- **Fitur**: Preview pesan, status read/unread

### 5. `mv_monthly_activity`
- **Fungsi**: Statistik aktivitas bulanan (komentar per bulan)
- **Implementasi**: `admin/laporan_bulanan.php` (baru)
- **Auto-refresh**: Ya, setiap kali halaman dibuka
- **Fitur**: Chart visualisasi trend, perbandingan bulanan

## Fungsi Helper yang Ditambahkan

### `refreshMaterializedViews($pdo, $specific_views = [])`
- Refresh materialized views tertentu atau semua
- Return array dengan status success/errors

### `autoRefreshAfterCRUD($pdo, $operation_type)`
- Auto-refresh views berdasarkan tipe operasi
- Mapping:
  - `news` → `mv_lab_dashboard_stats`, `mv_news_with_stats`
  - `project` → `mv_lab_dashboard_stats`, `mv_project_details`
  - `feedback` → `mv_lab_dashboard_stats`, `mv_feedback_summary`
  - `comment` → `mv_lab_dashboard_stats`, `mv_news_with_stats`, `mv_project_details`, `mv_monthly_activity`
  - `all` → refresh semua views

### `callStoredProcedure($pdo, $procedure_name, $params = [])`
- Execute stored procedure dengan auto-refresh
- Support untuk procedures: `add_comment`, `update_entity_rating`, `generate_monthly_report`, dll

## Implementasi Auto-Refresh

### Di File Process CRUD
Contoh implementasi di `process_berita.php`:
```php
// Setelah delete
$stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
$stmt->execute([$id]);
autoRefreshAfterCRUD($pdo, 'news');

// Setelah insert/update
autoRefreshAfterCRUD($pdo, 'news');
```

### Di Halaman Display
Contoh implementasi di halaman admin:
```php
try {
    $pdo->exec("REFRESH MATERIALIZED VIEW CONCURRENTLY mv_lab_dashboard_stats");
    $stmt = $pdo->query("SELECT * FROM mv_lab_dashboard_stats");
    $stats = $stmt->fetch();
} catch (Exception $e) {
    // Fallback ke query biasa
}
```

## Stored Procedures yang Tersedia

### `add_comment(entity_type, entity_id, author_name, author_email, rating, content, user_id)`
- Tambah komentar dengan validasi
- Auto-update rating entity
- Auto-refresh relevant views

### `generate_monthly_report(month)`
- Generate laporan aktivitas bulanan
- Refresh `mv_monthly_activity` dan `mv_lab_dashboard_stats`

### `cleanup_old_data(days_old)`
- Cleanup data lama (arsip)
- Refresh semua views

### `backup_lab_data(backup_type)`
- Backup data lab (full/minimal)

## File yang Dimodifikasi/Ditambahkan

### File yang Diupdate:
1. `config/db.php` - Tambah fungsi helper
2. `admin/index.php` - Implementasi `mv_lab_dashboard_stats`
3. `admin/berita.php` - Implementasi `mv_news_with_stats`
4. `admin/proyek.php` - Implementasi `mv_project_details`
5. `admin/process_berita.php` - Tambah auto-refresh

### File yang Ditambahkan:
1. `admin/laporan_bulanan.php` - Halaman laporan dengan `mv_monthly_activity`
2. `admin/api/generate_report.php` - API endpoint untuk generate laporan

## Best Practices

### 1. Concurrent Refresh
- Gunakan `REFRESH MATERIALIZED VIEW CONCURRENTLY` untuk menghindari locking
- Tidak semua view support concurrent refresh (tergantung complexity)

### 2. Error Handling
- Selalu gunakan try-catch untuk materialized view operations
- Sediakan fallback query jika materialized view gagal

### 3. Refresh Strategy
- Refresh setelah CRUD operations untuk data consistency
- Refresh di halaman display untuk data terbaru
- Avoid over-refreshing (consider cache strategy)

### 4. Performance Considerations
- Materialized views cocok untuk data yang sering dibaca tapi jarang berubah
- Monitor refresh time untuk view yang kompleks
- Consider refresh schedule untuk view yang sangat besar

## Monitoring & Maintenance

### Check Materialized View Status:
```sql
-- Cek ukuran materialized views
SELECT schemaname, matviewname, pg_size_pretty(pg_total_relation_size(schemaname||'.'||matviewname)) as size
FROM pg_matviews WHERE schemaname = 'public';

-- Cek last refresh time
SELECT matviewname, pg_size_pretty(pg_total_relation_size(schemaname||'.'||matviewname)) as size
FROM pg_matviews WHERE schemaname = 'public';
```

### Manual Refresh:
```sql
-- Refresh single view
REFRESH MATERIALIZED VIEW mv_lab_dashboard_stats;

-- Refresh all views
DO $$
DECLARE
    mv record;
BEGIN
    FOR mv IN SELECT matviewname FROM pg_matviews WHERE schemaname = 'public' LOOP
        EXECUTE 'REFRESH MATERIALIZED VIEW ' || mv.matviewname;
    END LOOP;
END $$;
```

## Troubleshooting

### Common Issues:
1. **Concurrent refresh not supported** → Gunakan refresh biasa
2. **View not found** → Check jika view sudah dibuat
3. **Long refresh time** → Optimize query atau consider partial refresh
4. **Stale data** → Implement proper refresh strategy

### Debug Commands:
```php
// Check if view exists
$exists = $pdo->query("SELECT to_regclass('mv_lab_dashboard_stats')")->fetchColumn();

// Force refresh all views
$result = refreshMaterializedViews($pdo);
print_r($result);
```

## Next Steps

1. Implementasi di halaman frontend (user-facing)
2. Add caching layer untuk reduce refresh frequency
3. Schedule automatic refresh untuk view tertentu
4. Monitor performance dan optimize query
5. Add more materialized views untuk kompleks queries lainnya
