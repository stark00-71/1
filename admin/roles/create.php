<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');

$page_title = 'Create Role';

// Define available permissions
$available_permissions = [
    'manage_students' => 'Manage Students',
    'manage_teachers' => 'Manage Teachers',
    'manage_admins' => 'Manage Admins',
    'manage_parents' => 'Manage Parents',
    'manage_classes' => 'Manage Classes & Sections',
    'manage_subjects' => 'Manage Subjects',
    'manage_attendance' => 'Manage Attendance',
    'manage_exams' => 'Manage Exams & Results',
    'manage_assignments' => 'Manage Assignments',
    'manage_timetable' => 'Manage Timetable',
    'manage_fees' => 'Manage Fees',
    'manage_notices' => 'Manage Notices & Events',
    'manage_leaves' => 'Manage Leaves',
    'manage_messages' => 'Manage Messages',
    'view_reports' => 'View Reports',
    'manage_settings' => 'Manage Settings',
    'manage_roles' => 'Manage Roles'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $selected_permissions = $_POST['permissions'] ?? [];
    
    if (!empty($name)) {
        try {
            // Filter out any permissions that aren't in the available list
            $valid_permissions = array_intersect(array_keys($available_permissions), $selected_permissions);
            $permissions_json = json_encode(array_values($valid_permissions));
            
            $stmt = $pdo->prepare("INSERT INTO roles (name, permissions) VALUES (?, ?)");
            $stmt->execute([$name, $permissions_json]);
            
            set_message('success', 'Role created successfully.');
            redirect('index.php');
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                set_message('danger', 'A role with this name already exists.');
            } else {
                set_message('danger', 'Database error: ' . $e->getMessage());
            }
        }
    } else {
        set_message('danger', 'Role name is required.');
    }
}

include '../../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Create Role</h1>
    <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Back</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label>Role Name *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g., Accountant, Librarian">
            </div>
            
            <h5 class="text-primary mt-4">Permissions</h5>
            <hr>
            <div class="row">
                <?php foreach ($available_permissions as $key => $label): ?>
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $key ?>" id="perm_<?= $key ?>">
                        <label class="form-check-label" for="perm_<?= $key ?>">
                            <?= $label ?>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn btn-primary mt-3">Save Role</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
