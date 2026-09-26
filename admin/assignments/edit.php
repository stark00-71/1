<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Edit Assignment';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    set_message('error', 'Invalid assignment ID.');
    redirect('index.php');
}

// Fetch existing record
$stmt = $pdo->prepare("SELECT * FROM assignments WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    set_message('error', 'Assignment not found.');
    redirect('index.php');
}

// Fetch classes, sections, subjects, teachers for dropdowns
$classes = $pdo->query("SELECT id, name FROM classes ORDER BY name ASC")->fetchAll();
$sections = $pdo->query("SELECT id, name, class_id FROM sections ORDER BY name ASC")->fetchAll();
$subjects = $pdo->query("SELECT id, name FROM subjects ORDER BY name ASC")->fetchAll();
$teachers = $pdo->query("SELECT id, first_name, last_name FROM teachers ORDER BY first_name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_message('error', 'Invalid form submission. Please try again.');
        redirect('edit.php?id=' . $id);
    }

    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $class_id = (int)($_POST['class_id'] ?? 0);
    $section_id = (int)($_POST['section_id'] ?? 0);
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $teacher_id_input = $_POST['teacher_id'] ?? '';
    $teacher_id = ($teacher_id_input === 'none' || $teacher_id_input === '0' || $teacher_id_input === '') ? null : (int)$teacher_id_input;
    $due_date = sanitize($_POST['due_date'] ?? '');

    $attachment = $record['attachment'];
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/assignments/';
        $upload_result = handle_secure_upload('attachment', $upload_dir);
        if ($upload_result['success']) {
            $attachment = $upload_result['filename'];
            // remove old attachment if needed
            if (!empty($record['attachment']) && file_exists($upload_dir . $record['attachment'])) {
                unlink($upload_dir . $record['attachment']);
            }
        } else {
            set_message('error', $upload_result['error']);
            redirect('edit.php?id=' . $id);
        }
    }

    if (!empty($title) && $class_id > 0 && $section_id > 0 && $subject_id > 0) {
        $stmt = $pdo->prepare("UPDATE assignments SET title = ?, description = ?, class_id = ?, section_id = ?, subject_id = ?, teacher_id = ?, due_date = ?, attachment = ? WHERE id = ?");
        try {
            $stmt->execute([$title, $description, $class_id, $section_id, $subject_id, $teacher_id, $due_date, $attachment, $id]);
            set_message('success', 'Assignment updated successfully.');
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
    <h1 class="h3 mb-0 text-gray-800">Edit Assignment</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="mb-3">
                <label>Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($record['title'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($record['description'] ?? '') ?></textarea>
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
                    <label>Section <span class="text-danger">*</span></label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                        <?php foreach($sections as $section): ?>
                            <option value="<?= $section['id'] ?>" <?= $section['id'] == $record['section_id'] ? 'selected' : '' ?>><?= htmlspecialchars($section['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Subject <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Select Subject</option>
                        <?php foreach($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>" <?= $subject['id'] == $record['subject_id'] ? 'selected' : '' ?>><?= htmlspecialchars($subject['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Teacher</label>
                    <select name="teacher_id" class="form-control">
                        <option value="">Select Teacher</option>
                        <option value="none" <?= empty($record['teacher_id']) ? 'selected' : '' ?>>None</option>
                        <?php foreach($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" <?= $teacher['id'] == $record['teacher_id'] ? 'selected' : '' ?>><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($record['due_date'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Attachment</label>
                <?php if (!empty($record['attachment'])): ?>
                    <div class="mb-2">
                        <a href="../../uploads/assignments/<?= htmlspecialchars($record['attachment']) ?>" target="_blank">View Current Attachment</a>
                    </div>
                <?php endif; ?>
                <input type="file" name="attachment" class="form-control">
                <small class="text-muted">Leave blank to keep the current attachment.</small>
            </div>
            <button type="submit" class="btn btn-primary">Update Assignment</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
