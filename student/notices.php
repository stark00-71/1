<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'Notices';

$stmt = $pdo->query("SELECT * FROM notices WHERE target_audience IN ('Everyone', 'Students') ORDER BY created_at DESC");
$notices = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Notices & Announcements</h1>
</div>

<div class="row">
    <?php foreach ($notices as $notice): ?>
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= htmlspecialchars($notice['title']) ?></h6>
                    <span class="badge bg-secondary"><?= date('M d, Y', strtotime($notice['created_at'])) ?></span>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($notice['description'])) ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($notices)): ?>
        <div class="col-12">
            <div class="alert alert-info">No notices available at this time.</div>
        </div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>