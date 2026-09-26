<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Results';

$stmt = $pdo->query("SELECT er.*, e.name as exam_name, s.first_name, s.last_name FROM exam_results er JOIN exams e ON er.exam_id = e.id JOIN students s ON er.student_id = s.id ORDER BY er.id DESC");
$records = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Results</h1>
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
                        <th>Exam</th>
                        <th>Student</th>
                        <th>Marks Obtained</th>
                        <th>Grade</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= $record['id'] ?></td>
                        <td><?= htmlspecialchars($record['exam_name']) ?></td>
                        <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                        <td><?= htmlspecialchars($record['marks_obtained']) ?></td>
                        <td><?= htmlspecialchars($record['grade']) ?></td>
                        <td><?= htmlspecialchars($record['remarks']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $record['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $record['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="7" class="text-center">No results found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
