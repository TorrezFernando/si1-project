<?php
// Script to fix double UTF-8 encoding inside the SQL dump file.

$sqlFile = dirname(__DIR__) . '/database/scripts/colegio_mysql_limpio_v3.sql';

if (!file_exists($sqlFile)) {
    die("SQL file not found at: {$sqlFile}\n");
}

echo "Reading SQL file: {$sqlFile}\n";
$content = file_get_contents($sqlFile);

// Check if it contains typical double-encoded patterns like 'GestiÃ³n'
if (strpos($content, 'GestiÃ³n') !== false || strpos($content, 'LÃ³pez') !== false) {
    echo "Double encoding patterns detected. Fixing...\n";
    
    // Convert from UTF-8 to ISO-8859-1 to restore the raw bytes of the original string,
    // then interpret those raw bytes as a clean UTF-8 string.
    $fixedContent = mb_convert_encoding($content, 'ISO-8859-1', 'UTF-8');
    
    // Verify if the conversion was successful by checking if 'Gestión' now exists
    if (strpos($fixedContent, 'Gestión') !== false || strpos($fixedContent, 'López') !== false) {
        file_put_to_file($sqlFile, $fixedContent); // Oops, file_put_contents
        echo "SQL file fixed and saved successfully!\n";
    } else {
        // Safe fallback in case mb_convert_encoding behaves differently:
        // Use utf8_decode
        $fixedContent = utf8_decode($content);
        if (strpos($fixedContent, 'Gestión') !== false || strpos($fixedContent, 'López') !== false) {
            file_put_contents($sqlFile, $fixedContent);
            echo "SQL file fixed using utf8_decode and saved successfully!\n";
        } else {
            echo "Warning: Conversion check failed (could not find 'Gestión' or 'López' after conversion). File was NOT modified.\n";
        }
    }
} else {
    echo "No double encoding patterns found in the SQL file. It might already be clean.\n";
}

function file_put_to_file($file, $data) {
    file_put_contents($file, $data);
}
