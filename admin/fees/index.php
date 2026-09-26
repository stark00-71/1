<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Fees';

$stmt = $pdo->query("SELECT sf.*, s.first_name, s.last_name, s.admission_no, fc.name as category_name 
                     FROM student_fees sf 
                     JOIN students s ON sf.student_id = s.id 
                     JOIN fee_categories fc ON sf.fee_category_id = fc.id 
                     ORDER BY sf.id DESC");
$records = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Fees</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Assign Fee
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Fee Category</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= $record['id'] ?></td>
                        <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?> (<?= htmlspecialchars($record['admission_no']) ?>)</td>
                        <td><?= htmlspecialchars($record['category_name']) ?></td>
                        <td><?= htmlspecialchars($record['amount']) ?></td>
                        <td><?= htmlspecialchars($record['due_date']) ?></td>
                        <td>
                            <?php if ($record['status'] === 'Paid'): ?>
                                <span class="text-success font-weight-bold">Paid</span>
                            <?php elseif ($record['status'] === 'Partial'): ?>
                                <span class="text-warning font-weight-bold">Partial</span>
                            <?php else: ?>
                                <span class="text-danger font-weight-bold">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="print_receipt.php?id=<?= $record['id'] ?>" class="btn btn-info btn-sm" title="Print Receipt" target="_blank"><i class="fas fa-print"></i></a>
                            <a href="edit.php?id=<?= $record['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $record['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="7" class="text-center">No fees found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
