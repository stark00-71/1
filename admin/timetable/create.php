<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Timetable Entry';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = (int)($_POST['class_id'] ?? 0);
    $section_id = (int)($_POST['section_id'] ?? 0);
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $teacher_id = (int)($_POST['teacher_id'] ?? 0);
    $day = sanitize($_POST['day'] ?? '');
    $start_time = sanitize($_POST['start_time'] ?? '');
    $end_time = sanitize($_POST['end_time'] ?? '');
    
    if ($class_id && $section_id && $subject_id && $teacher_id && $day && $start_time && $end_time) {
        try {
            $stmt = $pdo->prepare("INSERT INTO timetable (class_id, section_id, subject_id, teacher_id, day, period_no, start_time, end_time) VALUES (?, ?, ?, ?, ?, 1, ?, ?)");
            $stmt->execute([$class_id, $section_id, $subject_id, $teacher_id, $day, $start_time, $end_time]);
            set_message('success', 'Timetable entry added.');
            redirect('index.php?class_id=' . $class_id . '&section_id=' . $section_id);
        } catch (PDOException $e) {
            set_message('error', 'Error adding timetable entry.');
        }
    } else {
        set_message('error', 'All fields are required.');
    }
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
$sections = $pdo->query("SELECT * FROM sections ORDER BY name")->fetchAll();
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
$teachers = $pdo->query("SELECT * FROM teachers ORDER BY first_name")->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Timetable Entry</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Section</label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                        <?php foreach ($sections as $section): ?>
                        <option value="<?= $section['id'] ?>"><?= htmlspecialchars($section['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Subject</label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Teacher</label>
                    <select name="teacher_id" class="form-control" required>
                        <option value="">Select Teacher</option>
                        <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['first_name'].' '.$teacher['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Day</label>
                    <select name="day" class="form-control" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Entry</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
