<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My Assignments';

$student_id = $_SESSION['student_id'];

// Get student's class and section
$stmt = $pdo->prepare("SELECT class_id, section_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student_info = $stmt->fetch();

$assignments = [];
if ($student_info) {
    $class_id = $student_info['class_id'];
    $section_id = $student_info['section_id'];

    $stmt = $pdo->prepare("
        SELECT a.*, s.name as subject_name, sub.status as submission_status 
        FROM assignments a
        LEFT JOIN subjects s ON a.subject_id = s.id
        LEFT JOIN assignment_submissions sub ON a.id = sub.assignment_id AND sub.student_id = ?
        WHERE a.class_id = ? AND a.section_id = ?
        ORDER BY a.due_date DESC
    ");
    $stmt->execute([$student_id, $class_id, $section_id]);
    $assignments = $stmt->fetchAll();
}

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Assignments</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr><td colspan="5" class="text-center">No upcoming assignments found.</td></tr>
                    <?php else: ?>
                        <?php foreach($assignments as $assignment): ?>
                            <tr>
                                <td><?= htmlspecialchars($assignment['title']) ?></td>
                                <td><?= htmlspecialchars($assignment['subject_name']) ?></td>
                                <td><?= empty($assignment['due_date']) ? 'N/A' : date('M d, Y', strtotime($assignment['due_date'])) ?></td>
                                <td>
                                    <?php if ($assignment['submission_status'] === 'Submitted'): ?>
                                        <span class="badge bg-info">Submitted</span>
                                    <?php elseif ($assignment['submission_status'] === 'Graded'): ?>
                                        <span class="badge bg-success">Graded</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="view_assignment.php?id=<?= $assignment['id'] ?>" class="btn btn-sm btn-primary">View / Submit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>