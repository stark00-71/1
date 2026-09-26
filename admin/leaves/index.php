<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Leaves';

$stmt = $pdo->query("SELECT l.*, u.username, u.role FROM leaves l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");
$records = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Leaves</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Apply Leave
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= $record['id'] ?></td>
                        <td><?= htmlspecialchars($record['username']) ?></td>
                        <td><span class="badge badge-info"><?= ucfirst($record['role']) ?></span></td>
                        <td><?= htmlspecialchars($record['leave_type']) ?></td>
                        <td><?= date('M d, Y', strtotime($record['start_date'])) ?> to <?= date('M d, Y', strtotime($record['end_date'])) ?></td>
                        <td><?= htmlspecialchars($record['reason']) ?></td>
                        <td>
                            <?php if ($record['status'] === 'Approved'): ?>
                                <span class="text-success font-weight-bold">Approved</span>
                            <?php elseif ($record['status'] === 'Rejected'): ?>
                                <span class="text-danger font-weight-bold">Rejected</span>
                            <?php else: ?>
                                <span class="text-warning font-weight-bold">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($record['status'] === 'Pending'): ?>
                                <a href="update_status.php?id=<?= $record['id'] ?>&status=Approved" class="btn btn-success btn-sm" title="Approve" onclick="return confirm('Approve this leave?');"><i class="fas fa-check"></i></a>
                                <a href="update_status.php?id=<?= $record['id'] ?>&status=Rejected" class="btn btn-danger btn-sm" title="Reject" onclick="return confirm('Reject this leave?');"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                            <a href="delete.php?id=<?= $record['id'] ?>" class="btn btn-secondary btn-sm" title="Delete" onclick="return confirm('Delete this record?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center">No leave applications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
