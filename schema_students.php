<?php
require_once 'config/database.php';
$stmt = $pdo->query("DESCRIBE students");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($result);
