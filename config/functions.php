<?php
// config/functions.php

/**
 * Sanitize user input to prevent XSS.
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token and store it in session.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token.
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect utility function.
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Set a flash message.
 */
function set_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'text' => $message
    ];
}

/**
 * Display flash message if it exists.
 */
function display_message() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $text = $_SESSION['flash_message']['text'];
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$text}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['flash_message']);
    }
}

/**
 * Get setting value.
 */
function get_setting($pdo, $key, $default = '') {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

/**
 * Handle secure file uploads.
 * Returns the uploaded file name on success, or an array with an error message.
 */
function handle_secure_upload($file_input, $upload_dir) {
    if (!isset($_FILES[$file_input]) || $_FILES[$file_input]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or an error occurred.'];
    }

    $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip'];
    $file = $_FILES[$file_input];
    
    // Get file extension and validate
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_extensions)];
    }

    // Ensure directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate secure filename
    $safe_filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
    $new_filename = time() . '_' . bin2hex(random_bytes(4)) . '_' . $safe_filename;
    $target_path = rtrim($upload_dir, '/') . '/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $new_filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}
?>
