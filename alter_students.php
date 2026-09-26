<?php
require_once 'config/database.php';
try {
    $pdo->exec("ALTER TABLE students ADD COLUMN father_name VARCHAR(100) NULL AFTER state, ADD COLUMN mother_name VARCHAR(100) NULL AFTER father_name;");
    echo "Columns added successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
