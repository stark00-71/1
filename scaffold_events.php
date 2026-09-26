<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\events\\';

$index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage Events';

\$stmt = \$pdo->query(\"SELECT e.*, u.username as author FROM events e JOIN users u ON e.created_by = u.id ORDER BY event_date DESC\");
\$events = \$stmt->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Events</h1>
    <a href=\"create.php\" class=\"d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm\">
        <i class=\"fas fa-plus fa-sm text-white-50\"></i> Add New Event
    </a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover\" width=\"100%\" cellspacing=\"0\">
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
                    <?php foreach (\$events as \$event): ?>
                    <tr>
                        <td><?= htmlspecialchars(\$event['title']) ?></td>
                        <td><?= date('M d, Y', strtotime(\$event['event_date'])) ?></td>
                        <td><?= \$event['start_time'] ? date('h:i A', strtotime(\$event['start_time'])) . (\$event['end_time'] ? ' - ' . date('h:i A', strtotime(\$event['end_time'])) : '') : 'TBA' ?></td>
                        <td><?= htmlspecialchars(\$event['location'] ?? 'TBA') ?></td>
                        <td>
                            <a href=\"edit.php?id=<?= \$event['id'] ?>\" class=\"btn btn-warning btn-sm\" title=\"Edit\"><i class=\"fas fa-edit\"></i></a>
                            <a href=\"delete.php?id=<?= \$event['id'] ?>\" class=\"btn btn-danger btn-sm\" title=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this event?');\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty(\$events)): ?>
                    <tr><td colspan=\"5\" class=\"text-center\">No events found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
if(!is_dir($base)) mkdir($base, 0777, true);
file_put_contents($base . 'index.php', $index);

$create = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Add Event';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$title = sanitize(\$_POST['title'] ?? '');
    \$description = sanitize(\$_POST['description'] ?? '');
    \$event_date = sanitize(\$_POST['event_date'] ?? '');
    \$start_time = !empty(\$_POST['start_time']) ? sanitize(\$_POST['start_time']) : null;
    \$end_time = !empty(\$_POST['end_time']) ? sanitize(\$_POST['end_time']) : null;
    \$location = sanitize(\$_POST['location'] ?? '');
    \$created_by = \$_SESSION['user_id'];
    
    if (!empty(\$title) && !empty(\$description) && !empty(\$event_date)) {
        try {
            \$stmt = \$pdo->prepare(\"INSERT INTO events (title, description, event_date, start_time, end_time, location, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)\");
            \$stmt->execute([\$title, \$description, \$event_date, \$start_time, \$end_time, \$location, \$created_by]);
            set_message('success', 'Event added successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error adding event.');
        }
    } else {
        set_message('error', 'Title, description, and date are required.');
    }
}
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add Event</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Event Title *</label>
                <input type=\"text\" name=\"title\" class=\"form-control\" required>
            </div>
            <div class=\"mb-3\">
                <label>Description *</label>
                <textarea name=\"description\" class=\"form-control\" rows=\"3\" required></textarea>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Event Date *</label>
                    <input type=\"date\" name=\"event_date\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-4\">
                    <label>Start Time (Optional)</label>
                    <input type=\"time\" name=\"start_time\" class=\"form-control\">
                </div>
                <div class=\"col-md-4\">
                    <label>End Time (Optional)</label>
                    <input type=\"time\" name=\"end_time\" class=\"form-control\">
                </div>
            </div>
            <div class=\"mb-3\">
                <label>Location (Optional)</label>
                <input type=\"text\" name=\"location\" class=\"form-control\">
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Save Event</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'create.php', $create);

$edit = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Edit Event';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$title = sanitize(\$_POST['title'] ?? '');
    \$description = sanitize(\$_POST['description'] ?? '');
    \$event_date = sanitize(\$_POST['event_date'] ?? '');
    \$start_time = !empty(\$_POST['start_time']) ? sanitize(\$_POST['start_time']) : null;
    \$end_time = !empty(\$_POST['end_time']) ? sanitize(\$_POST['end_time']) : null;
    \$location = sanitize(\$_POST['location'] ?? '');
    
    if (!empty(\$title) && !empty(\$description) && !empty(\$event_date)) {
        try {
            \$stmt = \$pdo->prepare(\"UPDATE events SET title=?, description=?, event_date=?, start_time=?, end_time=?, location=? WHERE id=?\");
            \$stmt->execute([\$title, \$description, \$event_date, \$start_time, \$end_time, \$location, \$id]);
            set_message('success', 'Event updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error updating event.');
        }
    } else {
        set_message('error', 'Title, description, and date are required.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM events WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit Event</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Event Title *</label>
                <input type=\"text\" name=\"title\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['title']) ?>\" required>
            </div>
            <div class=\"mb-3\">
                <label>Description *</label>
                <textarea name=\"description\" class=\"form-control\" rows=\"3\" required><?= htmlspecialchars(\$record['description']) ?></textarea>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Event Date *</label>
                    <input type=\"date\" name=\"event_date\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['event_date']) ?>\" required>
                </div>
                <div class=\"col-md-4\">
                    <label>Start Time (Optional)</label>
                    <input type=\"time\" name=\"start_time\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['start_time'] ?? '') ?>\">
                </div>
                <div class=\"col-md-4\">
                    <label>End Time (Optional)</label>
                    <input type=\"time\" name=\"end_time\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['end_time'] ?? '') ?>\">
                </div>
            </div>
            <div class=\"mb-3\">
                <label>Location (Optional)</label>
                <input type=\"text\" name=\"location\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['location'] ?? '') ?>\">
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Update Event</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'edit.php', $edit);

$delete = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$id = \$_GET['id'] ?? 0;
if (\$id) {
    try {
        \$stmt = \$pdo->prepare(\"DELETE FROM events WHERE id = ?\");
        \$stmt->execute([\$id]);
        set_message('success', 'Event deleted successfully.');
    } catch (PDOException \$e) {
        set_message('error', 'Error deleting event.');
    }
}
redirect('index.php');
";
file_put_contents($base . 'delete.php', $delete);

echo "Events scaffold generated";
?>
