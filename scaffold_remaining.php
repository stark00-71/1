<?php
$base_dir = 'C:\\xampp\\htdocs\\1\\admin\\';

function create_simple_crud($module_name, $table_name, $singular_name) {
    global $base_dir;
    $dir = $base_dir . $module_name . '\\';
    if(!is_dir($dir)) mkdir($dir, 0777, true);

    $index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage ".ucfirst($module_name)."';

\$stmt = \$pdo->query(\"SELECT * FROM {$table_name} ORDER BY id DESC\");
\$records = \$stmt->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">".ucfirst($module_name)."</h1>
    <a href=\"#\" class=\"d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm\">
        <i class=\"fas fa-plus fa-sm text-white-50\"></i> Add New
    </a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <p>This module is under development. Here you can manage {$module_name}.</p>
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover\" width=\"100%\" cellspacing=\"0\">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (\$records as \$record): ?>
                    <tr>
                        <td><?= \$record['id'] ?></td>
                        <td>Data for <?= \$record['id'] ?></td>
                        <td>
                            <a href=\"#\" class=\"btn btn-warning btn-sm\" title=\"Edit\"><i class=\"fas fa-edit\"></i></a>
                            <a href=\"#\" class=\"btn btn-danger btn-sm\" title=\"Delete\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty(\$records)): ?>
                    <tr><td colspan=\"3\" class=\"text-center\">No {$module_name} found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
    file_put_contents($dir . 'index.php', $index);
}

// Check tables and create if missing
$pdo = new PDO("mysql:host=localhost;dbname=school_db", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$pdo->exec("CREATE TABLE IF NOT EXISTS exams (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, class_id INT, start_date DATE, end_date DATE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$pdo->exec("CREATE TABLE IF NOT EXISTS results (id INT AUTO_INCREMENT PRIMARY KEY, exam_id INT, student_id INT, marks VARCHAR(50), remarks TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$pdo->exec("CREATE TABLE IF NOT EXISTS assignments (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT, class_id INT, due_date DATE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$pdo->exec("CREATE TABLE IF NOT EXISTS fees (id INT AUTO_INCREMENT PRIMARY KEY, student_id INT, amount DECIMAL(10,2), due_date DATE, status ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$pdo->exec("CREATE TABLE IF NOT EXISTS settings (id INT AUTO_INCREMENT PRIMARY KEY, setting_key VARCHAR(100) UNIQUE, setting_value TEXT)");

create_simple_crud('exams', 'exams', 'exam');
create_simple_crud('results', 'results', 'result');
create_simple_crud('assignments', 'assignments', 'assignment');
create_simple_crud('fees', 'fees', 'fee');

// Settings is a bit different
$settings_dir = $base_dir . 'settings\\';
if(!is_dir($settings_dir)) mkdir($settings_dir, 0777, true);
$settings_index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'System Settings';
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Settings</h1>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <p>Settings module under development.</p>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($settings_dir . 'index.php', $settings_index);

echo "Remaining modules scaffolded successfully.";
?>
