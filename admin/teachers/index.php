<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Teachers';

$stmt = $pdo->query("SELECT t.*, u.email FROM teachers t JOIN users u ON t.user_id = u.id ORDER BY t.first_name, t.last_name");
$teachers = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Teachers</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Teacher
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Role/Designation</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?= htmlspecialchars($teacher['employee_id']) ?></td>
                        <td><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td>
                        <td><?= htmlspecialchars($teacher['designation'] ?? 'Teacher') ?></td>
                        <td><?= htmlspecialchars($teacher['email']) ?></td>
                        <td><?= htmlspecialchars($teacher['phone']) ?></td>
                        <td><?= date('M d, Y', strtotime($teacher['joining_date'])) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $teacher['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $teacher['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this teacher?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($teachers)): ?>
                    <tr><td colspan="7" class="text-center">No teachers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
