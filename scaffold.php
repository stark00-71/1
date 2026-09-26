<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\';

function make_crud($module, $singular, $table) {
    global $base;
    $dir = $base . $module . '\\';
    
    // create.php
    $create = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Add $singular';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$name = sanitize(\$_POST['name'] ?? '');
    if (!empty(\$name)) {
        \$stmt = \$pdo->prepare(\"INSERT INTO $table (name) VALUES (?)\");
        try {
            \$stmt->execute([\$name]);
            set_message('success', '$singular added successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error: Name may already exist.');
        }
    } else {
        set_message('error', 'Name is required.');
    }
}
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add $singular</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Name</label>
                <input type=\"text\" name=\"name\" class=\"form-control\" required>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Save</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
    file_put_contents($dir . 'create.php', $create);

    // edit.php
    $edit = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Edit $singular';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$name = sanitize(\$_POST['name'] ?? '');
    if (!empty(\$name)) {
        \$stmt = \$pdo->prepare(\"UPDATE $table SET name = ? WHERE id = ?\");
        try {
            \$stmt->execute([\$name, \$id]);
            set_message('success', '$singular updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error: Name may already exist.');
        }
    } else {
        set_message('error', 'Name is required.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM $table WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit $singular</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <div class=\"mb-3\">
                <label>Name</label>
                <input type=\"text\" name=\"name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['name']) ?>\" required>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Update</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
    file_put_contents($dir . 'edit.php', $edit);

    // delete.php
    $delete = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$id = \$_GET['id'] ?? 0;
if (\$id) {
    \$stmt = \$pdo->prepare(\"DELETE FROM $table WHERE id = ?\");
    try {
        \$stmt->execute([\$id]);
        set_message('success', '$singular deleted successfully.');
    } catch (PDOException \$e) {
        set_message('error', 'Cannot delete this record because it is referenced elsewhere.');
    }
}
redirect('index.php');
";
    file_put_contents($dir . 'delete.php', $delete);
}

// Scaffold for Classes
make_crud('classes', 'Class', 'classes');

// For sections, it needs class_id. And Subjects needs code. I will do them separately or modify them after.
echo "Scaffold generated";
?>
