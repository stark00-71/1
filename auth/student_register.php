<?php
// auth/student_register.php
require_once '../config/database.php';
require_once '../config/functions.php';

// Redirect if already logged in
require_once '../config/auth.php';
if (is_logged_in()) {
    $role = $_SESSION['user_role'];
    if ($role === 'admin') redirect(BASE_URL . '/admin/dashboard.php');
    if ($role === 'teacher') redirect(BASE_URL . '/teacher/dashboard.php');
    if ($role === 'student') redirect(BASE_URL . '/student/dashboard.php');
}

$error = '';
$success = '';

// Fetch classes and sections for the form
$classes_stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $classes_stmt->fetchAll();
$sections_stmt = $pdo->query("SELECT * FROM sections ORDER BY class_id, name");
$sections = $sections_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid form submission. Please try again.";
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $class_id = (int)($_POST['class_id'] ?? 0);
        $section_id = (int)($_POST['section_id'] ?? 0);
        $gender = sanitize($_POST['gender'] ?? '');
        $dob = sanitize($_POST['dob'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $father_name = sanitize($_POST['father_name'] ?? '');
        $mother_name = sanitize($_POST['mother_name'] ?? '');

        // Handle empty nullable fields to avoid MySQL strict mode errors
        $gender = $gender === '' ? null : $gender;
        $dob = $dob === '' ? null : $dob;
        
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name) || empty($class_id) || empty($section_id)) {
            $error = "Please fill in all required fields.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "Username or Email already exists.";
            } else {
                try {
                    $pdo->beginTransaction();

                    // Insert into users
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (?, ?, ?, 'student', 'active')");
                    $stmt->execute([$username, $email, $password_hash]);
                    $user_id = $pdo->lastInsertId();

                    // Generate Admission Number
                    $admission_no = 'ADM' . date('ymd') . rand(1000, 9999);

                    // Insert into students
                    $stmt = $pdo->prepare("INSERT INTO students (user_id, admission_no, first_name, last_name, gender, dob, phone, father_name, mother_name, class_id, section_id, admission_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 'active')");
                    $stmt->execute([$user_id, $admission_no, $first_name, $last_name, $gender, $dob, $phone, $father_name, $mother_name, $class_id, $section_id]);

                    $pdo->commit();
                    
                    set_message('success', 'Registration successful! You can now log in.');
                    redirect('login.php');
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Registration failed: " . $e->getMessage();
                }
            }
        }
    }
}

// Generate new CSRF token for the form
$csrf_token = generate_csrf_token();
$school_name = get_setting($pdo, 'school_name', 'SCM - School Management System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - <?= htmlspecialchars($school_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        body {
            background-color: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0;
        }
        .register-card {
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="card register-card p-4">
        <div class="text-center mb-4">
            <i class="fas fa-user-graduate fa-3x text-primary mb-2"></i>
            <h4 class="mb-0">Student Registration</h4>
            <p class="text-muted">Create a new student account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <h5 class="mb-3 border-bottom pb-2">Account Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password *</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>

            <h5 class="mb-3 mt-4 border-bottom pb-2">Personal Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">[ Select Gender ]</option>
                        <option value="Male" <?= (($_POST['gender']??'') == 'Male') ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= (($_POST['gender']??'') == 'Female') ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= (($_POST['gender']??'') == 'Other') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Father's Name</label>
                    <input type="text" name="father_name" class="form-control" value="<?= htmlspecialchars($_POST['father_name'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control" value="<?= htmlspecialchars($_POST['mother_name'] ?? '') ?>">
                </div>
            </div>

            <h5 class="mb-3 mt-4 border-bottom pb-2">Academic Details</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Class *</label>
                    <select name="class_id" id="class_id" class="form-select" required onchange="filterSections()">
                        <option value="">[ Select Class ]</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (($_POST['class_id']??'') == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Section *</label>
                    <select name="section_id" id="section_id" class="form-select" required>
                        <option value="">[ Select Section ]</option>
                        <?php foreach($sections as $s): ?>
                            <option value="<?= $s['id'] ?>" data-class="<?= $s['class_id'] ?>" <?= (($_POST['section_id']??'') == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mt-3">
                <i class="fas fa-user-plus me-2"></i>Register
            </button>
            <div class="text-center mt-3">
                Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
            </div>
        </form>
    </div>

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

        function filterSections() {
            const classId = document.getElementById('class_id').value;
            const sections = document.getElementById('section_id').options;
            
            let firstValid = null;
            for(let i=0; i<sections.length; i++) {
                if(sections[i].value === "") continue;
                
                if(sections[i].getAttribute('data-class') === classId) {
                    sections[i].style.display = '';
                    if(!firstValid) firstValid = sections[i].value;
                } else {
                    sections[i].style.display = 'none';
                }
            }
            if(firstValid) {
                document.getElementById('section_id').value = ""; // Reset on change or select first
            }
        }
        
        // Initial filter call on load
        window.addEventListener('DOMContentLoaded', filterSections);
    </script>
</body>
</html>
