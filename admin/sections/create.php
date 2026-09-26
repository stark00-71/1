<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Add Section';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $class_id = (int)($_POST['class_id'] ?? 0);
    
    if (!empty($name) && $class_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO sections (class_id, name) VALUES (?, ?)");
        try {
            $stmt->execute([$class_id, $name]);
            set_message('success', 'Section added successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Error adding section.');
        }
    } else {
        set_message('error', 'All fields are required.');
    }
}
$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Section</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label>Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Section Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
