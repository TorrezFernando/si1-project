<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=colegio_db', 'root', '');
    
    echo "=== beca table ===\n";
    $r = $pdo->query("SHOW TABLES LIKE 'beca'");
    $exists = $r->fetchAll();
    if ($exists) {
        echo "Table exists\n";
        $r = $pdo->query("DESCRIBE beca");
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
            echo "  {$row['Field']} - {$row['Type']}\n";
        }
        $r = $pdo->query("SELECT * FROM beca");
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
            echo "  Data: " . json_encode($row) . "\n";
        }
    } else {
        echo "Table does NOT exist\n";
    }
    
    echo "\n=== asistencia table ===\n";
    $r = $pdo->query("SHOW TABLES LIKE 'asistencia'");
    $exists = $r->fetchAll();
    if ($exists) {
        echo "Table exists\n";
        $r = $pdo->query("DESCRIBE asistencia");
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
            echo "  {$row['Field']} - {$row['Type']}\n";
        }
    } else {
        echo "Table does NOT exist\n";
    }
    
    echo "\n=== matricula table ===\n";
    $r = $pdo->query("SHOW TABLES LIKE 'matricula'");
    $exists = $r->fetchAll();
    if ($exists) {
        $r = $pdo->query("DESCRIBE matricula");
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
            echo "  {$row['Field']} - {$row['Type']}\n";
        }
    }
    
    echo "\n=== pago_mensual table ===\n";
    $r = $pdo->query("SHOW TABLES LIKE 'pago_mensual'");
    $exists = $r->fetchAll();
    if ($exists) {
        $r = $pdo->query("DESCRIBE pago_mensual");
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
            echo "  {$row['Field']} - {$row['Type']}\n";
        }
    }
    
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
