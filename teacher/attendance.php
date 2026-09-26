<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');

$page_title = 'Attendance';

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
$sections = $pdo->query("SELECT * FROM sections ORDER BY name")->fetchAll();

$class_id = $_GET['class_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');

$students = [];
$existing_attendance = [];

// Handle POST request to save attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    $post_class_id = $_POST['class_id'];
    $post_section_id = $_POST['section_id'];
    $post_date = $_POST['date'];
    $attendance_data = $_POST['attendance'] ?? [];
    
    try {
        $pdo->beginTransaction();
        
        // Loop through submitted attendance and insert/update
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, class_id, section_id, date, status, marked_by) 
                               VALUES (?, ?, ?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by)");
                               
        foreach ($attendance_data as $student_id => $status) {
            $stmt->execute([
                $student_id,
                $post_class_id,
                $post_section_id,
                $post_date,
                $status,
                $_SESSION['user_id']
            ]);
        }
        
        $pdo->commit();
        set_message('success', 'Attendance saved successfully.');
        
        // Redirect back to GET request to show updated data
        header("Location: attendance.php?class_id=$post_class_id&section_id=$post_section_id&date=$post_date");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_message('error', 'Error saving attendance: ' . $e->getMessage());
    }
}

// Fetch students and existing attendance if form submitted via GET
if ($class_id && $section_id && $date) {
    // Fetch students
    $stmt = $pdo->prepare("SELECT id, roll_number, first_name, last_name FROM students WHERE class_id = ? AND section_id = ? AND status = 'active' ORDER BY roll_number, first_name");
    $stmt->execute([$class_id, $section_id]);
    $students = $stmt->fetchAll();
    
    // Fetch existing attendance for the selected date
    $stmt = $pdo->prepare("SELECT student_id, status FROM attendance WHERE class_id = ? AND section_id = ? AND date = ?");
    $stmt->execute([$class_id, $section_id, $date]);
    $records = $stmt->fetchAll();
    
    foreach ($records as $record) {
        $existing_attendance[$record['student_id']] = $record['status'];
    }
}

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Mark Attendance</h1>
    <a href="attendance_report.php" class="btn btn-sm btn-info shadow-sm">
        <i class="fas fa-history fa-sm text-white-50"></i> View Previous Attendance
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Select Class</h6>
    </div>
    <div class="card-body">
        <form class="row g-3" method="GET" action="attendance.php">
            <div class="col-md-3">
                <label>Class</label>
                <select class="form-select" name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= $class['id'] == $class_id ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Section</label>
                <select class="form-select" name="section_id" required>
                    <option value="">Select Section</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?= $section['id'] ?>" <?= $section['id'] == $section_id ? 'selected' : '' ?>><?= htmlspecialchars($section['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Fetch Students</button>
            </div>
        </form>
    </div>
</div>

<?php if ($class_id && $section_id && $date): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Attendance Sheet (<?= htmlspecialchars(date('M d, Y', strtotime($date))) ?>)</h6>
        </div>
        <div class="card-body">
            <?php if (empty($students)): ?>
                <div class="alert alert-warning">No students found for this class and section.</div>
            <?php else: ?>
                <form method="POST" action="attendance.php">
                    <input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>">
                    <input type="hidden" name="section_id" value="<?= htmlspecialchars($section_id) ?>">
                    <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): 
                                    $status = $existing_attendance[$student['id']] ?? 'Present'; // Default to Present
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['roll_number'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="attendance[<?= $student['id'] ?>]" value="Present" id="present_<?= $student['id'] ?>" <?= $status == 'Present' ? 'checked' : '' ?>>
                                                <label class="form-check-label text-success" for="present_<?= $student['id'] ?>">Present</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="attendance[<?= $student['id'] ?>]" value="Absent" id="absent_<?= $student['id'] ?>" <?= $status == 'Absent' ? 'checked' : '' ?>>
                                                <label class="form-check-label text-danger" for="absent_<?= $student['id'] ?>">Absent</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="attendance[<?= $student['id'] ?>]" value="Late" id="late_<?= $student['id'] ?>" <?= $status == 'Late' ? 'checked' : '' ?>>
                                                <label class="form-check-label text-warning" for="late_<?= $student['id'] ?>">Late</label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="submit_attendance" class="btn btn-success"><i class="fas fa-save me-2"></i>Save Attendance</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">Select a class and section to mark attendance.</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>