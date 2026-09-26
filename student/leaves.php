<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');

$user_id = $_SESSION['user_id'];

// Handle new leave application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_leave'])) {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $reason = trim($_POST['reason'] ?? '');
    
    if ($start_date && $end_date && $reason) {
        $stmt = $pdo->prepare("INSERT INTO leaves (user_id, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, 'Pending')");
        if ($stmt->execute([$user_id, $start_date, $end_date, $reason])) {
            set_message('success', 'Leave application submitted successfully.');
        } else {
            set_message('error', 'Failed to submit leave application.');
        }
        redirect('leaves.php');
    } else {
        set_message('error', 'Please fill all required fields.');
    }
}

// Fetch leaves
$stmt = $pdo->prepare("SELECT * FROM leaves WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$leaves = $stmt->fetchAll();

$page_title = 'My Leaves';
include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Leaves</h1>
    <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Apply for Leave
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Leave History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date Applied</th>
                        <th>Duration</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $leave): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($leave['created_at'])) ?></td>
                        <td>
                            <?= date('M d, Y', strtotime($leave['start_date'])) ?> to 
                            <?= date('M d, Y', strtotime($leave['end_date'])) ?>
                        </td>
                        <td><?= htmlspecialchars($leave['reason']) ?></td>
                        <td>
                            <?php if ($leave['status'] === 'Approved'): ?>
                                <span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Approved</span>
                            <?php elseif ($leave['status'] === 'Rejected'): ?>
                                <span class="text-danger font-weight-bold"><i class="fas fa-times-circle"></i> Rejected</span>
                            <?php else: ?>
                                <span class="text-warning font-weight-bold"><i class="fas fa-clock"></i> Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($leaves)): ?>
                    <tr>
                        <td colspan="4" class="text-center">You have not submitted any leave applications.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" role="dialog" aria-labelledby="applyLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyLeaveModalLabel">Apply for Leave</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="Please explain why you need leave..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="apply_leave" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
