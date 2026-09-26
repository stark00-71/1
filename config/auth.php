<?php
// config/auth.php
session_start();

/**
 * Check if the user is logged in.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require a user to be logged in. Redirect to login if not.
 */
function requireLogin() {
    if (!is_logged_in()) {
        redirect(BASE_URL . '/auth/login.php');
    }
}

/**
 * Require the logged in user to have a specific role.
 */
function requireRole($role) {
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        // Logged in, but wrong role. Redirect to appropriate dashboard or error.
        if ($_SESSION['user_role'] === 'admin') redirect(BASE_URL . '/admin/dashboard.php');
        if ($_SESSION['user_role'] === 'teacher') redirect(BASE_URL . '/teacher/dashboard.php');
        if ($_SESSION['user_role'] === 'student') redirect(BASE_URL . '/student/dashboard.php');
        exit();
    }
}

/**
 * Check if the current admin has a specific permission.
 */
function hasPermission($power) {
    if ($_SESSION['user_role'] !== 'admin') return false;
    
    // Super admins have all permissions
    if (isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin']) return true;
    
    if (isset($_SESSION['admin_permissions']) && is_array($_SESSION['admin_permissions'])) {
        return in_array($power, $_SESSION['admin_permissions']);
    }
    return false;
}

/**
 * Perform login logic.
 */
function attempt_login($pdo, $email_or_username, $password, $role) {
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role, status FROM users WHERE (email = :identifier OR username = :identifier) AND role = :role LIMIT 1");
    $stmt->execute(['identifier' => $email_or_username, 'role' => $role]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is inactive.'];
        }

        // Prevent session fixation
        session_regenerate_id(true);

        // Setup session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        // Get specific role details (e.g. name, profile photo)
        if ($role === 'admin') {
            $stmt = $pdo->prepare("SELECT a.first_name, a.last_name, a.profile_photo, a.is_super_admin, r.permissions 
                                   FROM admins a 
                                   LEFT JOIN roles r ON a.role_id = r.id 
                                   WHERE a.user_id = ?");
            $stmt->execute([$user['id']]);
            $details = $stmt->fetch();
            $_SESSION['name'] = $details['first_name'] . ' ' . $details['last_name'];
            $_SESSION['profile_photo'] = $details['profile_photo'];
            $_SESSION['is_super_admin'] = $details['is_super_admin'];
            
            // Set permissions
            $permissions = [];
            if ($details['permissions']) {
                $permissions = json_decode($details['permissions'], true) ?? [];
            }
            $_SESSION['admin_permissions'] = $permissions;
        } elseif ($role === 'teacher') {
            $stmt = $pdo->prepare("SELECT id as teacher_id, first_name, last_name, profile_photo FROM teachers WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $details = $stmt->fetch();
            $_SESSION['teacher_id'] = $details['teacher_id'];
            $_SESSION['name'] = $details['first_name'] . ' ' . $details['last_name'];
            $_SESSION['profile_photo'] = $details['profile_photo'];
        } elseif ($role === 'student') {
            $stmt = $pdo->prepare("SELECT id as student_id, first_name, last_name, profile_photo FROM students WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $details = $stmt->fetch();
            $_SESSION['student_id'] = $details['student_id'];
            $_SESSION['name'] = $details['first_name'] . ' ' . $details['last_name'];
            $_SESSION['profile_photo'] = $details['profile_photo'];
        }

        return ['success' => true];
    }

    return ['success' => false, 'message' => 'Invalid credentials or role mismatch.'];
}

/**
 * Logout the user
 */
function logout() {
    session_unset();
    session_destroy();
    redirect(BASE_URL . '/auth/login.php');
}
?>
