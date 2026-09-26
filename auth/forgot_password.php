<?php
require_once '../config/database.php';
require_once '../config/functions.php';

$school_name = get_setting($pdo, 'school_name', 'SCM - School Management System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?= htmlspecialchars($school_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="card login-card p-4 text-center">
        <i class="fas fa-lock fa-3x text-warning mb-3"></i>
        <h4 class="mb-3">Forgot Your Password?</h4>
        <p class="text-muted mb-4">For security reasons, password resets are handled directly by the school administration.</p>
        
        <div class="alert alert-info text-start">
            <strong><i class="fas fa-info-circle me-1"></i> What to do:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>Students & Parents:</strong> Please contact your class teacher or the school office to request a password reset.</li>
                <li><strong>Teachers:</strong> Please contact the system administrator.</li>
            </ul>
        </div>
        
        <a href="login.php" class="btn btn-primary mt-3 w-100">
            <i class="fas fa-arrow-left me-2"></i>Return to Login
        </a>
    </div>
</body>
</html>
