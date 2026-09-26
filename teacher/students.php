<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'My Students';

$stmt = $pdo->query("SELECT s.*, c.name as class_name, sec.name as section_name 
                     FROM students s 
                     LEFT JOIN classes c ON s.class_id = c.id 
                     LEFT JOIN sections sec ON s.section_id = sec.id 
                     ORDER BY c.name, sec.name, s.first_name");
$students = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Students</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Admission No</th>
                        <th>Name</th>
                        <th>Class - Section</th>
                        <th>Roll No</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['admission_no']) ?></td>
                            <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td><?= htmlspecialchars($s['class_name'] . ' - ' . $s['section_name']) ?></td>
                            <td><?= htmlspecialchars($s['roll_number']) ?></td>
                            <td><?= htmlspecialchars($s['gender']) ?></td>
                            <td>
                                <a href="id_card.php?id=<?= $s['id'] ?>" class="btn btn-primary btn-sm" title="ID Card"><i class="fas fa-id-card"></i> ID Card</a>
                                <a href="edit_student.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm" title="Edit Student"><i class="fas fa-edit"></i> Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>