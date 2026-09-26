<?php
require 'config/database.php';
try {
    $pdo->beginTransaction();
    $pdo->exec("INSERT INTO users (username, password_hash, email, role) VALUES ('testadmin', 'test', 'test@test.com', 'admin')");
    $uid = $pdo->lastInsertId();
    $pdo->exec("INSERT INTO admins (user_id, first_name, last_name, phone) VALUES ($uid, 'Test', 'Admin', '1234')");
    $pdo->commit();
    echo 'SUCCESS';
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>
