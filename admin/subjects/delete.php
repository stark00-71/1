<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$id]);
        set_message('success', 'Subject deleted successfully.');
    } catch (PDOException $e) {
        set_message('error', 'Cannot delete subject. It may be in use.');
    }
}
redirect('index.php');
