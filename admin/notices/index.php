<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Notices';

$stmt = $pdo->query("SELECT n.*, u.username as author FROM notices n JOIN users u ON n.created_by = u.id ORDER BY publish_date DESC");
$notices = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Notices</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Notice
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Audience</th>
                        <th>Publish Date</th>
                        <th>Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notices as $notice): ?>
                    <tr>
                        <td><?= htmlspecialchars($notice['title']) ?></td>
                        <td><?= htmlspecialchars($notice['target_audience']) ?></td>
                        <td><?= date('M d, Y', strtotime($notice['publish_date'])) ?></td>
                        <td><?= htmlspecialchars($notice['author']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $notice['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $notice['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this notice?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($notices)): ?>
                    <tr><td colspan="5" class="text-center">No notices found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
