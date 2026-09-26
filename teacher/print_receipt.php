<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid fee ID.");
}

$stmt = $pdo->prepare("SELECT sf.*, s.first_name, s.last_name, s.admission_no, fc.name as category_name 
                       FROM student_fees sf 
                       JOIN students s ON sf.student_id = s.id 
                       JOIN fee_categories fc ON sf.fee_category_id = fc.id 
                       WHERE sf.id = ?");
$stmt->execute([$id]);
$fee = $stmt->fetch();

if (!$fee) {
    die("Fee record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt - <?= htmlspecialchars($fee['first_name'] . ' ' . $fee['last_name']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt-container {
            width: 600px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #555; }
        .content { line-height: 1.6; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .label { font-weight: bold; width: 150px; }
        .value { flex-grow: 1; border-bottom: 1px dotted #ccc; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        .status-paid { color: green; font-weight: bold; }
        .status-unpaid { color: red; font-weight: bold; }
        .status-partial { color: orange; font-weight: bold; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            .receipt-container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Receipt</button>
    <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Close</button>
</div>

<div class="receipt-container">
    <div class="header">
        <h1>School Management System</h1>
        <p>Official Fee Receipt</p>
    </div>
    
    <div class="content">
        <div class="row">
            <div class="label">Receipt No:</div>
            <div class="value">REC-<?= str_pad($fee['id'], 6, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="row">
            <div class="label">Date:</div>
            <div class="value"><?= date('Y-m-d') ?></div>
        </div>
        <div class="row">
            <div class="label">Student Name:</div>
            <div class="value"><?= htmlspecialchars($fee['first_name'] . ' ' . $fee['last_name']) ?></div>
        </div>
        <div class="row">
            <div class="label">Admission No:</div>
            <div class="value"><?= htmlspecialchars($fee['admission_no']) ?></div>
        </div>
        <div class="row">
            <div class="label">Fee Category:</div>
            <div class="value"><?= htmlspecialchars($fee['category_name']) ?></div>
        </div>
        <div class="row">
            <div class="label">Amount:</div>
            <div class="value">₹<?= number_format($fee['amount'], 2) ?></div>
        </div>
        <div class="row">
            <div class="label">Due Date:</div>
            <div class="value"><?= htmlspecialchars($fee['due_date']) ?></div>
        </div>
        <div class="row">
            <div class="label">Payment Status:</div>
            <div class="value">
                <?php
                $status = $fee['status'];
                if ($status === 'Paid') echo '<span class="status-paid">PAID</span>';
                elseif ($status === 'Partial') echo '<span class="status-partial">PARTIAL</span>';
                else echo '<span class="status-unpaid">UNPAID</span>';
                ?>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>This is a computer generated document. No signature is required.</p>
        <p>Thank you for your payment.</p>
    </div>
</div>

</body>
</html>
