<?php
// admin/students/index.php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireRole('admin');

$page_title = 'Manage Students';

// Fetch students with their class and section
$stmt = $pdo->query("
    SELECT s.id, s.admission_no, s.roll_number, s.first_name, s.last_name, s.gender, s.status,
           c.name as class_name, sec.name as section_name, u.email
    FROM students s
    LEFT JOIN classes c ON s.class_id = c.id
    LEFT JOIN sections sec ON s.section_id = sec.id
    LEFT JOIN users u ON s.user_id = u.id
    ORDER BY c.name, sec.name, s.roll_number
");
$students = $stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Students</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Student
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Admission No</th>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Class & Section</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['admission_no']) ?></td>
                        <td><?= htmlspecialchars($student['roll_number']) ?></td>
                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['class_name'] . ' - ' . $student['section_name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td>
                            <?php if ($student['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="id_card.php?id=<?= $student['id'] ?>" class="btn btn-primary btn-sm" title="ID Card"><i class="fas fa-id-card"></i></a>
                            <a href="view.php?id=<?= $student['id'] ?>" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>
                            <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $student['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this student?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
