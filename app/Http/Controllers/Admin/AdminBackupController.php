<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminBackupController extends Controller
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Halaman Manajemen Backup Database
     */
    public function index()
    {
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $bytes = $file->getSize();
                $sizeFormatted = $bytes >= 1048576 
                    ? number_format($bytes / 1048576, 2) . ' MB' 
                    : number_format($bytes / 1024, 1) . ' KB';

                $backups[] = [
                    'filename'   => $file->getFilename(),
                    'size'       => $sizeFormatted,
                    'bytes'      => $bytes,
                    'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                ];
            }
        }

        // Urutkan backup terbaru di atas
        usort($backups, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        $dbName = config('database.connections.' . config('database.default') . '.database', 'simrs');

        return view('admin.backup.index', compact('backups', 'dbName'));
    }

    /**
     * Buat file backup database baru dan simpan di server
     */
    public function generate(Request $request)
    {
        try {
            $sqlContent = $this->dumpDatabaseSql();
            $filename = 'simrs_backup_' . date('Y-m-d_His') . '.sql';
            $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

            File::put($filePath, $sqlContent);

            return redirect()->route('admin.backup')->with('status_backup', [
                'type'    => 'success',
                'message' => "Backup database berhasil dibuat: {$filename} (" . number_format(strlen($sqlContent) / 1024, 1) . " KB)",
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.backup')->with('status_backup', [
                'type'    => 'error',
                'message' => 'Gagal membuat backup database: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Download instant backup database (langsung unduh ke browser tanpa menyimpan di server)
     */
    public function downloadInstant()
    {
        try {
            $sqlContent = $this->dumpDatabaseSql();
            $filename = 'simrs_backup_instant_' . date('Y-m-d_His') . '.sql';

            return response($sqlContent, 200, [
                'Content-Type'        => 'application/sql',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return back()->with('status_backup', [
                'type'    => 'error',
                'message' => 'Gagal mengunduh backup database: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Unduh file backup yang sudah ada
     */
    public function downloadFile(string $filename)
    {
        $safeName = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (!File::exists($filePath)) {
            return back()->with('status_backup', [
                'type'    => 'error',
                'message' => "File backup {$safeName} tidak ditemukan.",
            ]);
        }

        return response()->download($filePath, $safeName, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Hapus file backup
     */
    public function deleteFile(string $filename)
    {
        $safeName = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($filePath)) {
            File::delete($filePath);
            return back()->with('status_backup', [
                'type'    => 'success',
                'message' => "File backup {$safeName} berhasil dihapus.",
            ]);
        }

        return back()->with('status_backup', [
            'type'    => 'error',
            'message' => "File backup tidak ditemukan.",
        ]);
    }

    /**
     * Ekspor seluruh struktur dan data database menjadi format SQL (Mendukung MySQL & SQLite via PDO murni)
     */
    protected function dumpDatabaseSql(): string
    {
        $driver = config('database.default');
        $dbName = config("database.connections.{$driver}.database", 'simrs');

        $out = "-- ========================================================\n";
        $out .= "-- BACKUP DATABASE SISTEM INFORMASI MANAJEMEN RUMAH SAKIT\n";
        $out .= "-- RUMAH SAKIT DELTA (SIMRS ENTERPRISE)\n";
        $out .= "-- Tanggal Backup : " . Carbon::now()->translatedFormat('l, d F Y H:i:s') . "\n";
        $out .= "-- Database Engine: " . strtoupper($driver) . " ({$dbName})\n";
        $out .= "-- ========================================================\n\n";

        $out .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $out .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $out .= "START TRANSACTION;\n\n";

        $tables = [];

        if ($driver === 'sqlite') {
            $sqliteTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($sqliteTables as $t) {
                $tables[] = $t->name;
            }
        } else {
            // MySQL / MariaDB
            $rawTables = DB::select('SHOW TABLES');
            foreach ($rawTables as $row) {
                $prop = array_values((array)$row);
                $tables[] = $prop[0];
            }
        }

        foreach ($tables as $table) {
            $out .= "-- --------------------------------------------------------\n";
            $out .= "-- Struktur Tabel: `{$table}`\n";
            $out .= "-- --------------------------------------------------------\n";
            $out .= "DROP TABLE IF EXISTS `{$table}`;\n";

            if ($driver === 'sqlite') {
                $createRow = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (!empty($createRow)) {
                    $out .= $createRow[0]->sql . ";\n\n";
                }
            } else {
                $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
                if (!empty($createRow)) {
                    $prop = (array)$createRow[0];
                    $out .= $prop['Create Table'] . ";\n\n";
                }
            }

            // Dump Data Baris
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $out .= "-- Data Baris untuk Tabel `{$table}` (" . $rows->count() . " baris)\n";
                
                $chunks = $rows->chunk(100);
                foreach ($chunks as $chunk) {
                    $insertVals = [];
                    foreach ($chunk as $row) {
                        $values = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $values[] = 'NULL';
                            } elseif (is_numeric($val) && !is_string($val)) {
                                $values[] = $val;
                            } else {
                                $escaped = addslashes((string)$val);
                                $escaped = str_replace(["\r", "\n"], ["\\r", "\\n"], $escaped);
                                $values[] = "'{$escaped}'";
                            }
                        }
                        $insertVals[] = '(' . implode(', ', $values) . ')';
                    }

                    $columns = array_keys((array)$chunk->first());
                    $colList = '`' . implode('`, `', $columns) . '`';
                    $out .= "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $insertVals) . ";\n";
                }
                $out .= "\n";
            }
        }

        $out .= "COMMIT;\n";
        $out .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $out .= "-- ======================== AKHIR BACKUP ========================\n";

        return $out;
    }
}
