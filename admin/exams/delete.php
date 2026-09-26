<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
        $stmt->execute([$id]);
        set_message('success', 'Exam deleted successfully.');
    } catch (PDOException $e) {
        set_message('error', 'Database error: ' . $e->getMessage());
    }
} else {
    set_message('error', 'Invalid exam ID.');
}

redirect('index.php');
