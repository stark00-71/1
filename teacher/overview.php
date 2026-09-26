<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('teacher');
$page_title = 'Overview';

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">My Students</div>
                                <div class="h3 mb-0 font-weight-bold text-gray-800">--</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle bg-primary-soft"><i class="fas fa-users"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Classes</div>
                                <div class="h3 mb-0 font-weight-bold text-gray-800">--</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle bg-success-soft"><i class="fas fa-chalkboard"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<div class="card dashboard-card mb-4 mt-4">
    <div class="card-body">
        <p>Welcome to the teacher portal. Modules are currently being built.</p>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
