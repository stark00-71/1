<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

// Check role dynamically
$role = basename(dirname(__DIR__));
if ($role === 'admin') requireRole('admin');
else if ($role === 'teacher') requireRole('teacher');
else if ($role === 'student') requireRole('student');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';

    if (!empty($receiver_id) && !empty($subject) && !empty($body)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES (?, ?, ?, ?)");
            $stmt->execute([$sender_id, $receiver_id, $subject, $body]);
            header("Location: index.php?msg=Message sent successfully");
            exit;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}

header("Location: index.php");
exit;
