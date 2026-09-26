<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$users_stmt = $pdo->query("SELECT id, username, role FROM users WHERE role IN ('teacher', 'student') AND status = 'active' ORDER BY role, username");
$users = $users_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $leave_type = $_POST['leave_type'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $reason = $_POST['reason'] ?? '';

    $errors = [];
    if (empty($user_id)) $errors[] = "User is required";
    if (empty($leave_type)) $errors[] = "Leave type is required";
    if (empty($start_date)) $errors[] = "Start date is required";
    if (empty($end_date)) $errors[] = "End date is required";
    if ($start_date > $end_date) $errors[] = "End date cannot be before start date";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO leaves (user_id, leave_type, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, ?, 'Approved')");
            $stmt->execute([$user_id, $leave_type, $start_date, $end_date, $reason]);
            header("Location: index.php?msg=Leave added successfully");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

$page_title = 'Apply Leave';
include '../../includes/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Apply Leave</h1>
    <a href="index.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Leaves
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Select User</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select a User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['username']) ?> (<?= ucfirst($user['role']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Leave Type</label>
                <select name="leave_type" class="form-control" required>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Casual Leave">Casual Leave</option>
                    <option value="Vacation">Vacation</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Reason</label>
                <textarea name="reason" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Leave</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
