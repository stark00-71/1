<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireRole('admin');

$page_title = 'Student ID Card';
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT s.*, c.name as class_name, sec.name as section_name 
                       FROM students s 
                       LEFT JOIN classes c ON s.class_id = c.id 
                       LEFT JOIN sections sec ON s.section_id = sec.id 
                       WHERE s.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student not found.");
}

include '../../includes/header.php';
?>

<style>
    .id-card-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        margin-bottom: 40px;
    }
    .id-card {
        width: 250px;
        height: 400px;
        border: 2px solid #ccc;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        overflow: hidden;
        position: relative;
        font-family: 'Arial', sans-serif;
    }
    .id-card-header {
        background: #195190;
        color: white;
        text-align: center;
        padding: 10px 5px 30px 5px;
        position: relative;
        border-bottom-left-radius: 50% 20px;
        border-bottom-right-radius: 50% 20px;
    }
    .id-card-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: bold;
    }
    .id-card-header p {
        margin: 2px 0;
        font-size: 0.65rem;
        line-height: 1.1;
    }
    .id-card-photo {
        width: 80px;
        height: 100px;
        background: #fff;
        border: 2px solid #195190;
        margin: -40px auto 10px auto;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .id-card-photo i {
        font-size: 3rem;
        color: #ccc;
    }
    .id-card-name {
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #333;
    }
    .id-card-details {
        padding: 0 15px;
        font-size: 0.7rem;
        line-height: 1.4;
        color: #333;
    }
    .id-card-details table {
        width: 100%;
    }
    .id-card-details td {
        vertical-align: top;
    }
    .id-card-details td:first-child {
        font-weight: bold;
        width: 40%;
    }
    .id-card-footer {
        position: absolute;
        bottom: 10px;
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 15px;
        font-size: 0.65rem;
    }
    .id-card-footer .sign {
        text-align: center;
    }
    .id-card-footer .sign img {
        height: 20px;
        display: block;
        margin: 0 auto;
    }
    .id-card-footer .sign-line {
        border-top: 1px solid #333;
        margin-top: 25px;
        padding-top: 2px;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .id-card-container, .id-card-container * {
            visibility: visible;
        }
        .id-card-container {
            position: absolute;
            left: 0;
            top: 0;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .id-card {
            box-shadow: none;
            border: 1px solid #000;
        }
        .id-card-header {
            background-color: #195190 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact; 
        }
        .d-sm-flex, .btn {
            display: none !important;
        }
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Student ID Card</h1>
    <div>
        <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
        <button onclick="window.print()" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-print fa-sm text-white-50"></i> Print ID Card</button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="id-card-container">
            <div class="id-card">
                <div class="id-card-header">
                    <h5>School Name</h5>
                    <p>(Govt. Recognised)<br>
                    Place your address,<br>
                    District State and Pin - 000000</p>
                    <p><strong>Phone No.: 9339400600</strong></p>
                </div>
                
                <div class="id-card-photo">
                    <i class="fas fa-user"></i>
                </div>
                
                <div class="id-card-name">
                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                </div>
                
                <div class="id-card-details">
                    <table border="0" cellpadding="2" cellspacing="0">
                        <tr>
                            <td>Father's Name</td>
                            <td>: <?= htmlspecialchars($student['father_name'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <td>Mother's Name</td>
                            <td>: <?= htmlspecialchars($student['mother_name'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <td>D.O.B.</td>
                            <td>: <?= htmlspecialchars($student['dob'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <td>Contact No.</td>
                            <td>: <?= htmlspecialchars($student['phone'] ?? ($student['parent_phone'] ?? 'N/A')) ?></td>
                        </tr>
                        <tr>
                            <td>Add.</td>
                            <td>: <?= htmlspecialchars($student['address'] ?? 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="id-card-footer">
                    <div>
                        <br><br>
                        <strong>Class: <?= htmlspecialchars($student['class_name']) ?></strong>
                    </div>
                    <div class="sign">
                        <div class="sign-line">Principal Sign.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
