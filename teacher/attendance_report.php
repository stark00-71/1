<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');

$date = $_GET['date'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT a.*, s.first_name, s.last_name, s.admission_no, c.name as class_name, sec.name as section_name 
                       FROM attendance a 
                       JOIN students s ON a.student_id = s.id 
                       JOIN classes c ON a.class_id = c.id 
                       JOIN sections sec ON a.section_id = sec.id 
                       WHERE a.date = ? 
                       ORDER BY c.name, sec.name, s.first_name");
$stmt->execute([$date]);
$records = $stmt->fetchAll();

$page_title = 'Attendance Report';
include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Attendance Report</h1>
    <a href="attendance.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Mark Attendance
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form method="GET" class="form-inline">
            <label class="mr-2">Select Date:</label>
            <input type="date" name="date" class="form-control mr-2" value="<?= htmlspecialchars($date) ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Class & Section</th>
                        <th>Student Name</th>
                        <th>Admission No</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['class_name'] . ' - ' . $row['section_name']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['admission_no']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'Present'): ?>
                                <span class="text-success font-weight-bold">Present</span>
                            <?php elseif ($row['status'] === 'Absent'): ?>
                                <span class="text-danger font-weight-bold">Absent</span>
                            <?php else: ?>
                                <span class="text-warning font-weight-bold">Late</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($records)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No attendance records found for this date.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
