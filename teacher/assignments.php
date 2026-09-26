<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Assignments';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Assignments</h1>
    <button class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Create Assignment</button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Submissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" class="text-center">No assignments found.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>