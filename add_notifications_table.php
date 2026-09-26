<?php
require_once 'config/database.php';

$sql = "
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
";

try {
    $pdo->exec($sql);
    echo "Notifications table created successfully.\n";

    // Clear existing dummy data if script is run multiple times
    $pdo->exec("TRUNCATE TABLE notifications");

    // Insert dummy notifications for admin (id 1), teacher (id 2), student (id 3)
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
    
    // Admin dummy
    $stmt->execute([1, 'System Update', 'The system will undergo maintenance tonight at 2 AM.', '']);
    $stmt->execute([1, 'New Leave Request', 'Teacher John Doe has submitted a new leave request.', BASE_URL . '/admin/leaves/']);
    
    // Teacher dummy
    $stmt->execute([2, 'Welcome', 'Welcome to the SCM portal!', '']);
    $stmt->execute([2, 'Timetable Updated', 'Your timetable for the new semester has been updated.', BASE_URL . '/teacher/timetable.php']);
    
    // Student dummy
    $stmt->execute([3, 'Fee Due Reminder', 'Your first term fee is due in 7 days.', BASE_URL . '/student/fees.php']);
    $stmt->execute([3, 'New Assignment', 'A new assignment for Mathematics has been posted.', BASE_URL . '/student/assignments.php']);
    
    echo "Dummy notifications inserted successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
