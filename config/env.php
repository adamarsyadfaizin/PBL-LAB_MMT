<?php
/**
 * FILE: config/env.php
 * Pembaca konfigurasi berbasis environment variable.
 *
 * Di lokal nilai diambil dari file .env di root project.
 * Di Vercel nilai diambil dari Environment Variables pada dashboard.
 */

if (!function_exists('loadEnvFile')) {
    /**
     * Membaca file .env sederhana (KEY=VALUE) tanpa dependency composer.
     * Nilai yang sudah ada di environment tidak ditimpa.
     */
    function loadEnvFile(string $file): void
    {
        if (!is_readable($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Buang tanda kutip pembungkus jika ada
            $length = strlen($value);
            if ($length >= 2) {
                $first = $value[0];
                $last = $value[$length - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            if ($key !== '' && getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

if (!function_exists('env')) {
    /**
     * Ambil nilai environment dengan nilai default opsional.
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }

        // Konversi tipe umum agar nyaman dipakai
        $lower = strtolower($value);
        if ($lower === 'true' || $lower === '(true)') {
            return true;
        }
        if ($lower === 'false' || $lower === '(false)') {
            return false;
        }
        if ($lower === 'null' || $lower === '(null)') {
            return null;
        }

        return $value;
    }
}

if (!function_exists('envBool')) {
    function envBool(string $key, bool $default = false): bool
    {
        $value = env($key);
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

loadEnvFile(dirname(__DIR__) . '/.env');
