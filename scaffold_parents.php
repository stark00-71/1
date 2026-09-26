<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\parents\\';

$index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage Parents';

\$stmt = \$pdo->query(\"SELECT * FROM parents ORDER BY father_name\");
\$parents = \$stmt->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Parents</h1>
    <a href=\"create.php\" class=\"d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm\">
        <i class=\"fas fa-plus fa-sm text-white-50\"></i> Add New Parent
    </a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover\" width=\"100%\" cellspacing=\"0\">
                <thead>
                    <tr>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (\$parents as \$parent): ?>
                    <tr>
                        <td><?= htmlspecialchars(\$parent['father_name']) ?></td>
                        <td><?= htmlspecialchars(\$parent['mother_name']) ?></td>
                        <td><?= htmlspecialchars(\$parent['email']) ?></td>
                        <td><?= htmlspecialchars(\$parent['phone']) ?></td>
                        <td>
                            <a href=\"edit.php?id=<?= \$parent['id'] ?>\" class=\"btn btn-warning btn-sm\" title=\"Edit\"><i class=\"fas fa-edit\"></i></a>
                            <a href=\"delete.php?id=<?= \$parent['id'] ?>\" class=\"btn btn-danger btn-sm\" title=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this parent?');\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty(\$parents)): ?>
                    <tr><td colspan=\"5\" class=\"text-center\">No parents found.</td></tr>
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
\$page_title = 'Add Parent';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$father_name = sanitize(\$_POST['father_name'] ?? '');
    \$mother_name = sanitize(\$_POST['mother_name'] ?? '');
    \$email = sanitize(\$_POST['email'] ?? '');
    \$phone = sanitize(\$_POST['phone'] ?? '');
    
    if (!empty(\$father_name)) {
        try {
            \$stmt = \$pdo->prepare(\"INSERT INTO parents (father_name, mother_name, email, phone) VALUES (?, ?, ?, ?)\");
            \$stmt->execute([\$father_name, \$mother_name, \$email, \$phone]);
            set_message('success', 'Parent added successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error: Email may already exist.');
        }
    } else {
        set_message('error', 'Father name is required.');
    }
}
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add Parent</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Father Name *</label>
                    <input type=\"text\" name=\"father_name\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Mother Name</label>
                    <input type=\"text\" name=\"mother_name\" class=\"form-control\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Email Address</label>
                    <input type=\"email\" name=\"email\" class=\"form-control\">
                </div>
                <div class=\"col-md-6\">
                    <label>Phone Number</label>
                    <input type=\"text\" name=\"phone\" class=\"form-control\">
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary mt-3\">Save Parent</button>
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
\$page_title = 'Edit Parent';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$father_name = sanitize(\$_POST['father_name'] ?? '');
    \$mother_name = sanitize(\$_POST['mother_name'] ?? '');
    \$email = sanitize(\$_POST['email'] ?? '');
    \$phone = sanitize(\$_POST['phone'] ?? '');
    
    if (!empty(\$father_name)) {
        \$stmt = \$pdo->prepare(\"UPDATE parents SET father_name=?, mother_name=?, email=?, phone=? WHERE id=?\");
        try {
            \$stmt->execute([\$father_name, \$mother_name, \$email, \$phone, \$id]);
            set_message('success', 'Parent updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error: Email may already exist.');
        }
    } else {
        set_message('error', 'Father name is required.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM parents WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit Parent</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Father Name *</label>
                    <input type=\"text\" name=\"father_name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['father_name']) ?>\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Mother Name</label>
                    <input type=\"text\" name=\"mother_name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['mother_name']) ?>\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Email Address</label>
                    <input type=\"email\" name=\"email\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['email']) ?>\">
                </div>
                <div class=\"col-md-6\">
                    <label>Phone Number</label>
                    <input type=\"text\" name=\"phone\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['phone']) ?>\">
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary mt-3\">Update Parent</button>
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
        \$stmt = \$pdo->prepare(\"DELETE FROM parents WHERE id = ?\");
        \$stmt->execute([\$id]);
        set_message('success', 'Parent deleted successfully.');
    } catch (PDOException \$e) {
        set_message('error', 'Cannot delete this record because it is referenced elsewhere.');
    }
}
redirect('index.php');
";
file_put_contents($base . 'delete.php', $delete);

echo "Parents scaffold generated";
?>
