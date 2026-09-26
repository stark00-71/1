<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\notices\\';

$index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage Notices';

\$stmt = \$pdo->query(\"SELECT n.*, u.username as author FROM notices n JOIN users u ON n.created_by = u.id ORDER BY publish_date DESC\");
\$notices = \$stmt->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Notices</h1>
    <a href=\"create.php\" class=\"d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm\">
        <i class=\"fas fa-plus fa-sm text-white-50\"></i> Add New Notice
    </a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover\" width=\"100%\" cellspacing=\"0\">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Audience</th>
                        <th>Publish Date</th>
                        <th>Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (\$notices as \$notice): ?>
                    <tr>
                        <td><?= htmlspecialchars(\$notice['title']) ?></td>
                        <td><?= htmlspecialchars(\$notice['target_audience']) ?></td>
                        <td><?= date('M d, Y', strtotime(\$notice['publish_date'])) ?></td>
                        <td><?= htmlspecialchars(\$notice['author']) ?></td>
                        <td>
                            <a href=\"edit.php?id=<?= \$notice['id'] ?>\" class=\"btn btn-warning btn-sm\" title=\"Edit\"><i class=\"fas fa-edit\"></i></a>
                            <a href=\"delete.php?id=<?= \$notice['id'] ?>\" class=\"btn btn-danger btn-sm\" title=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this notice?');\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty(\$notices)): ?>
                    <tr><td colspan=\"5\" class=\"text-center\">No notices found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'index.php', $index);

$create = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Add Notice';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$title = sanitize(\$_POST['title'] ?? '');
    \$description = sanitize(\$_POST['description'] ?? '');
    \$target_audience = sanitize(\$_POST['target_audience'] ?? 'Everyone');
    \$class_id = !empty(\$_POST['class_id']) ? (int)\$_POST['class_id'] : null;
    \$publish_date = sanitize(\$_POST['publish_date'] ?? date('Y-m-d'));
    \$expiry_date = !empty(\$_POST['expiry_date']) ? sanitize(\$_POST['expiry_date']) : null;
    \$created_by = \$_SESSION['user_id'];
    
    if (!empty(\$title) && !empty(\$description)) {
        try {
            \$stmt = \$pdo->prepare(\"INSERT INTO notices (title, description, target_audience, class_id, publish_date, expiry_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)\");
            \$stmt->execute([\$title, \$description, \$target_audience, \$class_id, \$publish_date, \$expiry_date, \$created_by]);
            set_message('success', 'Notice published successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error publishing notice.');
        }
    } else {
        set_message('error', 'Title and description are required.');
    }
}
\$classes = \$pdo->query(\"SELECT * FROM classes ORDER BY name\")->fetchAll();
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add Notice</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Title *</label>
                <input type=\"text\" name=\"title\" class=\"form-control\" required>
            </div>
            <div class=\"mb-3\">
                <label>Description *</label>
                <textarea name=\"description\" class=\"form-control\" rows=\"4\" required></textarea>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Target Audience</label>
                    <select name=\"target_audience\" class=\"form-control\" id=\"target_audience\" required>
                        <option value=\"Everyone\">Everyone</option>
                        <option value=\"Teachers\">Teachers</option>
                        <option value=\"Students\">Students</option>
                        <option value=\"Specific Class\">Specific Class</option>
                    </select>
                </div>
                <div class=\"col-md-6\" id=\"class_id_div\" style=\"display:none;\">
                    <label>Select Class</label>
                    <select name=\"class_id\" class=\"form-control\">
                        <option value=\"\">Select Class</option>
                        <?php foreach (\$classes as \$class): ?>
                        <option value=\"<?= \$class['id'] ?>\"><?= htmlspecialchars(\$class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Publish Date *</label>
                    <input type=\"date\" name=\"publish_date\" class=\"form-control\" value=\"<?= date('Y-m-d') ?>\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Expiry Date (Optional)</label>
                    <input type=\"date\" name=\"expiry_date\" class=\"form-control\">
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Publish</button>
        </form>
    </div>
</div>
<script>
document.getElementById('target_audience').addEventListener('change', function() {
    if(this.value === 'Specific Class') {
        document.getElementById('class_id_div').style.display = 'block';
    } else {
        document.getElementById('class_id_div').style.display = 'none';
    }
});
</script>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'create.php', $create);

$edit = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Edit Notice';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$title = sanitize(\$_POST['title'] ?? '');
    \$description = sanitize(\$_POST['description'] ?? '');
    \$target_audience = sanitize(\$_POST['target_audience'] ?? 'Everyone');
    \$class_id = !empty(\$_POST['class_id']) ? (int)\$_POST['class_id'] : null;
    \$publish_date = sanitize(\$_POST['publish_date'] ?? date('Y-m-d'));
    \$expiry_date = !empty(\$_POST['expiry_date']) ? sanitize(\$_POST['expiry_date']) : null;
    
    if (\$target_audience !== 'Specific Class') \$class_id = null;
    
    if (!empty(\$title) && !empty(\$description)) {
        try {
            \$stmt = \$pdo->prepare(\"UPDATE notices SET title=?, description=?, target_audience=?, class_id=?, publish_date=?, expiry_date=? WHERE id=?\");
            \$stmt->execute([\$title, \$description, \$target_audience, \$class_id, \$publish_date, \$expiry_date, \$id]);
            set_message('success', 'Notice updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error updating notice.');
        }
    } else {
        set_message('error', 'Title and description are required.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM notices WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

\$classes = \$pdo->query(\"SELECT * FROM classes ORDER BY name\")->fetchAll();
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit Notice</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Title *</label>
                <input type=\"text\" name=\"title\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['title']) ?>\" required>
            </div>
            <div class=\"mb-3\">
                <label>Description *</label>
                <textarea name=\"description\" class=\"form-control\" rows=\"4\" required><?= htmlspecialchars(\$record['description']) ?></textarea>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Target Audience</label>
                    <select name=\"target_audience\" class=\"form-control\" id=\"target_audience\" required>
                        <option value=\"Everyone\" <?= \$record['target_audience']=='Everyone'?'selected':'' ?>>Everyone</option>
                        <option value=\"Teachers\" <?= \$record['target_audience']=='Teachers'?'selected':'' ?>>Teachers</option>
                        <option value=\"Students\" <?= \$record['target_audience']=='Students'?'selected':'' ?>>Students</option>
                        <option value=\"Specific Class\" <?= \$record['target_audience']=='Specific Class'?'selected':'' ?>>Specific Class</option>
                    </select>
                </div>
                <div class=\"col-md-6\" id=\"class_id_div\" style=\"display:<?= \$record['target_audience']=='Specific Class'?'block':'none' ?>;\">
                    <label>Select Class</label>
                    <select name=\"class_id\" class=\"form-control\">
                        <option value=\"\">Select Class</option>
                        <?php foreach (\$classes as \$class): ?>
                        <option value=\"<?= \$class['id'] ?>\" <?= \$class['id']==\$record['class_id']?'selected':'' ?>><?= htmlspecialchars(\$class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Publish Date *</label>
                    <input type=\"date\" name=\"publish_date\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['publish_date']) ?>\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Expiry Date (Optional)</label>
                    <input type=\"date\" name=\"expiry_date\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['expiry_date']) ?>\">
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Update Notice</button>
        </form>
    </div>
</div>
<script>
document.getElementById('target_audience').addEventListener('change', function() {
    if(this.value === 'Specific Class') {
        document.getElementById('class_id_div').style.display = 'block';
    } else {
        document.getElementById('class_id_div').style.display = 'none';
    }
});
</script>
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
        \$stmt = \$pdo->prepare(\"DELETE FROM notices WHERE id = ?\");
        \$stmt->execute([\$id]);
        set_message('success', 'Notice deleted successfully.');
    } catch (PDOException \$e) {
        set_message('error', 'Error deleting notice.');
    }
}
redirect('index.php');
";
file_put_contents($base . 'delete.php', $delete);

echo "Notices scaffold generated";
?>
