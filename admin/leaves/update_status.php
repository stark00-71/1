<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    
    if (in_array($status, ['Approved', 'Rejected'])) {
        try {
            $stmt = $pdo->prepare("UPDATE leaves SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            
            header("Location: index.php?msg=Leave status updated successfully");
            exit;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}

header("Location: index.php");
exit;
