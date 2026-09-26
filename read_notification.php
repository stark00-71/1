<?php
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'config/auth.php';

if (!isset($_SESSION['user_id'])) {
    redirect(BASE_URL . '/auth/login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

if ($id > 0) {
    // Check if the notification belongs to the user
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $notification = $stmt->fetch();

    if ($notification) {
        // Mark as read
        $update_stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $update_stmt->execute([$id]);

        // Redirect to the link if it exists, otherwise to the notifications page or dashboard
        if (!empty($notification['link'])) {
            redirect($notification['link']);
        }
    }
}

// Fallback redirect based on role
if ($_SESSION['user_role'] == 'admin') {
    redirect(BASE_URL . '/admin/dashboard.php');
} elseif ($_SESSION['user_role'] == 'teacher') {
    redirect(BASE_URL . '/teacher/dashboard.php');
} else {
    redirect(BASE_URL . '/student/dashboard.php');
}
?>
