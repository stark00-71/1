<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Admins';

$stmt = $pdo->query("SELECT a.*, u.email, u.username, r.name as role_name FROM admins a JOIN users u ON a.user_id = u.id LEFT JOIN roles r ON a.role_id = r.id ORDER BY a.first_name, a.last_name");
$admins = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Admins</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Admin
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) ?></td>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                        <td>
                            <?php if ($admin['is_super_admin'] == 1): ?>
                                <span class="badge bg-primary text-white">Super Admin</span>
                            <?php elseif ($admin['role_name']): ?>
                                <span class="badge bg-info text-white"><?= htmlspecialchars($admin['role_name']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary text-white">No Role</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($admin['phone'] ?? '') ?></td>
                        <td>
                            <?php if ($admin['is_super_admin'] == 1 && !($_SESSION['is_super_admin'] ?? ($_SESSION['user_id'] == 1))): ?>
                                <!-- Cannot edit Super Admin -->
                            <?php else: ?>
                                <a href="edit.php?id=<?= $admin['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                <?php if ($admin['user_id'] != $_SESSION['user_id']): ?>
                                    <a href="delete.php?id=<?= $admin['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this admin?');"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($admins)): ?>
                    <tr><td colspan="6" class="text-center">No admins found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
