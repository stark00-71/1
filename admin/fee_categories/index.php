<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Fee Categories';

$stmt = $pdo->query("SELECT * FROM fee_categories ORDER BY name ASC");
$records = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Fee Categories</h1>
    <div>
        <a href="../fees/create.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Assign Fees
        </a>
        <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Category
        </a>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= $record['id'] ?></td>
                        <td><?= htmlspecialchars($record['name']) ?></td>
                        <td><?= htmlspecialchars($record['description']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $record['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $record['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr><td colspan="4" class="text-center">No categories found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
