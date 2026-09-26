<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'Timetable';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Class Timetable</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="alert alert-info">Your class timetable is currently being generated. Please check back later.</div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>