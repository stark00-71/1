<?php
// Fetch notifications for the current user
$nav_user_id = $_SESSION['user_id'] ?? 0;
$nav_stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$nav_stmt->execute([$nav_user_id]);
$nav_notifications = $nav_stmt->fetchAll();

$nav_unread_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$nav_unread_stmt->execute([$nav_user_id]);
$nav_unread_count = $nav_unread_stmt->fetchColumn();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-transparent topbar mb-4 static-top">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ms-auto">
            <!-- Real-time Clock -->
            <li class="nav-item d-none d-md-flex align-items-center mx-3">
                <div class="d-flex align-items-center px-3 py-2 rounded-pill bg-transparent" style="min-width: 220px;">
                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-25 rounded-circle me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-clock text-primary fs-5" id="clockIcon"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center lh-sm">
                        <span id="realTimeDate" class="text-muted text-uppercase" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px;">Loading...</span>
                        <span id="realTimeClock" class="fw-bold" style="font-size: 0.95rem; font-family: 'Courier New', Courier, monospace; letter-spacing: 0.5px;">--:--:--</span>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle position-relative" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <?php if ($nav_unread_count > 0): ?>
                    <span class="position-absolute top-25 start-75 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 10px; height: 10px;">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="alertsDropdown">
                    <li><h6 class="dropdown-header">Alerts Center</h6></li>
                    <?php if (empty($nav_notifications)): ?>
                        <li><a class="dropdown-item text-center small text-gray-500" href="#">No new notifications</a></li>
                    <?php else: ?>
                        <?php foreach ($nav_notifications as $notification): ?>
                        <li>
                            <a class="dropdown-item d-flex align-items-center <?= $notification['is_read'] ? '' : 'bg-light' ?>" href="<?= BASE_URL ?>/read_notification.php?id=<?= $notification['id'] ?>">
                                <div class="me-3">
                                    <div class="icon-circle bg-primary text-white p-2 rounded-circle">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500"><?= date('F d, Y h:i A', strtotime($notification['created_at'])) ?></div>
                                    <span class="<?= $notification['is_read'] ? '' : 'font-weight-bold' ?>"><?= htmlspecialchars($notification['title']) ?></span>
                                    <div class="small text-gray-700"><?= htmlspecialchars($notification['message']) ?></div>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <li><a class="dropdown-item text-center small text-gray-500" href="<?= BASE_URL ?>/notifications.php">Show All Alerts</a></li>
                </ul>
            </li>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="me-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
                    <img class="img-profile rounded-circle" src="<?= !empty($_SESSION['profile_photo']) ? BASE_URL . '/uploads/' . $_SESSION['user_role'] . 's/' . $_SESSION['profile_photo'] : BASE_URL . '/assets/images/default-avatar.svg' ?>" width="32" height="32" alt="Profile">
                </a>
                <!-- Dropdown - User Information -->
                <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                    <li>
                        <?php 
                        $profile_url = BASE_URL;
                        if($_SESSION['user_role'] == 'admin') $profile_url .= '/admin/profile.php';
                        elseif($_SESSION['user_role'] == 'teacher') $profile_url .= '/teacher/profile.php';
                        else $profile_url .= '/student/profile.php';
                        ?>
                        <a class="dropdown-item" href="<?= $profile_url ?>">
                            <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Profile
                        </a>
                    </li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i> Settings
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/auth/logout.php">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout
                    </a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
