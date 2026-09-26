<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Assignments';

$stmt = $pdo->query("SELECT a.*, c.name as class_name, sec.name as section_name, s.name as subject_name, t.first_name, t.last_name FROM assignments a JOIN classes c ON a.class_id = c.id JOIN sections sec ON a.section_id = sec.id JOIN subjects s ON a.subject_id = s.id JOIN teachers t ON a.teacher_id = t.id ORDER BY a.id DESC");
$records = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Assignments</h1>
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
                        <th>Title</th>
                        <th>Class - Section</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Due Date</th>
                        <th>Attachment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= $record['id'] ?></td>
                        <td><?= htmlspecialchars($record['title']) ?></td>
                        <td><?= htmlspecialchars($record['class_name'] . ' - ' . $record['section_name']) ?></td>
                        <td><?= htmlspecialchars($record['subject_name']) ?></td>
                        <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                        <td><?= htmlspecialchars($record['due_date']) ?></td>
                        <td>
                            <?php if (!empty($record['attachment'])): ?>
                                <a href="../../uploads/assignments/<?= htmlspecialchars($record['attachment']) ?>" target="_blank" class="btn btn-info btn-sm" title="View Attachment"><i class="fas fa-paperclip"></i></a>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $record['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $record['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center">No assignments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
