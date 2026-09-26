<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Assign Fee';

// Fetch students and fee categories for dropdowns
$students = $pdo->query("SELECT id, first_name, last_name, admission_no FROM students ORDER BY first_name ASC")->fetchAll();
$categories = $pdo->query("SELECT id, name FROM fee_categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id_input = $_POST['student_id'] ?? '';
    $fee_category_id = (int)($_POST['fee_category_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $due_date = sanitize($_POST['due_date'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Unpaid');

    if (!empty($student_id_input) && $fee_category_id > 0 && $amount > 0) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO student_fees (student_id, fee_category_id, amount, due_date, status) VALUES (?, ?, ?, ?, ?)");
            
            if ($student_id_input === 'all') {
                foreach ($students as $stu) {
                    $stmt->execute([$stu['id'], $fee_category_id, $amount, $due_date, $status]);
                }
            } else {
                $student_id = (int)$student_id_input;
                $stmt->execute([$student_id, $fee_category_id, $amount, $due_date, $status]);
            }
            
            $pdo->commit();
            set_message('success', 'Fee assigned successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            set_message('error', 'Database error: ' . $e->getMessage());
        }
    } else {
        set_message('error', 'Please fill all required fields correctly.');
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Assign Fee</h1>
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
                        <option value="all">All Students</option>
                        <?php foreach($students as $student): ?>
                            <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> (<?= htmlspecialchars($student['admission_no']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Fee Category <span class="text-danger">*</span> 
                        <a href="../fee_categories/index.php" class="text-primary small ml-2"><i class="fas fa-plus"></i> Manage Categories</a>
                    </label>
                    <select name="fee_category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Unpaid">Unpaid</option>
                        <option value="Partial">Partial</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Assign Fee</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
