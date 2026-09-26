<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Edit Admin';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('index.php');
}

// Fetch admin details
$stmt = $pdo->prepare("SELECT a.*, u.username, u.email FROM admins a JOIN users u ON a.user_id = u.id WHERE a.id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    redirect('index.php');
}

// Prevent standard admins from editing the Super Admin
if ($admin['is_super_admin'] == 1 && !($_SESSION['is_super_admin'] ?? ($_SESSION['user_id'] == 1))) {
    set_message('danger', 'Access Denied: You cannot modify a Super Admin.');
    redirect('index.php');
}

// Fetch roles for the dropdown
$roles_stmt = $pdo->query("SELECT * FROM roles ORDER BY name");
$roles = $roles_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
    
    if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($username)) {
        try {
            $pdo->beginTransaction();
            
            // Update admin details
            if ($_SESSION['is_super_admin'] ?? ($_SESSION['user_id'] == 1)) {
                $is_super_admin = isset($_POST['is_super_admin']) ? 1 : 0;
                $stmt = $pdo->prepare("UPDATE admins SET first_name = ?, last_name = ?, phone = ?, is_super_admin = ?, role_id = ? WHERE id = ?");
                $stmt->execute([$first_name, $last_name, $phone, $is_super_admin, $role_id, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE admins SET first_name = ?, last_name = ?, phone = ?, role_id = ? WHERE id = ?");
                $stmt->execute([$first_name, $last_name, $phone, $role_id, $id]);
            }
            
            // Update user email and username
            $stmt = $pdo->prepare("UPDATE users SET email = ?, username = ? WHERE id = ?");
            $stmt->execute([$email, $username, $admin['user_id']]);
            
            // Update password if provided
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $admin['user_id']]);
            }
            
            $pdo->commit();
            set_message('success', 'Admin updated successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                set_message('danger', 'Error: The email address or username you entered is already in use by another account.');
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
    <h1 class="h3 mb-0 text-gray-800">Edit Admin</h1>
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
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Password (Leave blank to keep current)</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control">
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
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($admin['first_name']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($admin['last_name']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Custom Role</label>
                    <select name="role_id" class="form-select">
                        <option value="">No Role (Basic Access)</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= $admin['role_id'] == $role['id'] ? 'selected' : '' ?>><?= htmlspecialchars($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if ($_SESSION['is_super_admin'] ?? ($_SESSION['user_id'] == 1)): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_super_admin" name="is_super_admin" value="1" <?= $admin['is_super_admin'] ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="is_super_admin">Assign Super Admin Role</label>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary mt-3">Update Admin</button>
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
