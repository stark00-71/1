<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Class Results';

// Fetch exams for dropdown
$exams = $pdo->query("SELECT e.id, e.name, c.name as class_name FROM exams e JOIN classes c ON e.class_id = c.id ORDER BY e.id DESC")->fetchAll();

$selected_exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$students = [];

if ($selected_exam_id > 0) {
    // Get the class_id for the selected exam
    $examStmt = $pdo->prepare("SELECT class_id FROM exams WHERE id = ?");
    $examStmt->execute([$selected_exam_id]);
    $exam = $examStmt->fetch();

    if ($exam) {
        // Fetch all students for this class
        $studentStmt = $pdo->prepare("SELECT id, first_name, last_name, admission_no FROM students WHERE class_id = ? ORDER BY first_name ASC");
        $studentStmt->execute([$exam['class_id']]);
        $students = $studentStmt->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = (int)($_POST['exam_id'] ?? 0);
    $results = $_POST['results'] ?? [];

    if ($exam_id > 0 && !empty($results)) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO exam_results (exam_id, student_id, marks_obtained, grade, remarks) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), grade = VALUES(grade), remarks = VALUES(remarks)");
            
            foreach ($results as $student_id => $data) {
                // Only insert if marks are provided
                if (isset($data['marks_obtained']) && $data['marks_obtained'] !== '') {
                    $marks_obtained = (float)$data['marks_obtained'];
                    $grade = sanitize($data['grade'] ?? '');
                    $remarks = sanitize($data['remarks'] ?? '');
                    $stmt->execute([$exam_id, $student_id, $marks_obtained, $grade, $remarks]);
                }
            }
            $pdo->commit();
            set_message('success', 'Results saved successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            set_message('error', 'Database error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Invalid request.');
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Class Results</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-6 mb-3">
                    <label>Select Exam <span class="text-danger">*</span></label>
                    <select name="exam_id" class="form-control" required onchange="this.form.submit()">
                        <option value="">Select Exam</option>
                        <?php foreach($exams as $exam): ?>
                            <option value="<?= $exam['id'] ?>" <?= $selected_exam_id == $exam['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($exam['name']) ?> (<?= htmlspecialchars($exam['class_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <button type="submit" class="btn btn-info">Load Students</button>
                </div>
            </div>
        </form>

        <?php if ($selected_exam_id > 0): ?>
            <?php if (empty($students)): ?>
                <div class="alert alert-warning">No students found for this exam's class.</div>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="exam_id" value="<?= $selected_exam_id ?>">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>Marks Obtained <span class="text-danger">*</span></th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['admission_no']) ?></td>
                                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                        <td>
                                            <input type="number" step="0.01" name="results[<?= $student['id'] ?>][marks_obtained]" class="form-control" placeholder="Marks" required>
                                        </td>
                                        <td>
                                            <input type="text" name="results[<?= $student['id'] ?>][grade]" class="form-control" placeholder="Grade">
                                        </td>
                                        <td>
                                            <input type="text" name="results[<?= $student['id'] ?>][remarks]" class="form-control" placeholder="Remarks">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save All Results</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
