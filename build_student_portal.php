<?php
$base_dir = 'C:\\xampp\\htdocs\\1\\student\\';

// 1. Profile (Fully Functional)
$profile_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My Profile';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (!empty($phone)) {
        try {
            $stmt = $pdo->prepare("UPDATE students SET phone=? WHERE user_id=?");
            $stmt->execute([$phone, $user_id]);
            $success_msg = "Profile updated successfully.";
        } catch (PDOException $e) {
            $error_msg = "Error updating profile.";
        }
    } else {
        $error_msg = "Phone number is required.";
    }
}

$stmt = $pdo->prepare("SELECT s.*, u.email, c.name as class_name, sec.name as section_name 
                       FROM students s 
                       JOIN users u ON s.user_id = u.id 
                       LEFT JOIN classes c ON s.class_id = c.id 
                       LEFT JOIN sections sec ON s.section_id = sec.id 
                       WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

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
                <i class="fas fa-user-graduate fa-5x text-gray-300 mb-3"></i>
                <h5 class="font-weight-bold"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h5>
                <p class="text-muted">Admission No: <?= htmlspecialchars($student['admission_no']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($student['class_name'] . ' - ' . $student['section_name']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($student['email']) ?></p>
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
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" value="<?= htmlspecialchars($student['date_of_birth']) ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Blood Group</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($student['blood_group']) ?>" readonly>
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
requireRole('student');
$page_title = 'Notices';

$stmt = $pdo->query("SELECT * FROM notices WHERE audience IN ('All', 'Students') ORDER BY created_at DESC");
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

// 3. Attendance (UI Scaffold)
$attendance_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My Attendance';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Attendance</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="alert alert-info">No attendance records found for the current month.</div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="2" class="text-center">No records available.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'attendance.php', $attendance_content);

// 4. Assignments (UI Scaffold)
$assignments_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My Assignments';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Assignments</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" class="text-center">No upcoming assignments found.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'assignments.php', $assignments_content);

// 5. Timetable (UI Scaffold)
$timetable_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'Timetable';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Class Timetable</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="alert alert-info">Your class timetable is currently being generated. Please check back later.</div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'timetable.php', $timetable_content);

// 6. Fees (UI Scaffold)
$fees_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My Fees';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Fee Payments</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Fee Category</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="text-center">No fee records found.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'fees.php', $fees_content);

// 7. Results (UI Scaffold)
$results_content = <<<'EOD'
<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'Exam Results';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Results</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Exam Name</th>
                        <th>Subject</th>
                        <th>Marks Obtained</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="text-center">No exam results published yet.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
EOD;
file_put_contents($base_dir . 'results.php', $results_content);

echo "Student portal UI successfully built.";
?>
