<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $admission_no = sanitize($_POST['admission_no'] ?? '');
    $roll_number = sanitize($_POST['roll_number'] ?? '');
    $gender = sanitize($_POST['gender'] ?? 'Male');
    $class_id = (int)($_POST['class_id'] ?? 0);
    $section_id = (int)($_POST['section_id'] ?? 0);
    $dob = sanitize($_POST['dob'] ?? '');
    $father_name = sanitize($_POST['father_name'] ?? '');
    $mother_name = sanitize($_POST['mother_name'] ?? '');
    
    if (!empty($first_name) && !empty($email) && !empty($password) && !empty($admission_no) && $class_id && $section_id) {
        try {
            $pdo->beginTransaction();
            
            // Create user
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'student')");
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            // using admission_no as username initially
            $stmt->execute([$admission_no, $email, $password_hash]);
            $user_id = $pdo->lastInsertId();
            
            // Create student
            $stmt = $pdo->prepare("INSERT INTO students (user_id, admission_no, roll_number, first_name, last_name, gender, dob, father_name, mother_name, class_id, section_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $admission_no, $roll_number, $first_name, $last_name, $gender, $dob, $father_name, $mother_name, $class_id, $section_id]);
            
            $pdo->commit();
            set_message('success', 'Student added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            set_message('error', 'Database error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Please fill required fields.');
    }
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
$sections = $pdo->query("SELECT * FROM sections ORDER BY name")->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Student</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <h5 class="mb-3">Login Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Password *</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3 mt-4">Student Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Admission No *</label>
                    <div class="input-group">
                        <input type="text" name="admission_no" id="admission_no" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="generateStudentId()">
                            Generate
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Roll Number</label>
                    <input type="text" name="roll_number" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Father's Name</label>
                    <input type="text" name="father_name" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Class *</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Section *</label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                        <?php foreach ($sections as $section): ?>
                        <option value="<?= $section['id'] ?>"><?= htmlspecialchars($section['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Student</button>
        </form>
    </div>
</div>
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function generateStudentId() {
    const date = new Date();
    const prefix = 'STD' + date.getFullYear() + (date.getMonth() + 1).toString().padStart(2, '0');
    const random = Math.floor(1000 + Math.random() * 9000);
    document.getElementById('admission_no').value = prefix + random;
}
</script>
<?php include '../../includes/footer.php'; ?>
