<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $school_name_setting = get_setting($pdo, 'school_name', 'SCM'); ?>
    <title><?= htmlspecialchars($page_title ?? 'Dashboard') ?> - <?= htmlspecialchars($school_name_setting) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <?php
    $admin_bg = get_setting($pdo, 'admin_bg_image');
    $teacher_bg = get_setting($pdo, 'teacher_bg_image');
    $student_bg = get_setting($pdo, 'student_bg_image');
    $current_role = $_SESSION['user_role'] ?? '';
    $primary_color = get_setting($pdo, 'primary_color', '#3b82f6');
    ?>
    <style>
        :root {
            --bs-primary: <?= htmlspecialchars($primary_color) ?> !important;
        }

        <?php if ($admin_bg && $current_role === 'admin'): ?>
        .theme-admin {
            background-image: url('<?= BASE_URL ?>/uploads/<?= htmlspecialchars($admin_bg) ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        <?php endif; ?>
        
        <?php if ($teacher_bg && $current_role === 'teacher'): ?>
        .theme-teacher {
            background-image: url('<?= BASE_URL ?>/uploads/<?= htmlspecialchars($teacher_bg) ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        <?php endif; ?>
        
        <?php if ($student_bg && $current_role === 'student'): ?>
        .theme-student {
            background-image: url('<?= BASE_URL ?>/uploads/<?= htmlspecialchars($student_bg) ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        <?php endif; ?>
    </style>
    <?php
    $has_bg = false;
    if ($admin_bg && $current_role === 'admin') $has_bg = true;
    if ($teacher_bg && $current_role === 'teacher') $has_bg = true;
    if ($student_bg && $current_role === 'student') $has_bg = true;
    
    $body_classes = "theme-" . htmlspecialchars($current_role ?: 'admin');
    if ($has_bg) {
        $body_classes .= " has-bg-image";
    }
    ?>
</head>
<body class="<?= $body_classes ?>">
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content">
            <!-- Navbar -->
            <?php include 'navbar.php'; ?>
            
            <div class="container-fluid p-4">
                <?php display_message(); ?>
