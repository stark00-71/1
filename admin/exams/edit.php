<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Edit Exam';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    set_message('error', 'Invalid exam ID.');
    redirect('index.php');
}

// Fetch existing record
$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    set_message('error', 'Exam not found.');
    redirect('index.php');
}

// Fetch classes and subjects for dropdowns
$classes = $pdo->query("SELECT id, name FROM classes ORDER BY name ASC")->fetchAll();
$subjects = $pdo->query("SELECT id, name FROM subjects ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $type = sanitize($_POST['type'] ?? '');
    $class_id = (int)($_POST['class_id'] ?? 0);
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $exam_date = sanitize($_POST['exam_date'] ?? '');
    $start_time = sanitize($_POST['start_time'] ?? '');
    $end_time = sanitize($_POST['end_time'] ?? '');
    $max_marks = (int)($_POST['max_marks'] ?? 0);
    $passing_marks = (int)($_POST['passing_marks'] ?? 0);

    if (!empty($name) && $class_id > 0 && $subject_id > 0 && $max_marks > 0 && $passing_marks > 0) {
        $stmt = $pdo->prepare("UPDATE exams SET name = ?, type = ?, class_id = ?, subject_id = ?, exam_date = ?, start_time = ?, end_time = ?, max_marks = ?, passing_marks = ? WHERE id = ?");
        try {
            $stmt->execute([$name, $type, $class_id, $subject_id, $exam_date, $start_time, $end_time, $max_marks, $passing_marks, $id]);
            set_message('success', 'Exam updated successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Database error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Please fill all required fields correctly.');
    }
}
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Exam</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Exam Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($record['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Exam Type</label>
                    <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($record['type'] ?? '') ?>" placeholder="e.g. Midterm, Final">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        <?php foreach($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= $class['id'] == $record['class_id'] ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Subject <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Select Subject</option>
                        <?php foreach($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>" <?= $subject['id'] == $record['subject_id'] ? 'selected' : '' ?>><?= htmlspecialchars($subject['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Exam Date</label>
                    <input type="date" name="exam_date" class="form-control" value="<?= htmlspecialchars($record['exam_date'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" value="<?= htmlspecialchars($record['start_time'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control" value="<?= htmlspecialchars($record['end_time'] ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Max Marks <span class="text-danger">*</span></label>
                    <input type="number" name="max_marks" class="form-control" min="1" value="<?= htmlspecialchars($record['max_marks'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Passing Marks <span class="text-danger">*</span></label>
                    <input type="number" name="passing_marks" class="form-control" min="1" value="<?= htmlspecialchars($record['passing_marks'] ?? '') ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Exam</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
