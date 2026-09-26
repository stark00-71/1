<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');

$user_id = $_SESSION['user_id'];

// Get student's class
$stmt = $pdo->prepare("SELECT class_id FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();
$class_id = $student['class_id'] ?? 0;

// Fetch upcoming exams
$stmt = $pdo->prepare("SELECT e.*, s.name as subject_name 
                       FROM exams e 
                       JOIN subjects s ON e.subject_id = s.id 
                       WHERE e.class_id = ? 
                       ORDER BY e.exam_date ASC, e.start_time ASC");
$stmt->execute([$class_id]);
$exams = $stmt->fetchAll();

$page_title = 'My Exams';
include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Exams</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Exam Schedule</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Max Marks</th>
                        <th>Passing Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= htmlspecialchars($exam['name']) ?></td>
                        <td><?= htmlspecialchars($exam['type']) ?></td>
                        <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($exam['exam_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($exam['start_time'])) ?> - <?= date('h:i A', strtotime($exam['end_time'])) ?></td>
                        <td><?= htmlspecialchars($exam['max_marks']) ?></td>
                        <td><?= htmlspecialchars($exam['passing_marks']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($exams)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No upcoming exams scheduled.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
