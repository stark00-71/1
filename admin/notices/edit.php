<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Edit Notice';
$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $target_audience = sanitize($_POST['target_audience'] ?? 'Everyone');
    $class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
    $publish_date = sanitize($_POST['publish_date'] ?? date('Y-m-d'));
    $expiry_date = !empty($_POST['expiry_date']) ? sanitize($_POST['expiry_date']) : null;
    
    if ($target_audience !== 'Specific Class') $class_id = null;
    
    if (!empty($title) && !empty($description)) {
        try {
            $stmt = $pdo->prepare("UPDATE notices SET title=?, description=?, target_audience=?, class_id=?, publish_date=?, expiry_date=? WHERE id=?");
            $stmt->execute([$title, $description, $target_audience, $class_id, $publish_date, $expiry_date, $id]);
            set_message('success', 'Notice updated successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Error updating notice.');
        }
    } else {
        set_message('error', 'Title and description are required.');
    }
}
$stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();
if (!$record) redirect('index.php');

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Notice</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label>Title *</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($record['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Description *</label>
                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($record['description']) ?></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Target Audience</label>
                    <select name="target_audience" class="form-control" id="target_audience" required>
                        <option value="Everyone" <?= $record['target_audience']=='Everyone'?'selected':'' ?>>Everyone</option>
                        <option value="Teachers" <?= $record['target_audience']=='Teachers'?'selected':'' ?>>Teachers</option>
                        <option value="Students" <?= $record['target_audience']=='Students'?'selected':'' ?>>Students</option>
                        <option value="Specific Class" <?= $record['target_audience']=='Specific Class'?'selected':'' ?>>Specific Class</option>
                    </select>
                </div>
                <div class="col-md-6" id="class_id_div" style="display:<?= $record['target_audience']=='Specific Class'?'block':'none' ?>;">
                    <label>Select Class</label>
                    <select name="class_id" class="form-control">
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= $class['id']==$record['class_id']?'selected':'' ?>><?= htmlspecialchars($class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Publish Date *</label>
                    <input type="date" name="publish_date" class="form-control" value="<?= htmlspecialchars($record['publish_date']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Expiry Date (Optional)</label>
                    <input type="date" name="expiry_date" class="form-control" value="<?= htmlspecialchars($record['expiry_date']) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Notice</button>
        </form>
    </div>
</div>
<script>
document.getElementById('target_audience').addEventListener('change', function() {
    if(this.value === 'Specific Class') {
        document.getElementById('class_id_div').style.display = 'block';
    } else {
        document.getElementById('class_id_div').style.display = 'none';
    }
});
</script>
<?php include '../../includes/footer.php'; ?>
