<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Manage Events';

$stmt = $pdo->query("SELECT e.*, u.username as author FROM events e JOIN users u ON e.created_by = u.id ORDER BY event_date DESC");
$events = $stmt->fetchAll();

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Events</h1>
    <a href="create.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New Event
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= date('M d, Y', strtotime($event['event_date'])) ?></td>
                        <td><?= $event['start_time'] ? date('h:i A', strtotime($event['start_time'])) . ($event['end_time'] ? ' - ' . date('h:i A', strtotime($event['end_time'])) : '') : 'TBA' ?></td>
                        <td><?= htmlspecialchars($event['location'] ?? 'TBA') ?></td>
                        <td>
                            <a href="edit.php?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $event['id'] ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this event?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($events)): ?>
                    <tr><td colspan="5" class="text-center">No events found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
