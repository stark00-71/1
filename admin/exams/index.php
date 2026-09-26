<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Exams';

$stmt = $pdo->query("SELECT e.*, c.name as class_name, s.name as subject_name FROM exams e JOIN classes c ON e.class_id = c.id JOIN subjects s ON e.subject_id = s.id ORDER BY e.id DESC");
$records = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Exams</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Max/Passing Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= $record['id'] ?></td>
                        <td><?= htmlspecialchars($record['name']) ?></td>
                        <td><?= htmlspecialchars($record['class_name']) ?></td>
                        <td><?= htmlspecialchars($record['subject_name']) ?></td>
                        <td><?= htmlspecialchars($record['type']) ?></td>
                        <td><?= htmlspecialchars($record['exam_date']) ?></td>
                        <td><?= htmlspecialchars($record['max_marks']) ?> / <?= htmlspecialchars($record['passing_marks']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $record['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $record['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center">No exams found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
