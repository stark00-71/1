<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Admin';

// Fetch roles for the dropdown
$roles_stmt = $pdo->query("SELECT * FROM roles ORDER BY name");
$roles = $roles_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
    
    if (!empty($first_name) && !empty($last_name) && !empty($username) && !empty($password) && !empty($email)) {
        try {
            $pdo->beginTransaction();
            // Create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$username, $hashed_password, $email]);
            $user_id = $pdo->lastInsertId();
            
            // Create admin
            if ($_SESSION['is_super_admin'] ?? ($_SESSION['user_id'] == 1)) {
                $is_super_admin = isset($_POST['is_super_admin']) ? 1 : 0;
                $stmt = $pdo->prepare("INSERT INTO admins (user_id, first_name, last_name, phone, is_super_admin, role_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $first_name, $last_name, $phone, $is_super_admin, $role_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO admins (user_id, first_name, last_name, phone, role_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $first_name, $last_name, $phone, $role_id]);
            }
            
            $pdo->commit();
            set_message('success', 'Admin added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                set_message('danger', 'Error: The username or email address you entered is already in use by another account.');
            } else {
                set_message('danger', 'Error: ' . $e->getMessage());
            }
        }
    } else {
        set_message('danger', 'Required fields are missing.');
    }
}
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Admin</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <h5 class="text-primary">Login Details</h5>
            <hr>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Password *</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <h5 class="text-primary mt-4">Admin Details</h5>
            <hr>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Custom Role</label>
                    <select name="role_id" class="form-select">
                        <option value="">No Role (Basic Access)</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if ($_SESSION['is_super_admin'] ?? ($_SESSION['user_id'] == 1)): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_super_admin" name="is_super_admin" value="1">
                        <label class="custom-control-label" for="is_super_admin">Assign Super Admin Role</label>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary mt-3">Save Admin</button>
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
