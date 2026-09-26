<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Parents';

$stmt = $pdo->query("SELECT p.*, u.email FROM parents p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.father_name");
$parents = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Parents</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Parent
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parents as $parent): ?>
                    <tr>
                        <td><?= htmlspecialchars($parent['father_name']) ?></td>
                        <td><?= htmlspecialchars($parent['mother_name']) ?></td>
                        <td><?= htmlspecialchars($parent['email']) ?></td>
                        <td><?= htmlspecialchars($parent['phone']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $parent['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $parent['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this parent?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($parents)): ?>
                    <tr><td colspan="5" class="text-center">No parents found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
