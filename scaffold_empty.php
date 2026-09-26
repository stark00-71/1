<?php
$base_dir = 'C:\\xampp\\htdocs\\1\\admin\\';

function create_empty_module($module_name) {
    global $base_dir;
    $dir = $base_dir . $module_name . '\\';
    if(!is_dir($dir)) mkdir($dir, 0777, true);

    $index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage ".ucfirst($module_name)."';

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">".ucfirst($module_name)."</h1>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <p>This module is under development.</p>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
    file_put_contents($dir . 'index.php', $index);
}

create_empty_module('leaves');
create_empty_module('messages');
create_empty_module('reports');

echo "Empty modules scaffolded.";
?>
