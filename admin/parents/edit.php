<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Edit Parent';
$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $father_name = sanitize($_POST['father_name'] ?? '');
    $mother_name = sanitize($_POST['mother_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (!empty($father_name)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE parents SET father_name=?, mother_name=?, phone=? WHERE id=?");
            $stmt->execute([$father_name, $mother_name, $phone, $id]);
            
            $stmt2 = $pdo->prepare("UPDATE users SET email=? WHERE id=(SELECT user_id FROM parents WHERE id=?)");
            $stmt2->execute([$email, $id]);
            $pdo->commit();
            
            set_message('success', 'Parent updated successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            set_message('error', 'Error: Email may already exist.');
        }
    } else {
        set_message('error', 'Father name is required.');
    }
}
$stmt = $pdo->prepare("SELECT p.*, u.email FROM parents p LEFT JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();
if (!$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Parent</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Father Name *</label>
                    <input type="text" name="father_name" class="form-control" value="<?= htmlspecialchars($record['father_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Mother Name</label>
                    <input type="text" name="mother_name" class="form-control" value="<?= htmlspecialchars($record['mother_name']) ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($record['email']) ?>">
                </div>
                <div class="col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($record['phone']) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Parent</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
