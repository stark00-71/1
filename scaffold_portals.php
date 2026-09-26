<?php
$base_dir = 'C:\\xampp\\htdocs\\1\\';

function create_role_module($role, $module_name) {
    global $base_dir;
    $dir = $base_dir . $role . '\\';
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    // Capitalize module name for display, strip .php
    $display_name = ucwords(str_replace(['.php', '_'], ['', ' '], $module_name));

    $content = "<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('$role');
\$page_title = '$display_name';

include '../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">$display_name</h1>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <p>This module is under development.</p>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
";

    // Dashboard gets special stats content
    if ($module_name === 'dashboard.php') {
        $stats = "";
        if ($role === 'teacher') {
            $stats = "
        <div class=\"row\">
            <div class=\"col-xl-3 col-md-6 mb-4\">
                <div class=\"card border-left-primary shadow h-100 py-2\">
                    <div class=\"card-body\">
                        <div class=\"row no-gutters align-items-center\">
                            <div class=\"col mr-2\">
                                <div class=\"text-xs font-weight-bold text-primary text-uppercase mb-1\">My Students</div>
                                <div class=\"h5 mb-0 font-weight-bold text-gray-800\">--</div>
                            </div>
                            <div class=\"col-auto\">
                                <i class=\"fas fa-users fa-2x text-gray-300\"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=\"col-xl-3 col-md-6 mb-4\">
                <div class=\"card border-left-success shadow h-100 py-2\">
                    <div class=\"card-body\">
                        <div class=\"row no-gutters align-items-center\">
                            <div class=\"col mr-2\">
                                <div class=\"text-xs font-weight-bold text-success text-uppercase mb-1\">Today's Classes</div>
                                <div class=\"h5 mb-0 font-weight-bold text-gray-800\">--</div>
                            </div>
                            <div class=\"col-auto\">
                                <i class=\"fas fa-chalkboard fa-2x text-gray-300\"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
        } else if ($role === 'student') {
            $stats = "
        <div class=\"row\">
            <div class=\"col-xl-3 col-md-6 mb-4\">
                <div class=\"card border-left-info shadow h-100 py-2\">
                    <div class=\"card-body\">
                        <div class=\"row no-gutters align-items-center\">
                            <div class=\"col mr-2\">
                                <div class=\"text-xs font-weight-bold text-info text-uppercase mb-1\">Attendance</div>
                                <div class=\"h5 mb-0 font-weight-bold text-gray-800\">--%</div>
                            </div>
                            <div class=\"col-auto\">
                                <i class=\"fas fa-calendar-check fa-2x text-gray-300\"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=\"col-xl-3 col-md-6 mb-4\">
                <div class=\"card border-left-warning shadow h-100 py-2\">
                    <div class=\"card-body\">
                        <div class=\"row no-gutters align-items-center\">
                            <div class=\"col mr-2\">
                                <div class=\"text-xs font-weight-bold text-warning text-uppercase mb-1\">Pending Assignments</div>
                                <div class=\"h5 mb-0 font-weight-bold text-gray-800\">--</div>
                            </div>
                            <div class=\"col-auto\">
                                <i class=\"fas fa-tasks fa-2x text-gray-300\"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
        }
        $content = "<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('$role');
\$page_title = 'Dashboard';

include '../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Dashboard</h1>
</div>
$stats
<div class=\"card shadow mb-4 mt-4\">
    <div class=\"card-body\">
        <p>Welcome to the $role portal. Modules are currently being built.</p>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
";
    }

    file_put_contents($dir . $module_name, $content);
}

// Teacher Files
$teacher_files = ['dashboard.php', 'profile.php', 'students.php', 'attendance.php', 'assignments.php', 'exams.php', 'results.php', 'timetable.php', 'notices.php', 'leaves.php', 'messages.php'];
foreach ($teacher_files as $file) {
    create_role_module('teacher', $file);
}

// Student Files
$student_files = ['dashboard.php', 'profile.php', 'attendance.php', 'assignments.php', 'exams.php', 'results.php', 'timetable.php', 'fees.php', 'notices.php', 'events.php', 'leaves.php', 'messages.php'];
foreach ($student_files as $file) {
    create_role_module('student', $file);
}

echo "Scaffolded Teacher and Student portals.";
?>
