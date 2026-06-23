<?php
// Script to fix double UTF-8 encoding in Colegio DB tables.

// Parse .env to get database credentials
$envFile = dirname(__DIR__) . '/.env';
$dbHost = '127.0.0.1';
$dbName = 'colegio_db';
$dbUser = 'root';
$dbPass = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = array_pad(explode('=', $line, 2), 2, null);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($name === 'DB_HOST') $dbHost = $value;
        if ($name === 'DB_DATABASE') $dbName = $value;
        if ($name === 'DB_USERNAME') $dbUser = $value;
        if ($name === 'DB_PASSWORD') $dbPass = $value;
    }
}

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Connected successfully to {$dbName} on {$dbHost}.\n";

    // Get all tables in the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $totalUpdated = 0;

    foreach ($tables as $table) {
        // Get all text/varchar/char columns for this table
        $colStmt = $pdo->prepare("
            SELECT COLUMN_NAME, DATA_TYPE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = :dbName 
              AND TABLE_NAME = :tableName 
              AND DATA_TYPE IN ('varchar', 'text', 'char', 'mediumtext', 'longtext', 'tinytext')
        ");
        $colStmt->execute(['dbName' => $dbName, 'tableName' => $table]);
        $columns = $colStmt->fetchAll();

        if (empty($columns)) continue;

        echo "Processing table `{$table}`:\n";

        foreach ($columns as $columnData) {
            $column = $columnData['COLUMN_NAME'];
            
            // Fix double encoding
            // Query filters rows containing Ã or Â which are typical of double-encoded UTF-8 in Latin1
            try {
                // First, check how many rows match
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` LIKE '%Ã%' OR `{$column}` LIKE '%Â%'");
                $checkStmt->execute();
                $count = $checkStmt->fetchColumn();

                if ($count > 0) {
                    $updateSql = "
                        UPDATE `{$table}` 
                        SET `{$column}` = CONVERT(CAST(CONVERT(`{$column}` USING latin1) AS BINARY) USING utf8mb4)
                        WHERE `{$column}` LIKE '%Ã%' OR `{$column}` LIKE '%Â%'
                    ";
                    $rowsAffected = $pdo->exec($updateSql);
                    echo "  - Column `{$column}`: updated {$rowsAffected} rows.\n";
                    $totalUpdated += $rowsAffected;
                }
            } catch (Exception $colEx) {
                echo "  - Column `{$column}`: Error: " . $colEx->getMessage() . "\n";
            }
        }
    }

    echo "\nEncoding fix completed. Total fields/rows corrected: {$totalUpdated}.\n";

} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
