<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');

$user_id = $_SESSION['user_id'];

// Fetch upcoming events
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC, start_time ASC");
$events = $stmt->fetchAll();

$page_title = 'Upcoming Events';
include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">School Events</h1>
</div>

<div class="row">
    <?php foreach ($events as $event): ?>
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><?= htmlspecialchars($event['title']) ?></h6>
                <span class="badge badge-info"><?= date('M d, Y', strtotime($event['event_date'])) ?></span>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                <hr>
                <div class="row text-sm">
                    <div class="col-6">
                        <i class="fas fa-clock text-gray-500 mr-2"></i>
                        <?= date('h:i A', strtotime($event['start_time'])) ?> - <?= date('h:i A', strtotime($event['end_time'])) ?>
                    </div>
                    <div class="col-6 text-right">
                        <i class="fas fa-map-marker-alt text-gray-500 mr-2"></i>
                        <?= htmlspecialchars($event['location']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($events)): ?>
    <div class="col-12">
        <div class="alert alert-info">
            There are no upcoming events scheduled at this time.
        </div>
    </div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
