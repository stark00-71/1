<?php

require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');

$stmt = $pdo->query("SELECT sf.id as receipt_number, sf.amount, sf.status, sf.due_date, s.first_name, s.last_name, s.admission_no, fc.name as fee_name 
                     FROM student_fees sf 
                     JOIN students s ON sf.student_id = s.id 
                     JOIN fee_categories fc ON sf.fee_category_id = fc.id 
                     ORDER BY sf.due_date DESC LIMIT 100");
$records = $stmt->fetchAll();

$page_title = 'Student Fees List';
include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Student Fees</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Recent Fee Records</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student Name</th>
                        <th>Admission No</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['receipt_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['admission_no']) ?></td>
                        <td><?= htmlspecialchars($row['fee_name']) ?></td>
                        <td>₹<?= number_format($row['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['due_date']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'Paid'): ?>
                                <span class="text-success font-weight-bold">Paid</span>
                            <?php elseif ($row['status'] === 'Partial'): ?>
                                <span class="text-warning font-weight-bold">Partial</span>
                            <?php else: ?>
                                <span class="text-danger font-weight-bold">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'Paid'): ?>
                                <a href="print_receipt.php?id=<?= $row['receipt_number'] ?>" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($records)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No fee records found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
