<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\sections\\';

$index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage Sections';

\$stmt = \$pdo->query(\"SELECT s.*, c.name as class_name FROM sections s JOIN classes c ON s.class_id = c.id ORDER BY c.name, s.name\");
\$sections = \$stmt->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Sections</h1>
    <a href=\"create.php\" class=\"d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm\">
        <i class=\"fas fa-plus fa-sm text-white-50\"></i> Add New Section
    </a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover\" width=\"100%\" cellspacing=\"0\">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Section Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (\$sections as \$section): ?>
                    <tr>
                        <td><?= htmlspecialchars(\$section['class_name']) ?></td>
                        <td><?= htmlspecialchars(\$section['name']) ?></td>
                        <td><?= date('M d, Y', strtotime(\$section['created_at'])) ?></td>
                        <td>
                            <a href=\"edit.php?id=<?= \$section['id'] ?>\" class=\"btn btn-warning btn-sm\" title=\"Edit\"><i class=\"fas fa-edit\"></i></a>
                            <a href=\"delete.php?id=<?= \$section['id'] ?>\" class=\"btn btn-danger btn-sm\" title=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this section?');\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty(\$sections)): ?>
                    <tr><td colspan=\"4\" class=\"text-center\">No sections found.</td></tr>
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
\$page_title = 'Add Section';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$name = sanitize(\$_POST['name'] ?? '');
    \$class_id = (int)(\$_POST['class_id'] ?? 0);
    
    if (!empty(\$name) && \$class_id > 0) {
        \$stmt = \$pdo->prepare(\"INSERT INTO sections (class_id, name) VALUES (?, ?)\");
        try {
            \$stmt->execute([\$class_id, \$name]);
            set_message('success', 'Section added successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error adding section.');
        }
    } else {
        set_message('error', 'All fields are required.');
    }
}
\$classes = \$pdo->query(\"SELECT * FROM classes ORDER BY name\")->fetchAll();
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add Section</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Class</label>
                <select name=\"class_id\" class=\"form-control\" required>
                    <option value=\"\">Select Class</option>
                    <?php foreach (\$classes as \$class): ?>
                    <option value=\"<?= \$class['id'] ?>\"><?= htmlspecialchars(\$class['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class=\"mb-3\">
                <label>Section Name</label>
                <input type=\"text\" name=\"name\" class=\"form-control\" required>
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
\$page_title = 'Edit Section';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$name = sanitize(\$_POST['name'] ?? '');
    \$class_id = (int)(\$_POST['class_id'] ?? 0);
    
    if (!empty(\$name) && \$class_id > 0) {
        \$stmt = \$pdo->prepare(\"UPDATE sections SET class_id = ?, name = ? WHERE id = ?\");
        try {
            \$stmt->execute([\$class_id, \$name, \$id]);
            set_message('success', 'Section updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error updating section.');
        }
    } else {
        set_message('error', 'All fields are required.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM sections WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

\$classes = \$pdo->query(\"SELECT * FROM classes ORDER BY name\")->fetchAll();
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit Section</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Class</label>
                <select name=\"class_id\" class=\"form-control\" required>
                    <option value=\"\">Select Class</option>
                    <?php foreach (\$classes as \$class): ?>
                    <option value=\"<?= \$class['id'] ?>\" <?= \$class['id'] == \$record['class_id'] ? 'selected' : '' ?>><?= htmlspecialchars(\$class['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class=\"mb-3\">
                <label>Section Name</label>
                <input type=\"text\" name=\"name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['name']) ?>\" required>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Update</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'edit.php', $edit);

echo "Sections scaffold generated";
?>
