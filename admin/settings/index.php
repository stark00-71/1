<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$page_title = 'System Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        set_message('danger', 'Invalid CSRF token.');
    } else {
        if (isset($_POST['update_settings'])) {
            $settings_to_update = [
                'school_name' => sanitize($_POST['school_name'] ?? ''),
                'school_email' => sanitize($_POST['school_email'] ?? ''),
                'school_phone' => sanitize($_POST['school_phone'] ?? ''),
                'school_address' => sanitize($_POST['school_address'] ?? ''),
                'academic_year' => sanitize($_POST['academic_year'] ?? ''),
                'primary_color' => sanitize($_POST['primary_color'] ?? '#3b82f6')
            ];

            // Handle background image uploads
            $bg_fields = ['admin_bg_image', 'teacher_bg_image', 'student_bg_image', 'login_bg_image'];
            foreach ($bg_fields as $bg_field) {
                if (isset($_FILES[$bg_field]) && $_FILES[$bg_field]['error'] === UPLOAD_ERR_OK) {
                    $upload_result = handle_secure_upload($bg_field, '../../uploads/');
                    if ($upload_result['success']) {
                        $settings_to_update[$bg_field] = $upload_result['filename'];
                    } else {
                        set_message('danger', "Background image upload failed for $bg_field: " . $upload_result['error']);
                    }
                }
            }

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                foreach ($settings_to_update as $key => $value) {
                    $stmt->execute([$key, $value, $value]);
                }
                $pdo->commit();
                set_message('success', 'General settings updated successfully.');
            } catch (PDOException $e) {
                $pdo->rollBack();
                set_message('danger', 'Error updating settings: ' . $e->getMessage());
            }
            redirect(BASE_URL . '/admin/settings/index.php');
        } elseif (isset($_POST['add_custom_setting'])) {
            $new_key = sanitize($_POST['new_setting_key'] ?? '');
            $new_value = sanitize($_POST['new_setting_value'] ?? '');
            
            if ($new_key) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
                    $stmt->execute([$new_key, $new_value]);
                    set_message('success', 'Custom setting added successfully.');
                } catch (PDOException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        set_message('danger', 'Error: Setting key already exists.');
                    } else {
                        set_message('danger', 'Error adding setting: ' . $e->getMessage());
                    }
                }
            } else {
                set_message('danger', 'Setting key is required.');
            }
            redirect(BASE_URL . '/admin/settings/index.php');
        } elseif (isset($_POST['update_raw_settings'])) {
            $raw_settings = $_POST['raw_settings'] ?? [];
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                foreach ($raw_settings as $key => $value) {
                    $stmt->execute([sanitize($value), sanitize($key)]);
                }
                $pdo->commit();
                set_message('success', 'Raw settings updated successfully.');
            } catch (PDOException $e) {
                $pdo->rollBack();
                set_message('danger', 'Error updating raw settings: ' . $e->getMessage());
            }
            redirect(BASE_URL . '/admin/settings/index.php');
        } elseif (isset($_POST['delete_setting_key'])) {
            $key = sanitize($_POST['delete_setting_key']);
            try {
                $stmt = $pdo->prepare("DELETE FROM settings WHERE setting_key = ?");
                $stmt->execute([$key]);
                set_message('success', 'Setting deleted successfully.');
            } catch (PDOException $e) {
                set_message('danger', 'Error deleting setting: ' . $e->getMessage());
            }
            redirect(BASE_URL . '/admin/settings/index.php');
        }
    }
}

// Fetch all settings for the raw editor
$all_settings_stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_key");
$all_settings = $all_settings_stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Settings</h1>
</div>

<?php display_message(); ?>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">General Settings</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="mb-3">
                        <label for="school_name" class="form-label">School Name</label>
                        <input type="text" class="form-control" id="school_name" name="school_name" value="<?php echo htmlspecialchars(get_setting($pdo, 'school_name', 'SCM - School Management System')); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="school_email" class="form-label">School Email</label>
                        <input type="email" class="form-control" id="school_email" name="school_email" value="<?php echo htmlspecialchars(get_setting($pdo, 'school_email', 'admin@example.com')); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="school_phone" class="form-label">School Phone</label>
                        <input type="text" class="form-control" id="school_phone" name="school_phone" value="<?php echo htmlspecialchars(get_setting($pdo, 'school_phone', '')); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="school_address" class="form-label">School Address</label>
                        <textarea class="form-control" id="school_address" name="school_address" rows="3"><?php echo htmlspecialchars(get_setting($pdo, 'school_address', '')); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="academic_year" class="form-label">Current Academic Year</label>
                        <input type="text" class="form-control" id="academic_year" name="academic_year" value="<?php echo htmlspecialchars(get_setting($pdo, 'academic_year', '2023-2024')); ?>">
                    </div>

                    <h6 class="m-0 font-weight-bold text-primary mt-4 mb-3">Appearance</h6>

                    <div class="mb-4">
                        <label for="primary_color" class="form-label fw-bold">Primary Theme Color</label>
                        <div class="d-flex align-items-center">
                            <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars(get_setting($pdo, 'primary_color', '#3b82f6')); ?>" title="Choose your color">
                            <span class="ms-3 text-muted small">This color is used for buttons, active sidebar links, and accents.</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Login Page Background Image</label>
                        <?php 
                        $login_bg_image = get_setting($pdo, 'login_bg_image', '');
                        if ($login_bg_image): ?>
                            <div class="mb-3">
                                <p class="text-muted small mb-1">Current Background:</p>
                                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($login_bg_image) ?>" alt="Background" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="login_bg_image" name="login_bg_image" accept="image/jpeg, image/png, image/jpg">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Admin Dashboard Background Image</label>
                        <?php 
                        $admin_bg_image = get_setting($pdo, 'admin_bg_image', '');
                        if ($admin_bg_image): ?>
                            <div class="mb-3">
                                <p class="text-muted small mb-1">Current Background:</p>
                                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($admin_bg_image) ?>" alt="Background" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="admin_bg_image" name="admin_bg_image" accept="image/jpeg, image/png, image/jpg">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Teacher Dashboard Background Image</label>
                        <?php 
                        $teacher_bg_image = get_setting($pdo, 'teacher_bg_image', '');
                        if ($teacher_bg_image): ?>
                            <div class="mb-3">
                                <p class="text-muted small mb-1">Current Background:</p>
                                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($teacher_bg_image) ?>" alt="Background" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="teacher_bg_image" name="teacher_bg_image" accept="image/jpeg, image/png, image/jpg">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Student Dashboard Background Image</label>
                        <?php 
                        $student_bg_image = get_setting($pdo, 'student_bg_image', '');
                        if ($student_bg_image): ?>
                            <div class="mb-3">
                                <p class="text-muted small mb-1">Current Background:</p>
                                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($student_bg_image) ?>" alt="Background" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="student_bg_image" name="student_bg_image" accept="image/jpeg, image/png, image/jpg">
                        <div class="form-text text-muted mt-2">
                            Upload high-resolution images (JPG/PNG) to use as backgrounds for the respective dashboards. Leave blank to keep current.
                        </div>
                    </div>

                    <button type="submit" name="update_settings" class="btn btn-primary">Save General Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Power Edit: Add New Setting</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="new_setting_key" class="form-label">Setting Key</label>
                            <input type="text" class="form-control" id="new_setting_key" name="new_setting_key" placeholder="e.g. maintenance_mode" required>
                        </div>
                        <div class="col-md-7 mb-3">
                            <label for="new_setting_value" class="form-label">Setting Value</label>
                            <input type="text" class="form-control" id="new_setting_value" name="new_setting_value" placeholder="Value">
                        </div>
                    </div>
                    <button type="submit" name="add_custom_setting" class="btn btn-danger btn-sm">Add Setting</button>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Power Edit: Raw Settings Table</h6>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_settings as $setting): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($setting['setting_key']); ?></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="raw_settings[<?php echo htmlspecialchars($setting['setting_key']); ?>]" value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('Delete this setting?')) { document.getElementById('delete_form_<?php echo htmlspecialchars($setting['setting_key']); ?>').submit(); }">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($all_settings)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No settings found in database.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="update_raw_settings" class="btn btn-danger btn-sm mt-2">Save All Changes</button>
                </form>
                
                <!-- Hidden forms for deletion -->
                <?php foreach ($all_settings as $setting): ?>
                <form id="delete_form_<?php echo htmlspecialchars($setting['setting_key']); ?>" action="" method="POST" style="display: none;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="delete_setting_key" value="<?php echo htmlspecialchars($setting['setting_key']); ?>">
                </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
