<?php
// auth/login.php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    $role = $_SESSION['user_role'];
    if ($role === 'admin') redirect(BASE_URL . '/admin/dashboard.php');
    if ($role === 'teacher') redirect(BASE_URL . '/teacher/dashboard.php');
    if ($role === 'student') redirect(BASE_URL . '/student/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid form submission. Please try again.";
    } else {
        $identifier = sanitize($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = sanitize($_POST['role'] ?? '');
        
        if (empty($identifier) || empty($password) || empty($role)) {
            $error = "All fields are required.";
        } else {
            $login_result = attempt_login($pdo, $identifier, $password, $role);
            if ($login_result['success']) {
                // Redirect to respective dashboard
                if ($role === 'admin') redirect(BASE_URL . '/admin/dashboard.php');
                if ($role === 'teacher') redirect(BASE_URL . '/teacher/dashboard.php');
                if ($role === 'student') redirect(BASE_URL . '/student/dashboard.php');
            } else {
                $error = $login_result['message'];
            }
        }
    }
}

// Generate new CSRF token for the form
$csrf_token = generate_csrf_token();
$school_name = get_setting($pdo, 'school_name', 'SCM - School Management System');
$login_bg_image = get_setting($pdo, 'login_bg_image', '');
$bg_image_url = $login_bg_image ? BASE_URL . '/uploads/' . $login_bg_image : BASE_URL . '/assets/img/login_bg.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars($school_name) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        body {
            background-color: #0f172a;
            background-image: url('<?= $bg_image_url ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: #e2e8f0;
        }
        
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            background-color: rgba(9, 21, 46, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 2.5rem 2rem;
        }

        .login-card .text-primary {
            color: #3b82f6 !important;
        }
        
        .login-card h4 {
            color: #ffffff;
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }
        
        .login-card h4 span {
            color: #3b82f6;
        }

        .login-card p.text-muted {
            color: #94a3b8 !important;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #f8fafc;
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select, .input-group-text {
            background-color: rgba(15, 34, 70, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(15, 34, 70, 0.8);
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
            color: #ffffff;
        }
        
        /* Select placeholder styling workaround */
        .form-select option {
            background-color: #0f172a;
            color: #ffffff;
        }

        .input-group-text {
            border-right: none;
            color: #94a3b8;
        }

        .form-control {
            border-left: none;
            padding-left: 0;
        }
        
        /* Fix for browser autofill */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0f2246 inset !important;
            -webkit-text-fill-color: #f1f5f9 !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        
        /* Special case for toggle button */
        #togglePassword {
            border-left: none;
            border-color: rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        #togglePassword:hover {
            color: #ffffff;
            background-color: transparent;
        }

        .form-check-label {
            font-size: 0.85rem;
            color: #cbd5e1;
        }
        
        .form-check-input {
            background-color: rgba(15, 34, 70, 0.6);
            border-color: rgba(255,255,255,0.2);
        }
        
        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        a {
            color: #3b82f6;
            font-size: 0.85rem;
        }
        
        a:hover {
            color: #60a5fa;
        }

        .btn-login {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            padding: 0.6rem;
            margin-top: 0.5rem;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }
        
        .register-link {
            font-size: 0.85rem;
            color: #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-3">
            <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
            <h4>SCM - School Management<br><span>System</span></h4>
            <p class="text-muted">Sign in to continue</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="background-color: rgba(220,53,69,0.2); color: #fca5a5; border-color: rgba(220,53,69,0.3); font-size: 0.9rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="mb-3">
                <label class="form-label">Role</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                    <select name="role" class="form-select" required>
                        <option value="">[ Select Role ]</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email/Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="identifier" class="form-control" placeholder="Email or Username" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
            <div class="text-center register-link mt-2">
                New Student? <a href="student_register.php" class="text-decoration-none fw-medium">Register here</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            toggleIcon.classList.toggle('fa-eye-slash');
            toggleIcon.classList.toggle('fa-eye');
        });
    </script>
</body>
</html>
