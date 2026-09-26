<?php

require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$stmt = $pdo->query("SELECT sf.id as receipt_number, sf.amount as amount_paid, sf.created_at as payment_date, 'Cash' as payment_method, s.first_name, s.last_name, s.admission_no, fc.name as fee_name 
                     FROM student_fees sf 
                     JOIN students s ON sf.student_id = s.id 
                     JOIN fee_categories fc ON sf.fee_category_id = fc.id 
                     WHERE sf.status = 'Paid'
                     ORDER BY sf.created_at DESC LIMIT 100");
$records = $stmt->fetchAll();

$page_title = 'Fees Collection Report';
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Fees Collection Report</h1>
    <a href="index.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Reports
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Recent 100 Fee Payments</h6>
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
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                        <th>Method</th>
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
                        <td class="font-weight-bold text-success">₹<?= number_format($row['amount_paid'], 2) ?></td>
                        <td><?= date('M d, Y', strtotime($row['payment_date'])) ?></td>
                        <td><?= htmlspecialchars($row['payment_method']) ?></td>
                        <td>
                            <a href="../fees/print_receipt.php?id=<?= $row['receipt_number'] ?>" class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($records)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No fee payments found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
