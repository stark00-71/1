<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if role is assigned to any admins
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE role_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        set_message('danger', 'Cannot delete role because it is assigned to ' . $count . ' admin(s). Please reassign them first.');
    } else {
        $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
        if ($stmt->execute([$id])) {
            set_message('success', 'Role deleted successfully.');
        } else {
            set_message('danger', 'Failed to delete role.');
        }
    }
}
redirect('index.php');
