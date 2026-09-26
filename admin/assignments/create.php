<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Assignment';

// Fetch classes, sections, subjects, teachers for dropdowns
$classes = $pdo->query("SELECT id, name FROM classes ORDER BY name ASC")->fetchAll();
$sections = $pdo->query("SELECT id, name, class_id FROM sections ORDER BY name ASC")->fetchAll();
$subjects = $pdo->query("SELECT id, name FROM subjects ORDER BY name ASC")->fetchAll();
$teachers = $pdo->query("SELECT id, first_name, last_name FROM teachers ORDER BY first_name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_message('error', 'Invalid form submission. Please try again.');
        redirect('create.php');
    }

    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $class_id = (int)($_POST['class_id'] ?? 0);
    $section_id_input = $_POST['section_id'] ?? '';
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $teacher_id_input = $_POST['teacher_id'] ?? '';
    $teacher_id = ($teacher_id_input === 'none' || $teacher_id_input === '0' || $teacher_id_input === '') ? null : (int)$teacher_id_input;
    $due_date = sanitize($_POST['due_date'] ?? '');

    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/assignments/';
        $upload_result = handle_secure_upload('attachment', $upload_dir);
        if ($upload_result['success']) {
            $attachment = $upload_result['filename'];
        } else {
            set_message('error', $upload_result['error']);
            redirect('create.php');
        }
    }

    if (!empty($title) && $class_id > 0 && !empty($section_id_input) && $subject_id > 0) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO assignments (title, description, class_id, section_id, subject_id, teacher_id, due_date, attachment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($section_id_input === 'all') {
                $secs = $pdo->prepare("SELECT id FROM sections WHERE class_id = ?");
                $secs->execute([$class_id]);
                $class_sections = $secs->fetchAll();
                
                foreach ($class_sections as $sec) {
                    $stmt->execute([$title, $description, $class_id, $sec['id'], $subject_id, $teacher_id, $due_date, $attachment]);
                }
            } else {
                $section_id = (int)$section_id_input;
                $stmt->execute([$title, $description, $class_id, $section_id, $subject_id, $teacher_id, $due_date, $attachment]);
            }
            
            $pdo->commit();
            set_message('success', 'Assignment added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            set_message('error', 'Database error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Please fill all required fields correctly.');
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Assignment</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="mb-3">
                <label>Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        <?php foreach($classes as $class): ?>
                            <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Section <span class="text-danger">*</span></label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                        <option value="all">All Sections (for this class)</option>
                        <?php foreach($sections as $section): ?>
                            <option value="<?= $section['id'] ?>"><?= htmlspecialchars($section['name']) ?></option>
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
                            <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Teacher</label>
                    <select name="teacher_id" class="form-control">
                        <option value="">Select Teacher</option>
                        <option value="none">None</option>
                        <?php foreach($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control">
            </div>
            <div class="mb-3">
                <label>Attachment</label>
                <input type="file" name="attachment" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Save Assignment</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
