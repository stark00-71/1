<?php
require 'config/database.php';
$stmt = $pdo->query('SHOW CREATE TABLE users');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($row);
