<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
$page_title = 'Edit Event';
$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $event_date = sanitize($_POST['event_date'] ?? '');
    $start_time = !empty($_POST['start_time']) ? sanitize($_POST['start_time']) : null;
    $end_time = !empty($_POST['end_time']) ? sanitize($_POST['end_time']) : null;
    $location = sanitize($_POST['location'] ?? '');
    
    if (!empty($title) && !empty($description) && !empty($event_date)) {
        try {
            $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, start_time=?, end_time=?, location=? WHERE id=?");
            $stmt->execute([$title, $description, $event_date, $start_time, $end_time, $location, $id]);
            set_message('success', 'Event updated successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            set_message('error', 'Error updating event.');
        }
    } else {
        set_message('error', 'Title, description, and date are required.');
    }
}
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();
if (!$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Event</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label>Event Title *</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($record['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Description *</label>
                <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($record['description']) ?></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Event Date *</label>
                    <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($record['event_date']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Start Time (Optional)</label>
                    <input type="time" name="start_time" class="form-control" value="<?= htmlspecialchars($record['start_time'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label>End Time (Optional)</label>
                    <input type="time" name="end_time" class="form-control" value="<?= htmlspecialchars($record['end_time'] ?? '') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label>Location (Optional)</label>
                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($record['location'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Event</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
