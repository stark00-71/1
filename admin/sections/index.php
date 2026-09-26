<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Sections';

$stmt = $pdo->query("SELECT s.*, c.name as class_name FROM sections s JOIN classes c ON s.class_id = c.id ORDER BY c.name, s.name");
$sections = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sections</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Section
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Section Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $section): ?>
                    <tr>
                        <td><?= htmlspecialchars($section['class_name']) ?></td>
                        <td><?= htmlspecialchars($section['name']) ?></td>
                        <td><?= date('M d, Y', strtotime($section['created_at'])) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $section['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $section['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this section?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sections)): ?>
                    <tr><td colspan="4" class="text-center">No sections found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
