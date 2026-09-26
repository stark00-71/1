<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');

$user_id = $_SESSION['user_id'];

// Get teacher ID
$stmt = $pdo->prepare("SELECT id FROM teachers WHERE user_id = ?");
$stmt->execute([$user_id]);
$teacher = $stmt->fetch();
$teacher_id = $teacher['id'] ?? 0;

// Fetch exams 
$stmt = $pdo->prepare("
    SELECT e.*, c.name as class_name, s.name as subject_name 
    FROM exams e 
    JOIN classes c ON e.class_id = c.id 
    JOIN subjects s ON e.subject_id = s.id 
    ORDER BY e.exam_date ASC, e.start_time ASC
");
$stmt->execute();
$exams = $stmt->fetchAll();

$page_title = 'Exam Management';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Exams</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Exam Name</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= htmlspecialchars($exam['name']) ?></td>
                        <td><?= htmlspecialchars($exam['class_name']) ?></td>
                        <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($exam['exam_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($exam['start_time'])) ?> - <?= date('h:i A', strtotime($exam['end_time'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($exams)): ?>
                    <tr><td colspan="5" class="text-center">No upcoming exams found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>