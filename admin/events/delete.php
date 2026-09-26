<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$id = $_GET['id'] ?? 0;
if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        set_message('success', 'Event deleted successfully.');
    } catch (PDOException $e) {
        set_message('error', 'Error deleting event.');
    }
}
redirect('index.php');
