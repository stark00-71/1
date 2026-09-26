<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$id = $_GET['id'] ?? 0;
if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
        if ($student) {
            // Delete user, and cascade will delete student
            $stmt2 = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt2->execute([$student['user_id']]);
            set_message('success', 'Student deleted successfully.');
        }
    } catch (PDOException $e) {
        set_message('error', 'Error deleting student.');
    }
}
redirect('index.php');
