<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');

$assignment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($assignment_id <= 0) {
    redirect('assignments.php');
}

$student_id = $_SESSION['student_id'];

// Get student's class and section
$stmt = $pdo->prepare("SELECT class_id, section_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student_info = $stmt->fetch();

if (!$student_info) {
    redirect('assignments.php');
}

$class_id = $student_info['class_id'];
$section_id = $student_info['section_id'];

// Fetch Assignment
$stmt = $pdo->prepare("
    SELECT a.*, s.name as subject_name 
    FROM assignments a
    LEFT JOIN subjects s ON a.subject_id = s.id
    WHERE a.id = ? AND a.class_id = ? AND a.section_id = ?
");
$stmt->execute([$assignment_id, $class_id, $section_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    set_message('error', 'Assignment not found or you do not have permission to view it.');
    redirect('assignments.php');
}

// Fetch Submission
$stmt = $pdo->prepare("SELECT * FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?");
$stmt->execute([$assignment_id, $student_id]);
$submission = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$submission) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_message('error', 'Invalid form submission. Please try again.');
        redirect('view_assignment.php?id=' . $assignment_id);
    }

    $submission_text = sanitize($_POST['submission_text'] ?? '');
    
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/assignments/';
        $upload_result = handle_secure_upload('attachment', $upload_dir);
        if ($upload_result['success']) {
            $attachment = $upload_result['filename'];
        } else {
            set_message('error', $upload_result['error']);
            redirect('view_assignment.php?id=' . $assignment_id);
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, submission_text, attachment, status) VALUES (?, ?, ?, ?, 'Submitted')");
        $stmt->execute([$assignment_id, $student_id, $submission_text, $attachment]);
        set_message('success', 'Assignment submitted successfully.');
        redirect('view_assignment.php?id=' . $assignment_id);
    } catch (PDOException $e) {
        set_message('error', 'Database error: ' . $e->getMessage());
    }
}

$page_title = 'View Assignment - ' . htmlspecialchars($assignment['title']);
include '../includes/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Assignment Details</h1>
    <a href="assignments.php" class="btn btn-sm btn-secondary shadow-sm">Back to Assignments</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= htmlspecialchars($assignment['title']) ?></h6>
            </div>
            <div class="card-body">
                <p><strong>Subject:</strong> <?= htmlspecialchars($assignment['subject_name']) ?></p>
                <p><strong>Due Date:</strong> <?= empty($assignment['due_date']) ? 'N/A' : date('M d, Y', strtotime($assignment['due_date'])) ?></p>
                <hr>
                <h6><strong>Description:</strong></h6>
                <p><?= nl2br(htmlspecialchars($assignment['description'])) ?></p>
                
                <?php if (!empty($assignment['attachment'])): ?>
                    <hr>
                    <h6><strong>Attached File:</strong></h6>
                    <a href="../uploads/assignments/<?= htmlspecialchars($assignment['attachment']) ?>" class="btn btn-sm btn-info" download>Download Attachment</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Your Submission</h6>
            </div>
            <div class="card-body">
                <?php if ($submission): ?>
                    <p><strong>Status:</strong> 
                        <?php if ($submission['status'] === 'Submitted'): ?>
                            <span class="badge bg-info">Submitted</span>
                        <?php else: ?>
                            <span class="badge bg-success">Graded</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Submitted On:</strong> <?= date('M d, Y H:i', strtotime($submission['created_at'])) ?></p>
                    
                    <?php if (!empty($submission['submission_text'])): ?>
                        <h6><strong>Your Answer:</strong></h6>
                        <p><?= nl2br(htmlspecialchars($submission['submission_text'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($submission['attachment'])): ?>
                        <h6><strong>Your File:</strong></h6>
                        <a href="../uploads/assignments/<?= htmlspecialchars($submission['attachment']) ?>" class="btn btn-sm btn-outline-primary" download>Download Your File</a>
                    <?php endif; ?>

                    <?php if ($submission['status'] === 'Graded'): ?>
                        <hr>
                        <h6><strong>Marks:</strong> <?= htmlspecialchars($submission['marks_obtained']) ?></h6>
                        <?php if (!empty($submission['feedback'])): ?>
                            <h6><strong>Feedback:</strong></h6>
                            <p><?= nl2br(htmlspecialchars($submission['feedback'])) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php else: ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="mb-3">
                            <label>Submission Text (Optional)</label>
                            <textarea name="submission_text" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Attach File (Optional)</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Assignment</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
