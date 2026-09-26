<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Edit Teacher';
$id = $_GET['id'] ?? 0;

$roles = $pdo->query("SELECT * FROM roles ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $employee_id = sanitize($_POST['employee_id'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $qualification = sanitize($_POST['qualification'] ?? '');
    $joining_date = sanitize($_POST['joining_date'] ?? '');
    $designation = sanitize($_POST['designation'] ?? 'Teacher');
    $role_id = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
    
    if (!empty($first_name) && !empty($last_name) && !empty($employee_id)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE teachers SET first_name=?, last_name=?, employee_id=?, phone=?, qualification=?, joining_date=?, designation=?, role_id=? WHERE id=?");
            $stmt->execute([$first_name, $last_name, $employee_id, $phone, $qualification, $joining_date, $designation, $role_id, $id]);
            
            $stmt2 = $pdo->prepare("UPDATE users SET email=?, username=? WHERE id=(SELECT user_id FROM teachers WHERE id=?)");
            $stmt2->execute([$email, $username, $id]);
            $pdo->commit();
            
            set_message('success', 'Teacher updated successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            set_message('error', 'Error: Email, Username, or Employee ID may already exist.');
        }
    } else {
        set_message('error', 'Required fields are missing.');
    }
}
$stmt = $pdo->prepare("SELECT t.*, u.email, u.username FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();
if (!$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Teacher</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <h5 class="text-primary mt-4">Teacher Details</h5>
            <hr>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($record['first_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($record['last_name']) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Employee ID *</label>
                    <input type="text" name="employee_id" class="form-control" value="<?= htmlspecialchars($record['employee_id']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($record['username']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($record['email']) ?>">
                </div>
                <div class="col-md-3">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($record['phone']) ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Qualification</label>
                    <input type="text" name="qualification" class="form-control" value="<?= htmlspecialchars($record['qualification']) ?>">
                </div>
                <div class="col-md-3">
                    <label>Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" value="<?= htmlspecialchars($record['joining_date']) ?>">
                </div>
                <div class="col-md-3">
                    <label>Designation</label>
                    <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($record['designation'] ?? 'Teacher') ?>">
                </div>
                <div class="col-md-3">
                    <label>System Role (Optional)</label>
                    <select name="role_id" class="form-control">
                        <option value="">-- None --</option>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (isset($record['role_id']) && $record['role_id'] == $r['id']) ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Teacher</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
