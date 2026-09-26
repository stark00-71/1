<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$id = $_GET['id'] ?? 0;
if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM parents WHERE id = ?");
        $stmt->execute([$id]);
        set_message('success', 'Parent deleted successfully.');
    } catch (PDOException $e) {
        set_message('error', 'Cannot delete this record because it is referenced elsewhere.');
    }
}
redirect('index.php');
