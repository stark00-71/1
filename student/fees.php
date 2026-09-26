<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');

$user_id = $_SESSION['user_id'];

// Get the student's ID
$stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();
$student_id = $student['id'] ?? 0;

// Fetch their fees
$stmt = $pdo->prepare("SELECT sf.*, fc.name as fee_name 
                       FROM student_fees sf 
                       JOIN fee_categories fc ON sf.fee_category_id = fc.id 
                       WHERE sf.student_id = ? 
                       ORDER BY sf.due_date DESC");
$stmt->execute([$student_id]);
$fees = $stmt->fetchAll();

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
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fees as $fee): ?>
                        <tr>
                            <td><?= htmlspecialchars($fee['fee_name']) ?></td>
                            <td>₹<?= number_format($fee['amount'], 2) ?></td>
                            <td><?= $fee['due_date'] ? date('M d, Y', strtotime($fee['due_date'])) : 'N/A' ?></td>
                            <td>
                                <?php if ($fee['status'] === 'Paid'): ?>
                                    <span class="text-success font-weight-bold">Paid</span>
                                <?php elseif ($fee['status'] === 'Partial'): ?>
                                    <span class="text-warning font-weight-bold">Partial</span>
                                <?php else: ?>
                                    <span class="text-danger font-weight-bold">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($fee['status'] === 'Paid'): ?>
                                    <a href="print_receipt.php?id=<?= $fee['id'] ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                <?php else: ?>
                                    <a href="pay_fee.php?id=<?= $fee['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-credit-card"></i> Pay Now
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($fees)): ?>
                        <tr><td colspan="5" class="text-center">No fee records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>