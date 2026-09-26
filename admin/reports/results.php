<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$stmt = $pdo->query("SELECT er.*, e.name as exam_name, e.exam_date, s.first_name, s.last_name, s.admission_no, c.name as class_name 
                     FROM exam_results er 
                     JOIN exams e ON er.exam_id = e.id 
                     JOIN students s ON er.student_id = s.id 
                     JOIN classes c ON e.class_id = c.id 
                     ORDER BY er.created_at DESC LIMIT 100");
$records = $stmt->fetchAll();

$page_title = 'Exam Results Report';
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Exam Results Report</h1>
    <a href="index.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Reports
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Recent 100 Exam Results</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Class</th>
                        <th>Student Name</th>
                        <th>Admission No</th>
                        <th>Marks Obtained</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['exam_name']) ?></td>
                        <td><?= htmlspecialchars($row['class_name']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['admission_no']) ?></td>
                        <td><?= htmlspecialchars($row['marks_obtained']) ?></td>
                        <?php
                            $grade = strtoupper(trim($row['grade']));
                            $grade_class = 'text-primary font-weight-bold'; // Default
                            if (strpos($grade, 'A') !== false) {
                                $grade_class = 'text-success font-weight-bold';
                            } elseif (strpos($grade, 'B') !== false) {
                                $grade_class = 'text-primary font-weight-bold';
                            } else {
                                // For C, D, E, F, FAIL or any other lower grade, make it red
                                $grade_class = 'text-danger font-weight-bold';
                            }
                        ?>
                        <td class="<?= $grade_class ?>">
                            <?= htmlspecialchars($row['grade']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($records)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No exam results found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
