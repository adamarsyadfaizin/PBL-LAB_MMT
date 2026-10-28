<?php
/**
 * FILE: config/helpers.php
 * Helper umum: penanganan unggahan file dan pemotongan teks.
 */

if (!function_exists('publicPath')) {
    /**
     * Path absolut ke sebuah folder di dalam project.
     */
    function publicPath(string $relative = ''): string
    {
        return dirname(__DIR__) . '/' . ltrim($relative, '/');
    }
}

if (!function_exists('uploadAvailable')) {
    /**
     * Apakah folder tujuan unggahan bisa ditulisi?
     * Di hosting serverless (Vercel) folder project bersifat read-only,
     * sehingga fitur unggah otomatis dinonaktifkan dengan pesan yang jelas.
     */
    function uploadAvailable(string $directory): bool
    {
        $path = publicPath($directory);

        if (!is_dir($path)) {
            if (!@mkdir($path, 0755, true) && !is_dir($path)) {
                return false;
            }
        }

        return is_writable($path);
    }
}

if (!function_exists('storeUpload')) {
    /**
     * Menyimpan file unggahan dengan validasi ekstensi, ukuran, dan tipe MIME.
     *
     * @param array  $file         Satu entri dari $_FILES
     * @param string $directory    Folder tujuan relatif terhadap root, mis. 'assets/uploads'
     * @param array  $allowedExt   Daftar ekstensi yang diizinkan
     * @param int    $maxBytes     Batas ukuran file
     *
     * @return array{ok: bool, path?: string, error?: string}
     */
    function storeUpload(array $file, string $directory, array $allowedExt, int $maxBytes = 8388608): array
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['ok' => false, 'error' => 'Tidak ada file yang diunggah.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Unggahan gagal (kode ' . $file['error'] . ').'];
        }

        if ($file['size'] > $maxBytes) {
            $maxMb = round($maxBytes / 1048576, 1);
            return ['ok' => false, 'error' => "Ukuran file melebihi {$maxMb} MB."];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExt, true)) {
            return ['ok' => false, 'error' => 'Format file harus: ' . strtoupper(implode(', ', $allowedExt)) . '.'];
        }

        // Cocokkan isi file dengan ekstensinya supaya file berbahaya tidak lolos.
        if (function_exists('finfo_open')) {
            $mimeMap = [
                'jpg'  => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'],
                'gif'  => ['image/gif'],
                'webp' => ['image/webp'],
                'mp4'  => ['video/mp4', 'application/octet-stream'],
                'webm' => ['video/webm'],
            ];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
            if ($finfo) {
                finfo_close($finfo);
            }

            if ($mime && isset($mimeMap[$extension]) && !in_array($mime, $mimeMap[$extension], true)) {
                return ['ok' => false, 'error' => 'Isi file tidak sesuai dengan ekstensinya.'];
            }
        }

        if (!uploadAvailable($directory)) {
            return ['ok' => false, 'error' => 'Penyimpanan file tidak tersedia di server ini (mode demo).'];
        }

        $newName = date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
        $target = publicPath($directory) . '/' . $newName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['ok' => false, 'error' => 'Gagal menyimpan file yang diunggah.'];
        }

        return ['ok' => true, 'path' => trim($directory, '/') . '/' . $newName];
    }
}

if (!function_exists('removeUpload')) {
    /**
     * Menghapus file unggahan lama. Aman kalau file sudah tidak ada.
     */
    function removeUpload(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        // Hanya hapus file di folder unggahan. Gambar bawaan template tidak disentuh.
        $allowedPrefixes = ['assets/uploads/', 'assets/images/news/', 'assets/images/projects/'];
        $allowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (strpos($relativePath, $prefix) === 0) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return;
        }

        $path = publicPath($relativePath);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

if (!function_exists('excerpt')) {
    /**
     * Potong teks dengan aman untuk karakter UTF-8 (tidak memotong kata di tengah).
     */
    function excerpt(?string $text, int $limit = 150): string
    {
        $text = trim(strip_tags((string) $text));

        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit, 'UTF-8');
        $lastSpace = mb_strrpos($cut, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $cut = mb_substr($cut, 0, $lastSpace, 'UTF-8');
        }

        return rtrim($cut) . '…';
    }
}

if (!function_exists('slugify')) {
    /**
     * Membuat slug URL-friendly dari judul.
     */
    function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        return $text !== '' ? $text : 'item-' . time();
    }
}

if (!function_exists('uniqueSlug')) {
    /**
     * Pastikan slug belum dipakai tabel lain; tambahkan akhiran angka bila perlu.
     */
    function uniqueSlug(PDO $pdo, string $table, string $slug, ?int $ignoreId = null): string
    {
        $allowedTables = ['news', 'projects'];
        if (!in_array($table, $allowedTables, true)) {
            return $slug;
        }

        $base = $slug;
        $suffix = 2;

        while (true) {
            $sql = "SELECT COUNT(*) FROM $table WHERE slug = ?";
            $params = [$slug];
            if ($ignoreId) {
                $sql .= " AND id <> ?";
                $params[] = $ignoreId;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            if ((int) $stmt->fetchColumn() === 0) {
                return $slug;
            }

            $slug = $base . '-' . $suffix;
            $suffix++;
        }
    }
}
