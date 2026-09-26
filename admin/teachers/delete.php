<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$id = $_GET['id'] ?? 0;
if ($id) {
    try {
        $pdo->beginTransaction();
        
        // Get user_id
        $stmt = $pdo->prepare("SELECT user_id FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $teacher = $stmt->fetch();
        
        if ($teacher) {
            // Delete teacher record
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete associated user record
            if ($teacher['user_id']) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$teacher['user_id']]);
            }
        }
        
        $pdo->commit();
        set_message('success', 'Teacher deleted successfully.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_message('error', 'Cannot delete this record because it is referenced elsewhere.');
    }
}
redirect('index.php');
