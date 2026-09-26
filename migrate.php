<?php
require_once 'config/database.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;");
    echo "Migration successful.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "Migration failed: " . $e->getMessage();
    }
}
