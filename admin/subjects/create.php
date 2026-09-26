<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Subject';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $code = sanitize($_POST['code'] ?? '');
    
    if (!empty($name) && !empty($code)) {
        $stmt = $pdo->prepare("INSERT INTO subjects (name, code) VALUES (?, ?)");
        try {
            $stmt->execute([$name, $code]);
            set_message('success', 'Subject added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Error: Subject code may already exist.');
        }
    } else {
        set_message('error', 'All fields are required.');
    }
}
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Subject</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label>Subject Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Subject Code</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
