<?php
$base_dir = 'C:\\xampp\\htdocs\\1\\teacher\\';

// 1. Profile (Fully Functional)
$profile_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'My Profile';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = sanitize($_POST['phone'] ?? '');
    $qualification = sanitize($_POST['qualification'] ?? '');
    
    if (!empty($phone)) {
        try {
            $stmt = $pdo->prepare("UPDATE teachers SET phone=?, qualification=? WHERE user_id=?");
            $stmt->execute([$phone, $qualification, $user_id]);
            $success_msg = "Profile updated successfully.";
        } catch (PDOException $e) {
            $error_msg = "Error updating profile.";
        }
    } else {
        $error_msg = "Phone number is required.";
    }
}

$stmt = $pdo->prepare("SELECT t.*, u.email FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.user_id = ?");
$stmt->execute([$user_id]);
$teacher = $stmt->fetch();

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
</div>

<?php if ($success_msg): ?>
    <div class="alert alert-success"><?= $success_msg ?></div>
<?php endif; ?>
<?php if ($error_msg): ?>
    <div class="alert alert-danger"><?= $error_msg ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <i class="fas fa-user-circle fa-5x text-gray-300 mb-3"></i>
                <h5 class="font-weight-bold"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></h5>
                <p class="text-muted">Employee ID: <?= htmlspecialchars($teacher['employee_id']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($teacher['email']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Details</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Phone *</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($teacher['phone']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Qualification</label>
                            <input type="text" name="qualification" class="form-control" value="<?= htmlspecialchars($teacher['qualification']) ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Joining Date</label>
                            <input type="date" class="form-control" value="<?= htmlspecialchars($teacher['joining_date']) ?>" readonly>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'profile.php', $profile_content);

// 2. Notices (Fully Functional - Read Only)
$notices_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Notices';

$stmt = $pdo->query("SELECT * FROM notices WHERE audience IN ('All', 'Teachers') ORDER BY created_at DESC");
$notices = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Notices & Announcements</h1>
</div>

<div class="row">
    <?php foreach ($notices as $notice): ?>
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= htmlspecialchars($notice['title']) ?></h6>
                    <span class="badge bg-secondary"><?= date('M d, Y', strtotime($notice['created_at'])) ?></span>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($notice['content'])) ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($notices)): ?>
        <div class="col-12">
            <div class="alert alert-info">No notices available at this time.</div>
        </div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'notices.php', $notices_content);

// 3. Students (List all students for now)
$students_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'My Students';

$stmt = $pdo->query("SELECT s.*, c.name as class_name, sec.name as section_name 
                     FROM students s 
                     LEFT JOIN classes c ON s.class_id = c.id 
                     LEFT JOIN sections sec ON s.section_id = sec.id 
                     ORDER BY c.name, sec.name, s.first_name");
$students = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Students</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Admission No</th>
                        <th>Name</th>
                        <th>Class - Section</th>
                        <th>Roll No</th>
                        <th>Gender</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['admission_no']) ?></td>
                            <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td><?= htmlspecialchars($s['class_name'] . ' - ' . $s['section_name']) ?></td>
                            <td><?= htmlspecialchars($s['roll_number']) ?></td>
                            <td><?= htmlspecialchars($s['gender']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'students.php', $students_content);

// 4. Attendance (UI Scaffold)
$attendance_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Attendance';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Mark Attendance</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Select Class</h6>
    </div>
    <div class="card-body">
        <form class="row g-3">
            <div class="col-md-3">
                <label>Class</label>
                <select class="form-select"><option>Select Class</option></select>
            </div>
            <div class="col-md-3">
                <label>Section</label>
                <select class="form-select"><option>Select Section</option></select>
            </div>
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100">Fetch Students</button>
            </div>
        </form>
    </div>
</div>
<div class="alert alert-info">Select a class and section to mark attendance.</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'attendance.php', $attendance_content);

// 5. Assignments (UI Scaffold)
$assignments_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Assignments';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Assignments</h1>
    <button class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Create Assignment</button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Submissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" class="text-center">No assignments found.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'assignments.php', $assignments_content);

// 6. Leaves (UI Scaffold)
$leaves_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Leave Request';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Leave Request</h1>
    <button class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Apply for Leave</button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" class="text-center">No leave requests found.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'leaves.php', $leaves_content);

// 7. Timetable (UI Scaffold)
$timetable_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Timetable';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Timetable</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="alert alert-info">Your weekly timetable is not yet assigned. Please contact the administrator.</div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'timetable.php', $timetable_content);

// 8. Exam Mgt (UI Scaffold)
$exams_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Exam Management';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Exams</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Exam Name</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" class="text-center">No upcoming exams found.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'exams.php', $exams_content);

// 9. Results (UI Scaffold)
$results_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Exam Results';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Upload Results</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Select Exam</h6>
    </div>
    <div class="card-body">
        <form class="row g-3">
            <div class="col-md-4">
                <label>Exam</label>
                <select class="form-select"><option>Select Exam</option></select>
            </div>
            <div class="col-md-4">
                <label>Class</label>
                <select class="form-select"><option>Select Class</option></select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100">Fetch Students</button>
            </div>
        </form>
    </div>
</div>
<div class="alert alert-info">Select an exam and class to enter student marks.</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'results.php', $results_content);

echo "Teacher portal UI successfully built.";
?>
