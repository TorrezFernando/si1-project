<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$backupFile = base_path('backup_antes_de_limpieza.sql');
$handle = fopen($backupFile, 'w');

fprintf($handle, "-- Backup Colegio DB\n");
fprintf($handle, "-- Generado: " . date('Y-m-d H:i:s') . "\n");
fprintf($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

$tables = DB::select('SHOW TABLES');
$key = 'Tables_in_' . env('DB_DATABASE');

foreach ($tables as $table) {
    $tableName = $table->$key;
    
    // Schema
    $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0];
    $createSql = $createTable->{'Create Table'};
    fprintf($handle, "DROP TABLE IF EXISTS `$tableName`;\n");
    fprintf($handle, $createSql . ";\n\n");
    
    // Data
    $rows = DB::table($tableName)->get();
    if ($rows->count() > 0) {
        fprintf($handle, "INSERT INTO `$tableName` VALUES \n");
        $rowCount = $rows->count();
        foreach ($rows as $index => $row) {
            $values = array_values((array)$row);
            $escapedValues = array_map(function($v) {
                if ($v === null) return 'NULL';
                return "'" . addslashes($v) . "'";
            }, $values);
            
            fprintf($handle, "(" . implode(", ", $escapedValues) . ")");
            if ($index < $rowCount - 1) {
                fprintf($handle, ",\n");
            } else {
                fprintf($handle, ";\n\n");
            }
        }
    }
}

fprintf($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($handle);

echo "Respaldo completo generado en: backup_antes_de_limpieza.sql\n";
