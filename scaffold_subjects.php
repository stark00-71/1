<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\subjects\\';

$index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage Subjects';

\$stmt = \$pdo->query(\"SELECT * FROM subjects ORDER BY name\");
\$subjects = \$stmt->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Subjects</h1>
    <a href=\"create.php\" class=\"d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm\">
        <i class=\"fas fa-plus fa-sm text-white-50\"></i> Add New Subject
    </a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover\" width=\"100%\" cellspacing=\"0\">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (\$subjects as \$subject): ?>
                    <tr>
                        <td><?= htmlspecialchars(\$subject['code']) ?></td>
                        <td><?= htmlspecialchars(\$subject['name']) ?></td>
                        <td><?= date('M d, Y', strtotime(\$subject['created_at'])) ?></td>
                        <td>
                            <a href=\"edit.php?id=<?= \$subject['id'] ?>\" class=\"btn btn-warning btn-sm\" title=\"Edit\"><i class=\"fas fa-edit\"></i></a>
                            <a href=\"delete.php?id=<?= \$subject['id'] ?>\" class=\"btn btn-danger btn-sm\" title=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this subject?');\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty(\$subjects)): ?>
                    <tr><td colspan=\"4\" class=\"text-center\">No subjects found.</td></tr>
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
\$page_title = 'Add Subject';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$name = sanitize(\$_POST['name'] ?? '');
    \$code = sanitize(\$_POST['code'] ?? '');
    
    if (!empty(\$name) && !empty(\$code)) {
        \$stmt = \$pdo->prepare(\"INSERT INTO subjects (name, code) VALUES (?, ?)\");
        try {
            \$stmt->execute([\$name, \$code]);
            set_message('success', 'Subject added successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error: Subject code may already exist.');
        }
    } else {
        set_message('error', 'All fields are required.');
    }
}
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add Subject</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Subject Name</label>
                <input type=\"text\" name=\"name\" class=\"form-control\" required>
            </div>
            <div class=\"mb-3\">
                <label>Subject Code</label>
                <input type=\"text\" name=\"code\" class=\"form-control\" required>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Save</button>
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
\$page_title = 'Edit Subject';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$name = sanitize(\$_POST['name'] ?? '');
    \$code = sanitize(\$_POST['code'] ?? '');
    
    if (!empty(\$name) && !empty(\$code)) {
        \$stmt = \$pdo->prepare(\"UPDATE subjects SET name = ?, code = ? WHERE id = ?\");
        try {
            \$stmt->execute([\$name, \$code, \$id]);
            set_message('success', 'Subject updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error: Subject code may already exist.');
        }
    } else {
        set_message('error', 'All fields are required.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM subjects WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit Subject</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Subject Name</label>
                <input type=\"text\" name=\"name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['name']) ?>\" required>
            </div>
            <div class=\"mb-3\">
                <label>Subject Code</label>
                <input type=\"text\" name=\"code\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['code']) ?>\" required>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Update</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'edit.php', $edit);

echo "Subjects scaffold generated";
?>
