<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Teacher';

$roles = $pdo->query("SELECT * FROM roles ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $employee_id = sanitize($_POST['employee_id'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $qualification = sanitize($_POST['qualification'] ?? '');
    $joining_date = sanitize($_POST['joining_date'] ?? '');
    $designation = sanitize($_POST['designation'] ?? 'Teacher');
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
    
    if (!empty($first_name) && !empty($last_name) && !empty($employee_id) && !empty($username) && !empty($password)) {
        try {
            $pdo->beginTransaction();
            // Create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, 'teacher')");
            $stmt->execute([$username, $hashed_password, $email]);
            $user_id = $pdo->lastInsertId();
            
            // Create teacher
            $stmt = $pdo->prepare("INSERT INTO teachers (user_id, first_name, last_name, employee_id, phone, qualification, joining_date, designation, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $first_name, $last_name, $employee_id, $phone, $qualification, $joining_date, $designation, $role_id]);
            
            $pdo->commit();
            set_message('success', 'Teacher added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            set_message('danger', 'Error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Required fields are missing.');
    }
}
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Teacher</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <h5 class="text-primary">Login Details</h5>
            <hr>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" required>
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
            
            <h5 class="text-primary mt-4">Teacher Details</h5>
            <hr>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Employee ID *</label>
                    <input type="text" name="employee_id" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Qualification</label>
                    <input type="text" name="qualification" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Joining Date</label>
                    <input type="date" name="joining_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Designation</label>
                    <input type="text" name="designation" class="form-control" value="Teacher">
                </div>
                <div class="col-md-3">
                    <label>System Role (Optional)</label>
                    <select name="role_id" class="form-control">
                        <option value="">-- None --</option>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Teacher</button>
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
</script>
<?php include '../../includes/footer.php'; ?>
