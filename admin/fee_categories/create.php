<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Fee Category';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO fee_categories (name, description) VALUES (?, ?)");
        try {
            $stmt->execute([$name, $description]);
            set_message('success', 'Fee category added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Database error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Please provide a category name.');
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Fee Category</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label>Category Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Tuition Fee, Transport Fee">
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Category</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
