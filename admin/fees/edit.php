<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('index.php');
}

// Fetch the existing fee record
$stmt = $pdo->prepare("SELECT * FROM student_fees WHERE id = ?");
$stmt->execute([$id]);
$fee = $stmt->fetch();

if (!$fee) {
    set_message('error', 'Fee record not found.');
    redirect('index.php');
}

$page_title = 'Edit Fee';

// Fetch students and fee categories for dropdowns
$students = $pdo->query("SELECT id, first_name, last_name, admission_no FROM students ORDER BY first_name ASC")->fetchAll();
$categories = $pdo->query("SELECT id, name FROM fee_categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $fee_category_id = (int)($_POST['fee_category_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $due_date = sanitize($_POST['due_date'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Unpaid');

    if ($student_id > 0 && $fee_category_id > 0 && $amount > 0) {
        $update_stmt = $pdo->prepare("UPDATE student_fees SET student_id = ?, fee_category_id = ?, amount = ?, due_date = ?, status = ? WHERE id = ?");
        try {
            $update_stmt->execute([$student_id, $fee_category_id, $amount, $due_date, $status, $id]);
            set_message('success', 'Fee updated successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Database error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Please fill all required fields correctly.');
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Fee</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Student <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        <?php foreach($students as $student): ?>
                            <option value="<?= $student['id'] ?>" <?= $student['id'] == $fee['student_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> (<?= htmlspecialchars($student['admission_no']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Fee Category <span class="text-danger">*</span></label>
                    <select name="fee_category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $category['id'] == $fee['fee_category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" class="form-control" required value="<?= htmlspecialchars($fee['amount']) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($fee['due_date']) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Unpaid" <?= $fee['status'] == 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                        <option value="Partial" <?= $fee['status'] == 'Partial' ? 'selected' : '' ?>>Partial</option>
                        <option value="Paid" <?= $fee['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Fee</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
