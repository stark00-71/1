<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'Dashboard';

include '../includes/header.php';
?>

<div class="row" style="min-height: 70vh; align-items: center; justify-content: center;">
    <div class="col-md-8 text-center">
        <!-- Dashboard is empty to show the background image. Stats are in the Overview page. -->
        <h2 class="text-white mb-4 slide-in-up" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8);">Welcome to the Student Portal</h2>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
