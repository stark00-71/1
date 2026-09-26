<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $pdo->beginTransaction();
        
        // Get user_id for this admin
        $stmt = $pdo->prepare("SELECT user_id FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            // Prevent self-deletion
            if ($admin['user_id'] == $_SESSION['user_id']) {
                throw new Exception("You cannot delete your own admin account.");
            }

            // Prevent deleting the Super Admin
            if ($admin['user_id'] == 1 && $_SESSION['user_id'] != 1) {
                throw new Exception("Access Denied: You cannot delete the Super Admin.");
            }
            
            // Delete admin record
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete user record
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
            $stmt->execute([$admin['user_id']]);
            
            $pdo->commit();
            set_message('success', 'Admin deleted successfully.');
        } else {
            $pdo->rollBack();
            set_message('danger', 'Admin not found.');
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        set_message('danger', 'Error deleting admin: ' . $e->getMessage());
    }
}

redirect('index.php');
?>
