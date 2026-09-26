<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My Attendance';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Attendance</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="alert alert-info">No attendance records found for the current month.</div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="2" class="text-center">No records available.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>