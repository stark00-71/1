<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My ID Card';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT s.*, u.email, c.name as class_name, sec.name as section_name 
                       FROM students s 
                       JOIN users u ON s.user_id = u.id 
                       LEFT JOIN classes c ON s.class_id = c.id 
                       LEFT JOIN sections sec ON s.section_id = sec.id 
                       WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student not found.");
}

include '../includes/header.php';
?>

<style>
    .id-card-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        margin-bottom: 40px;
    }
    .id-card {
        width: 350px;
        border: 2px solid #4e73df;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        position: relative;
    }
    .id-card-header {
        background: #4e73df;
        color: white;
        text-align: center;
        padding: 15px 10px;
    }
    .id-card-header h4 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: bold;
    }
    .id-card-header p {
        margin: 0;
        font-size: 0.9rem;
    }
    .id-card-body {
        padding: 20px;
        text-align: center;
    }
    .id-card-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #eaecf4;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        border: 3px solid #4e73df;
    }
    .id-card-photo i {
        font-size: 3rem;
        color: #b7b9cc;
    }
    .id-card-details {
        text-align: left;
        font-size: 0.95rem;
    }
    .id-card-details p {
        margin-bottom: 5px;
        border-bottom: 1px dashed #eaecf4;
        padding-bottom: 5px;
    }
    .id-card-details p:last-child {
        border-bottom: none;
    }
    .id-card-footer {
        background: #f8f9fc;
        padding: 10px;
        text-align: center;
        font-size: 0.8rem;
        border-top: 1px solid #eaecf4;
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
            border: 2px solid #000;
        }
        .id-card-header {
            background-color: #4e73df !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact; 
        }
        .d-sm-flex, .btn {
            display: none !important;
        }
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My ID Card</h1>
    <button onclick="window.print()" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-print fa-sm text-white-50"></i> Print ID Card</button>
</div>

<div class="row">
    <div class="col-12">
        <div class="id-card-container">
            <div class="id-card">
                <div class="id-card-header">
                    <h4>School Management System</h4>
                    <p>Student Identity Card</p>
                </div>
                <div class="id-card-body">
                    <div class="id-card-photo">
                        <?php if (!empty($student['profile_photo'])): ?>
                            <img src="<?= BASE_URL ?>/uploads/students/<?= htmlspecialchars($student['profile_photo']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <h5 class="font-weight-bold mb-3"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h5>
                    
                    <div class="id-card-details">
                        <p><strong>Adm No:</strong> <?= htmlspecialchars($student['admission_no']) ?></p>
                        <p><strong>Class:</strong> <?= htmlspecialchars($student['class_name'] . ' - ' . $student['section_name']) ?></p>
                        <p><strong>DOB:</strong> <?= htmlspecialchars($student['dob'] ?? 'N/A') ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone'] ?? 'N/A') ?></p>
                        <p><strong>Blood Group:</strong> <?= htmlspecialchars($student['blood_group'] ?? 'N/A') ?></p>
                    </div>
                </div>
                <div class="id-card-footer">
                    Valid for the current academic year
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
