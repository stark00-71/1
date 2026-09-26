<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Class';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO classes (name) VALUES (?)");
        try {
            $stmt->execute([$name]);
            set_message('success', 'Class added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Error: Name may already exist.');
        }
    } else {
        set_message('error', 'Name is required.');
    }
}
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Class</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
