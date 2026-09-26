<?php
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'config/auth.php';

if (!isset($_SESSION['user_id'])) {
    redirect(BASE_URL . '/auth/login.php');
}

$page_title = 'All Notifications';
$user_id = $_SESSION['user_id'];

// Mark all as read if requested
if (isset($_POST['mark_all_read'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->execute([$user_id]);
        set_message('success', 'All notifications marked as read.');
        redirect(BASE_URL . '/notifications.php');
    }
}

// Fetch all notifications with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_notifications = $total_stmt->fetchColumn();
$total_pages = ceil($total_notifications / $limit);

// We need to set the working directory for includes to work correctly if they rely on relative paths
chdir(__DIR__ . '/includes');
include 'header.php';
chdir(__DIR__);
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Your Notifications</h6>
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <button type="submit" name="mark_all_read" class="btn btn-sm btn-outline-primary">Mark All as Read</button>
        </form>
    </div>
    <div class="card-body">
        <?php if (empty($notifications)): ?>
            <p class="text-center text-muted my-5">You have no notifications.</p>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notification): ?>
                    <a href="<?= BASE_URL ?>/read_notification.php?id=<?= $notification['id'] ?>" class="list-group-item list-group-item-action <?= $notification['is_read'] ? '' : 'bg-light font-weight-bold' ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-primary"><?= htmlspecialchars($notification['title']) ?></h5>
                            <small class="text-muted"><?= date('M d, Y h:i A', strtotime($notification['created_at'])) ?></small>
                        </div>
                        <p class="mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                        <?php if (!$notification['is_read']): ?>
                            <span class="badge bg-danger rounded-pill">New</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
chdir(__DIR__ . '/includes');
include 'footer.php'; 
chdir(__DIR__);
?>
