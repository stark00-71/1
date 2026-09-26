<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Timetable';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
$sections = [];
if ($class_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM sections WHERE class_id = ? ORDER BY name");
    $stmt->execute([$class_id]);
    $sections = $stmt->fetchAll();
}

$timetable = [];
if ($class_id > 0 && $section_id > 0) {
    $stmt = $pdo->prepare("
        SELECT t.*, s.name as subject_name, te.first_name, te.last_name 
        FROM timetable t 
        JOIN subjects s ON t.subject_id = s.id 
        JOIN teachers te ON t.teacher_id = te.id 
        WHERE t.class_id = ? AND t.section_id = ? 
        ORDER BY FIELD(t.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), t.start_time
    ");
    $stmt->execute([$class_id, $section_id]);
    $results = $stmt->fetchAll();
    
    foreach ($results as $row) {
        $timetable[$row['day']][] = $row;
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Timetable</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add Timetable Entry
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row mb-4">
            <div class="col-md-4">
                <label>Class</label>
                <select name="class_id" class="form-control" onchange="this.form.submit()" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= $class['id'] == $class_id ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Section</label>
                <select name="section_id" class="form-control" onchange="this.form.submit()" required>
                    <option value="">Select Section</option>
                    <?php foreach ($sections as $section): ?>
                    <option value="<?= $section['id'] ?>" <?= $section['id'] == $section_id ? 'selected' : '' ?>><?= htmlspecialchars($section['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Load Timetable</button>
            </div>
        </form>

        <?php if ($class_id > 0 && $section_id > 0): ?>
            <?php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; ?>
            <?php foreach ($days as $day): ?>
                <h5 class="bg-light p-2 mt-4"><?= $day ?></h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($timetable[$day])): ?>
                                <?php foreach ($timetable[$day] as $entry): ?>
                                <tr>
                                    <td><?= date('h:i A', strtotime($entry['start_time'])) ?> - <?= date('h:i A', strtotime($entry['end_time'])) ?></td>
                                    <td><?= htmlspecialchars($entry['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']) ?></td>
                                    <td>
                                        <a href="delete.php?id=<?= $entry['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this entry?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No classes scheduled.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
