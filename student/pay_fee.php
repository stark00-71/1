<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');

$fee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($fee_id <= 0) {
    redirect('fees.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();
$student_id = $student['id'] ?? 0;

// Fetch fee details
$stmt = $pdo->prepare("SELECT sf.*, fc.name as fee_name 
                       FROM student_fees sf 
                       JOIN fee_categories fc ON sf.fee_category_id = fc.id 
                       WHERE sf.id = ? AND sf.student_id = ?");
$stmt->execute([$fee_id, $student_id]);
$fee = $stmt->fetch();

if (!$fee) {
    set_message('error', 'Fee record not found.');
    redirect('fees.php');
}

if ($fee['status'] === 'Paid') {
    set_message('info', 'This fee is already paid.');
    redirect('fees.php');
}

// Calculate remaining amount
$stmt = $pdo->prepare("SELECT SUM(amount_paid) as total_paid FROM fee_payments WHERE student_fee_id = ?");
$stmt->execute([$fee_id]);
$paid_data = $stmt->fetch();
$total_paid = $paid_data['total_paid'] ?? 0;
$remaining = $fee['amount'] - $total_paid;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_message('error', 'Invalid form submission. Please try again.');
        redirect('pay_fee.php?id=' . $fee_id);
    }

    $payment_method = sanitize($_POST['payment_method'] ?? 'Online');
    
    try {
        $pdo->beginTransaction();
        
        $receipt_number = 'REC-' . time() . '-' . rand(100, 999);
        
        $stmt = $pdo->prepare("INSERT INTO fee_payments (student_fee_id, amount_paid, payment_date, payment_method, receipt_number) VALUES (?, ?, CURDATE(), ?, ?)");
        $stmt->execute([$fee_id, $remaining, $payment_method, $receipt_number]);
        
        $stmt = $pdo->prepare("UPDATE student_fees SET status = 'Paid' WHERE id = ?");
        $stmt->execute([$fee_id]);
        
        $pdo->commit();
        set_message('success', 'Payment successful! Receipt generated.');
        redirect('fees.php');
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_message('error', 'Payment failed: ' . $e->getMessage());
    }
}

$page_title = 'Pay Fee';
include '../includes/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pay Fee: <?= htmlspecialchars($fee['fee_name']) ?></h1>
    <a href="fees.php" class="btn btn-sm btn-secondary shadow-sm">Back to Fees</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
            </div>
            <div class="card-body">
                <p><strong>Total Fee Amount:</strong> ₹<?= number_format($fee['amount'], 2) ?></p>
                <p><strong>Amount Already Paid:</strong> ₹<?= number_format($total_paid, 2) ?></p>
                <p><strong>Remaining Amount to Pay:</strong> <span class="text-danger font-weight-bold">₹<?= number_format($remaining, 2) ?></span></p>
                <hr>
                
                <form method="POST" action="" id="paymentForm">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <div class="mb-3">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Net Banking">Net Banking</option>
                            <option value="UPI">UPI</option>
                        </select>
                    </div>
                    
                    <!-- Dummy inputs for appearance -->
                    <div class="mb-3">
                        <label>Card / UPI Details</label>
                        <input type="text" class="form-control" placeholder="Enter details" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100" id="payButton">
                        <span id="btnText"><i class="fas fa-check-circle"></i> Pay ₹<?= number_format($remaining, 2) ?> Now</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    if (form) {
        form.addEventListener('submit', function() {
            const btn = document.getElementById('payButton');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Secure Payment...';
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
