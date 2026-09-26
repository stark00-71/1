<?php
require 'config/database.php';
$stmt = $pdo->query('DESCRIBE settings');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
