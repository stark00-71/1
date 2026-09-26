<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Exam Results';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Upload Results</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Select Exam</h6>
    </div>
    <div class="card-body">
        <form class="row g-3">
            <div class="col-md-4">
                <label>Exam</label>
                <select class="form-select"><option>Select Exam</option></select>
            </div>
            <div class="col-md-4">
                <label>Class</label>
                <select class="form-select"><option>Select Class</option></select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100">Fetch Students</button>
            </div>
        </form>
    </div>
</div>
<div class="alert alert-info">Select an exam and class to enter student marks.</div>
<?php include '../includes/footer.php'; ?>