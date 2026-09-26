<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Parent';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $father_name = sanitize($_POST['father_name'] ?? '');
    $mother_name = sanitize($_POST['mother_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (!empty($father_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO parents (father_name, mother_name, email, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$father_name, $mother_name, $email, $phone]);
            set_message('success', 'Parent added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Error: Email may already exist.');
        }
    } else {
        set_message('error', 'Father name is required.');
    }
}
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Parent</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Father Name *</label>
                    <input type="text" name="father_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Mother Name</label>
                    <input type="text" name="mother_name" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Parent</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
