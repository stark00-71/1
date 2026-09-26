<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'View Student';
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT s.*, c.name as class_name, sec.name as section_name, u.email FROM students s LEFT JOIN classes c ON s.class_id = c.id LEFT JOIN sections sec ON s.section_id = sec.id LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();
if (!$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">View Student</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <p><strong>Name:</strong> <?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($record['email']) ?></p>
        <p><strong>Admission No:</strong> <?= htmlspecialchars($record['admission_no']) ?></p>
        <p><strong>Roll No:</strong> <?= htmlspecialchars($record['roll_number']) ?></p>
        <p><strong>Class:</strong> <?= htmlspecialchars($record['class_name']) ?></p>
        <p><strong>Section:</strong> <?= htmlspecialchars($record['section_name']) ?></p>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
