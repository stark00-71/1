<?php
$dirs = [
    'teachers', 'parents', 'classes', 'sections', 'subjects', 'attendance',
    'exams', 'results', 'assignments', 'timetable', 'fees', 'notices',
    'events', 'leaves', 'messages', 'reports', 'settings'
];

$base_path = 'C:\\xampp\\htdocs\\1\\admin\\';

foreach ($dirs as $dir) {
    $dir_path = $base_path . $dir;
    if (!is_dir($dir_path)) {
        mkdir($dir_path, 0777, true);
    }
    
    $index_path = $dir_path . '\\index.php';
    if (!file_exists($index_path)) {
        $title = ucfirst($dir);
        $content = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireRole('admin');
\$page_title = 'Manage {$title}';
include '../../includes/header.php';
?>

<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">{$title}</h1>
</div>

<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <p>{$title} module is under construction.</p>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
";
        file_put_contents($index_path, $content);
    }
}
echo "All admin placeholder pages created successfully!";
?>
