<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Mark Attendance';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
$date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = (int)$_POST['class_id'];
    $section_id = (int)$_POST['section_id'];
    $date = sanitize($_POST['date']);
    $attendance = $_POST['attendance'] ?? [];
    $marked_by = $_SESSION['user_id'];
    
    try {
        $pdo->beginTransaction();
        foreach ($attendance as $student_id => $status) {
            $stmt = $pdo->prepare("INSERT INTO attendance (student_id, class_id, section_id, date, status, marked_by) VALUES (?, ?, ?, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by)");
            $stmt->execute([$student_id, $class_id, $section_id, $date, $status, $marked_by]);
        }
        $pdo->commit();
        set_message('success', 'Attendance saved successfully.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_message('error', 'Error saving attendance.');
    }
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
$sections = [];
if ($class_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM sections WHERE class_id = ? ORDER BY name");
    $stmt->execute([$class_id]);
    $sections = $stmt->fetchAll();
}

$students = [];
$existing_attendance = [];
if ($class_id > 0 && $section_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? AND section_id = ? ORDER BY first_name, last_name");
    $stmt->execute([$class_id, $section_id]);
    $students = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT student_id, status FROM attendance WHERE class_id = ? AND section_id = ? AND date = ?");
    $stmt->execute([$class_id, $section_id, $date]);
    foreach ($stmt->fetchAll() as $row) {
        $existing_attendance[$row['student_id']] = $row['status'];
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Mark Attendance</h1>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row mb-4">
            <div class="col-md-3">
                <label>Class</label>
                <select name="class_id" class="form-control" onchange="this.form.submit()" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= $class['id'] == $class_id ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Section</label>
                <select name="section_id" class="form-control" onchange="this.form.submit()" required>
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
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Load Students</button>
            </div>
        </form>

        <?php if (!empty($students)): ?>
        <hr>
        <form method="POST" action="">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            <input type="hidden" name="section_id" value="<?= $section_id ?>">
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
                            $status = $existing_attendance[$student['id']] ?? 'Present';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($student['roll_number']) ?></td>
                            <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="attendance[<?= $student['id'] ?>]" value="Present" <?= $status == 'Present' ? 'checked' : '' ?>>
                                    <label class="form-check-label text-success">Present</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="attendance[<?= $student['id'] ?>]" value="Absent" <?= $status == 'Absent' ? 'checked' : '' ?>>
                                    <label class="form-check-label text-danger">Absent</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="attendance[<?= $student['id'] ?>]" value="Late" <?= $status == 'Late' ? 'checked' : '' ?>>
                                    <label class="form-check-label text-warning">Late</label>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success mt-3">Save Attendance</button>
        </form>
        <?php elseif ($class_id > 0 && $section_id > 0): ?>
            <p class="text-center">No students found in this section.</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
