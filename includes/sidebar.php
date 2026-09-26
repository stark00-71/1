<?php 
$current_page = $_SERVER['REQUEST_URI']; 
$sidebar_school_name = get_setting($pdo, 'school_name', 'SCM - SCHOOL MANAGEMENT');
$name_parts = explode(' - ', $sidebar_school_name, 2);
$main_name = $name_parts[0];
$sub_name = isset($name_parts[1]) ? $name_parts[1] : '';
?>
<nav id="sidebar" class="text-white">
    <div class="sidebar-header p-4 text-center">
        <a href="<?= BASE_URL ?>/index.php" class="text-white text-decoration-none d-flex flex-column align-items-center">
            <i class="fas fa-graduation-cap fa-2x mb-2 text-primary"></i>
            <h5 class="mb-0 fw-bold tracking-wide"><?= htmlspecialchars($main_name) ?></h5>
            <?php if ($sub_name): ?>
            <small class="text-white-50 mt-1" style="font-size: 0.7rem; letter-spacing: 1px;"><?= htmlspecialchars(strtoupper($sub_name)) ?></small>
            <?php endif; ?>
        </a>
    </div>

    <ul class="list-unstyled components p-2">
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <li class="<?= strpos($current_page, '/admin/dashboard.php') !== false ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin/dashboard.php"><i class="fas fa-fw fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li class="<?= strpos($current_page, '/admin/overview.php') !== false ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin/overview.php"><i class="fas fa-fw fa-chart-pie me-2"></i> Overview</a>
            </li>
            <?php 
                $is_users_active = strpos($current_page, '/admin/admins/') !== false || strpos($current_page, '/admin/roles/') !== false || strpos($current_page, '/admin/students/') !== false || strpos($current_page, '/admin/teachers/') !== false || strpos($current_page, '/admin/parents/') !== false;
            ?>
            <li class="<?= $is_users_active ? 'active' : '' ?>">
                <a href="#usersSubmenu" data-bs-toggle="collapse" aria-expanded="<?= $is_users_active ? 'true' : 'false' ?>" class="manage-users-btn <?= $is_users_active ? '' : 'collapsed' ?>" style="text-decoration: none;">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Manage Users</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="collapse list-unstyled <?= $is_users_active ? 'show' : '' ?>" id="usersSubmenu" style="padding-left: 1.5rem;">
                    <li class="<?= strpos($current_page, '/admin/roles/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/roles/"><i class="fas fa-fw fa-key me-2"></i> Roles & Permissions</a></li>
                    <li class="<?= strpos($current_page, '/admin/admins/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/admins/"><i class="fas fa-fw fa-user-shield me-2"></i> Admins</a></li>
                    <li class="<?= strpos($current_page, '/admin/students/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/students/" class="students-active"><i class="fas fa-fw fa-user-graduate me-2"></i> Students</a></li>
                    <li class="<?= strpos($current_page, '/admin/teachers/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/teachers/"><i class="fas fa-fw fa-chalkboard-teacher me-2"></i> Teachers</a></li>
                    <li class="<?= strpos($current_page, '/admin/parents/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/parents/"><i class="fas fa-fw fa-users me-2"></i> Parents</a></li>
                </ul>
            </li>
            <li class="<?= strpos($current_page, '/admin/classes/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/classes/"><i class="fas fa-fw fa-chalkboard me-2"></i> Classes</a></li>
            <li class="<?= strpos($current_page, '/admin/sections/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/sections/"><i class="fas fa-fw fa-layer-group me-2"></i> Sections</a></li>
            <li class="<?= strpos($current_page, '/admin/subjects/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/subjects/"><i class="fas fa-fw fa-book me-2"></i> Subjects</a></li>
            <li class="<?= strpos($current_page, '/admin/attendance/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/attendance/"><i class="fas fa-fw fa-calendar-check me-2"></i> Attendance</a></li>
            <li class="<?= strpos($current_page, '/admin/exams/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/exams/"><i class="fas fa-fw fa-file-alt me-2"></i> Exams</a></li>
            <li class="<?= strpos($current_page, '/admin/results/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/results/"><i class="fas fa-fw fa-chart-bar me-2"></i> Results</a></li>
            <li class="<?= strpos($current_page, '/admin/assignments/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/assignments/"><i class="fas fa-fw fa-tasks me-2"></i> Assignments</a></li>
            <li class="<?= strpos($current_page, '/admin/timetable/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/timetable/"><i class="fas fa-fw fa-table me-2"></i> Timetable</a></li>
            <li class="<?= strpos($current_page, '/admin/fees/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/fees/"><i class="fas fa-fw fa-money-bill-wave me-2"></i> Fees</a></li>
            <li class="<?= strpos($current_page, '/admin/notices/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/notices/"><i class="fas fa-fw fa-bullhorn me-2"></i> Notices</a></li>
            <li class="<?= strpos($current_page, '/admin/events/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/events/"><i class="fas fa-fw fa-calendar-alt me-2"></i> Events</a></li>
            <li class="<?= strpos($current_page, '/admin/leaves/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/leaves/"><i class="fas fa-fw fa-envelope-open-text me-2"></i> Leave Mgt</a></li>
            <li class="<?= strpos($current_page, '/admin/messages/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/messages/"><i class="fas fa-fw fa-comments me-2"></i> Messages</a></li>
            <li class="<?= strpos($current_page, '/admin/reports/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/reports/"><i class="fas fa-fw fa-chart-pie me-2"></i> Reports</a></li>
            <li class="<?= strpos($current_page, '/admin/settings/') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/admin/settings/"><i class="fas fa-fw fa-cogs me-2"></i> Settings</a></li>
        <?php elseif ($_SESSION['user_role'] === 'teacher'): ?>
            <li class="<?= strpos($current_page, '/teacher/dashboard.php') !== false ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/teacher/dashboard.php"><i class="fas fa-fw fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li class="<?= strpos($current_page, '/teacher/overview.php') !== false ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/teacher/overview.php"><i class="fas fa-fw fa-chart-pie me-2"></i> Overview</a>
            </li>
            <li class="<?= strpos($current_page, '/teacher/profile.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/profile.php"><i class="fas fa-fw fa-user me-2"></i> My Profile</a></li>
            <li class="<?= strpos($current_page, '/teacher/students.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/students.php"><i class="fas fa-fw fa-users me-2"></i> My Students</a></li>
            <li class="<?= strpos($current_page, '/teacher/attendance.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/attendance.php"><i class="fas fa-fw fa-calendar-check me-2"></i> Attendance</a></li>
            <li class="<?= strpos($current_page, '/teacher/assignments.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/assignments.php"><i class="fas fa-fw fa-tasks me-2"></i> Assignments</a></li>
            <li class="<?= strpos($current_page, '/teacher/exams.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/exams.php"><i class="fas fa-fw fa-file-alt me-2"></i> Exam Mgt</a></li>
            <li class="<?= strpos($current_page, '/teacher/results.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/results.php"><i class="fas fa-fw fa-chart-bar me-2"></i> Results</a></li>
            <li class="<?= strpos($current_page, '/teacher/timetable.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/timetable.php"><i class="fas fa-fw fa-table me-2"></i> My Timetable</a></li>
            <li class="<?= strpos($current_page, '/teacher/student_timetable.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/student_timetable.php"><i class="fas fa-fw fa-calendar-alt me-2"></i> Student Timetable</a></li>
            <li class="<?= strpos($current_page, '/teacher/fees.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/fees.php"><i class="fas fa-fw fa-money-bill-wave me-2"></i> Fees</a></li>
            <li class="<?= strpos($current_page, '/teacher/notices.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/notices.php"><i class="fas fa-fw fa-bullhorn me-2"></i> Notices</a></li>
            <li class="<?= strpos($current_page, '/teacher/leaves.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/leaves.php"><i class="fas fa-fw fa-envelope-open-text me-2"></i> Leave Request</a></li>
            <li class="<?= strpos($current_page, '/teacher/messages.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/teacher/messages.php"><i class="fas fa-fw fa-comments me-2"></i> Messages</a></li>
        <?php elseif ($_SESSION['user_role'] === 'student'): ?>
            <li class="<?= strpos($current_page, '/student/dashboard.php') !== false ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/student/dashboard.php"><i class="fas fa-fw fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li class="<?= strpos($current_page, '/student/overview.php') !== false ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/student/overview.php"><i class="fas fa-fw fa-chart-pie me-2"></i> Overview</a>
            </li>
            <li class="<?= strpos($current_page, '/student/profile.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/profile.php"><i class="fas fa-fw fa-user me-2"></i> My Profile</a></li>
            <li class="<?= strpos($current_page, '/student/id_card.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/id_card.php"><i class="fas fa-fw fa-id-card me-2"></i> ID Card</a></li>
            <li class="<?= strpos($current_page, '/student/attendance.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/attendance.php"><i class="fas fa-fw fa-calendar-check me-2"></i> Attendance</a></li>
            <li class="<?= strpos($current_page, '/student/assignments.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/assignments.php"><i class="fas fa-fw fa-tasks me-2"></i> Assignments</a></li>
            <li class="<?= strpos($current_page, '/student/exams.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/exams.php"><i class="fas fa-fw fa-file-alt me-2"></i> Exams</a></li>
            <li class="<?= strpos($current_page, '/student/results.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/results.php"><i class="fas fa-fw fa-chart-bar me-2"></i> Results</a></li>
            <li class="<?= strpos($current_page, '/student/timetable.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/timetable.php"><i class="fas fa-fw fa-table me-2"></i> Timetable</a></li>
            <li class="<?= strpos($current_page, '/student/fees.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/fees.php"><i class="fas fa-fw fa-money-bill-wave me-2"></i> Fees</a></li>
            <li class="<?= strpos($current_page, '/student/notices.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/notices.php"><i class="fas fa-fw fa-bullhorn me-2"></i> Notices</a></li>
            <li class="<?= strpos($current_page, '/student/events.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/events.php"><i class="fas fa-fw fa-calendar-alt me-2"></i> Events</a></li>
            <li class="<?= strpos($current_page, '/student/leaves.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/leaves.php"><i class="fas fa-fw fa-envelope-open-text me-2"></i> Leave Request</a></li>
            <li class="<?= strpos($current_page, '/student/messages.php') !== false ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/student/messages.php"><i class="fas fa-fw fa-comments me-2"></i> Messages</a></li>
        <?php endif; ?>
    </ul>
</nav>
