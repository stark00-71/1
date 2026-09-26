<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\teachers\\';

$index = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Manage Teachers';

\$stmt = \$pdo->query(\"SELECT * FROM teachers ORDER BY first_name, last_name\");
\$teachers = \$stmt->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Teachers</h1>
    <a href=\"create.php\" class=\"d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm\">
        <i class=\"fas fa-plus fa-sm text-white-50\"></i> Add New Teacher
    </a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover\" width=\"100%\" cellspacing=\"0\">
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (\$teachers as \$teacher): ?>
                    <tr>
                        <td><?= htmlspecialchars(\$teacher['employee_id']) ?></td>
                        <td><?= htmlspecialchars(\$teacher['first_name'] . ' ' . \$teacher['last_name']) ?></td>
                        <td><?= htmlspecialchars(\$teacher['email']) ?></td>
                        <td><?= htmlspecialchars(\$teacher['phone']) ?></td>
                        <td><?= date('M d, Y', strtotime(\$teacher['joining_date'])) ?></td>
                        <td>
                            <a href=\"edit.php?id=<?= \$teacher['id'] ?>\" class=\"btn btn-warning btn-sm\" title=\"Edit\"><i class=\"fas fa-edit\"></i></a>
                            <a href=\"delete.php?id=<?= \$teacher['id'] ?>\" class=\"btn btn-danger btn-sm\" title=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this teacher?');\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty(\$teachers)): ?>
                    <tr><td colspan=\"6\" class=\"text-center\">No teachers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'index.php', $index);

$create = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Add Teacher';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$first_name = sanitize(\$_POST['first_name'] ?? '');
    \$last_name = sanitize(\$_POST['last_name'] ?? '');
    \$employee_id = sanitize(\$_POST['employee_id'] ?? '');
    \$email = sanitize(\$_POST['email'] ?? '');
    \$phone = sanitize(\$_POST['phone'] ?? '');
    \$qualification = sanitize(\$_POST['qualification'] ?? '');
    \$joining_date = sanitize(\$_POST['joining_date'] ?? '');
    \$username = sanitize(\$_POST['username'] ?? '');
    \$password = \$_POST['password'] ?? '';
    
    if (!empty(\$first_name) && !empty(\$last_name) && !empty(\$employee_id) && !empty(\$username) && !empty(\$password)) {
        try {
            \$pdo->beginTransaction();
            // Create user
            \$hashed_password = password_hash(\$password, PASSWORD_DEFAULT);
            \$stmt = \$pdo->prepare(\"INSERT INTO users (username, password, role) VALUES (?, ?, 'teacher')\");
            \$stmt->execute([\$username, \$hashed_password]);
            \$user_id = \$pdo->lastInsertId();
            
            // Create teacher
            \$stmt = \$pdo->prepare(\"INSERT INTO teachers (user_id, first_name, last_name, employee_id, email, phone, qualification, joining_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)\");
            \$stmt->execute([\$user_id, \$first_name, \$last_name, \$employee_id, \$email, \$phone, \$qualification, \$joining_date]);
            
            \$pdo->commit();
            set_message('success', 'Teacher added successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            \$pdo->rollBack();
            set_message('error', 'Error: Username, Email, or Employee ID may already exist.');
        }
    } else {
        set_message('error', 'Required fields are missing.');
    }
}
include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add Teacher</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <h5 class=\"text-primary\">Login Details</h5>
            <hr>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Username *</label>
                    <input type=\"text\" name=\"username\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Password *</label>
                    <input type=\"password\" name=\"password\" class=\"form-control\" required>
                </div>
            </div>
            
            <h5 class=\"text-primary mt-4\">Teacher Details</h5>
            <hr>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>First Name *</label>
                    <input type=\"text\" name=\"first_name\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Last Name *</label>
                    <input type=\"text\" name=\"last_name\" class=\"form-control\" required>
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Employee ID *</label>
                    <input type=\"text\" name=\"employee_id\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-4\">
                    <label>Email Address</label>
                    <input type=\"email\" name=\"email\" class=\"form-control\">
                </div>
                <div class=\"col-md-4\">
                    <label>Phone Number</label>
                    <input type=\"text\" name=\"phone\" class=\"form-control\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Qualification</label>
                    <input type=\"text\" name=\"qualification\" class=\"form-control\">
                </div>
                <div class=\"col-md-6\">
                    <label>Joining Date</label>
                    <input type=\"date\" name=\"joining_date\" class=\"form-control\">
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary mt-3\">Save Teacher</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'create.php', $create);

$edit = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Edit Teacher';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$first_name = sanitize(\$_POST['first_name'] ?? '');
    \$last_name = sanitize(\$_POST['last_name'] ?? '');
    \$employee_id = sanitize(\$_POST['employee_id'] ?? '');
    \$email = sanitize(\$_POST['email'] ?? '');
    \$phone = sanitize(\$_POST['phone'] ?? '');
    \$qualification = sanitize(\$_POST['qualification'] ?? '');
    \$joining_date = sanitize(\$_POST['joining_date'] ?? '');
    
    if (!empty(\$first_name) && !empty(\$last_name) && !empty(\$employee_id)) {
        \$stmt = \$pdo->prepare(\"UPDATE teachers SET first_name=?, last_name=?, employee_id=?, email=?, phone=?, qualification=?, joining_date=? WHERE id=?\");
        try {
            \$stmt->execute([\$first_name, \$last_name, \$employee_id, \$email, \$phone, \$qualification, \$joining_date, \$id]);
            set_message('success', 'Teacher updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Error: Email or Employee ID may already exist.');
        }
    } else {
        set_message('error', 'Required fields are missing.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM teachers WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit Teacher</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <h5 class=\"text-primary mt-4\">Teacher Details</h5>
            <hr>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>First Name *</label>
                    <input type=\"text\" name=\"first_name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['first_name']) ?>\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Last Name *</label>
                    <input type=\"text\" name=\"last_name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['last_name']) ?>\" required>
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Employee ID *</label>
                    <input type=\"text\" name=\"employee_id\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['employee_id']) ?>\" required>
                </div>
                <div class=\"col-md-4\">
                    <label>Email Address</label>
                    <input type=\"email\" name=\"email\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['email']) ?>\">
                </div>
                <div class=\"col-md-4\">
                    <label>Phone Number</label>
                    <input type=\"text\" name=\"phone\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['phone']) ?>\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Qualification</label>
                    <input type=\"text\" name=\"qualification\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['qualification']) ?>\">
                </div>
                <div class=\"col-md-6\">
                    <label>Joining Date</label>
                    <input type=\"date\" name=\"joining_date\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['joining_date']) ?>\">
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary mt-3\">Update Teacher</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'edit.php', $edit);

$delete = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$id = \$_GET['id'] ?? 0;
if (\$id) {
    try {
        \$pdo->beginTransaction();
        
        // Get user_id
        \$stmt = \$pdo->prepare(\"SELECT user_id FROM teachers WHERE id = ?\");
        \$stmt->execute([\$id]);
        \$teacher = \$stmt->fetch();
        
        if (\$teacher) {
            // Delete teacher record
            \$stmt = \$pdo->prepare(\"DELETE FROM teachers WHERE id = ?\");
            \$stmt->execute([\$id]);
            
            // Delete associated user record
            if (\$teacher['user_id']) {
                \$stmt = \$pdo->prepare(\"DELETE FROM users WHERE id = ?\");
                \$stmt->execute([\$teacher['user_id']]);
            }
        }
        
        \$pdo->commit();
        set_message('success', 'Teacher deleted successfully.');
    } catch (PDOException \$e) {
        \$pdo->rollBack();
        set_message('error', 'Cannot delete this record because it is referenced elsewhere.');
    }
}
redirect('index.php');
";
file_put_contents($base . 'delete.php', $delete);

echo "Teachers scaffold generated";
?>
