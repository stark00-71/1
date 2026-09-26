<?php
$base = 'C:\\xampp\\htdocs\\1\\admin\\students\\';

$create = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'Add Student';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$first_name = sanitize(\$_POST['first_name'] ?? '');
    \$last_name = sanitize(\$_POST['last_name'] ?? '');
    \$email = sanitize(\$_POST['email'] ?? '');
    \$password = \$_POST['password'] ?? '';
    \$admission_no = sanitize(\$_POST['admission_no'] ?? '');
    \$roll_number = sanitize(\$_POST['roll_number'] ?? '');
    \$gender = sanitize(\$_POST['gender'] ?? 'Male');
    \$class_id = (int)(\$_POST['class_id'] ?? 0);
    \$section_id = (int)(\$_POST['section_id'] ?? 0);
    \$dob = sanitize(\$_POST['dob'] ?? '');
    
    if (!empty(\$first_name) && !empty(\$email) && !empty(\$password) && !empty(\$admission_no) && \$class_id && \$section_id) {
        try {
            \$pdo->beginTransaction();
            
            // Create user
            \$stmt = \$pdo->prepare(\"INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'student')\");
            \$password_hash = password_hash(\$password, PASSWORD_DEFAULT);
            // using admission_no as username initially
            \$stmt->execute([\$admission_no, \$email, \$password_hash]);
            \$user_id = \$pdo->lastInsertId();
            
            // Create student
            \$stmt = \$pdo->prepare(\"INSERT INTO students (user_id, admission_no, roll_number, first_name, last_name, gender, dob, class_id, section_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)\");
            \$stmt->execute([\$user_id, \$admission_no, \$roll_number, \$first_name, \$last_name, \$gender, \$dob, \$class_id, \$section_id]);
            
            \$pdo->commit();
            set_message('success', 'Student added successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            \$pdo->rollBack();
            set_message('error', 'Database error: ' . \$e->getMessage());
        }
    } else {
        set_message('error', 'Please fill required fields.');
    }
}

\$classes = \$pdo->query(\"SELECT * FROM classes ORDER BY name\")->fetchAll();
\$sections = \$pdo->query(\"SELECT * FROM sections ORDER BY name\")->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Add Student</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <h5 class=\"mb-3\">Login Details</h5>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>Email *</label>
                    <input type=\"email\" name=\"email\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Password *</label>
                    <input type=\"password\" name=\"password\" class=\"form-control\" required>
                </div>
            </div>
            
            <h5 class=\"mb-3 mt-4\">Student Details</h5>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>First Name *</label>
                    <input type=\"text\" name=\"first_name\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Last Name</label>
                    <input type=\"text\" name=\"last_name\" class=\"form-control\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Admission No *</label>
                    <input type=\"text\" name=\"admission_no\" class=\"form-control\" required>
                </div>
                <div class=\"col-md-4\">
                    <label>Roll Number</label>
                    <input type=\"text\" name=\"roll_number\" class=\"form-control\">
                </div>
                <div class=\"col-md-4\">
                    <label>Date of Birth</label>
                    <input type=\"date\" name=\"dob\" class=\"form-control\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Gender</label>
                    <select name=\"gender\" class=\"form-control\">
                        <option value=\"Male\">Male</option>
                        <option value=\"Female\">Female</option>
                        <option value=\"Other\">Other</option>
                    </select>
                </div>
                <div class=\"col-md-4\">
                    <label>Class *</label>
                    <select name=\"class_id\" class=\"form-control\" required>
                        <option value=\"\">Select Class</option>
                        <?php foreach (\$classes as \$class): ?>
                        <option value=\"<?= \$class['id'] ?>\"><?= htmlspecialchars(\$class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class=\"col-md-4\">
                    <label>Section *</label>
                    <select name=\"section_id\" class=\"form-control\" required>
                        <option value=\"\">Select Section</option>
                        <?php foreach (\$sections as \$section): ?>
                        <option value=\"<?= \$section['id'] ?>\"><?= htmlspecialchars(\$section['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Save Student</button>
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
\$page_title = 'Edit Student';
\$id = \$_GET['id'] ?? 0;

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$first_name = sanitize(\$_POST['first_name'] ?? '');
    \$last_name = sanitize(\$_POST['last_name'] ?? '');
    \$admission_no = sanitize(\$_POST['admission_no'] ?? '');
    \$roll_number = sanitize(\$_POST['roll_number'] ?? '');
    \$gender = sanitize(\$_POST['gender'] ?? 'Male');
    \$class_id = (int)(\$_POST['class_id'] ?? 0);
    \$section_id = (int)(\$_POST['section_id'] ?? 0);
    \$dob = sanitize(\$_POST['dob'] ?? '');
    
    if (!empty(\$first_name) && !empty(\$admission_no) && \$class_id && \$section_id) {
        try {
            \$stmt = \$pdo->prepare(\"UPDATE students SET admission_no=?, roll_number=?, first_name=?, last_name=?, gender=?, dob=?, class_id=?, section_id=? WHERE id=?\");
            \$stmt->execute([\$admission_no, \$roll_number, \$first_name, \$last_name, \$gender, \$dob, \$class_id, \$section_id, \$id]);
            set_message('success', 'Student updated successfully.');
            redirect('index.php');
        } catch (PDOException \$e) {
            set_message('error', 'Database error: ' . \$e->getMessage());
        }
    } else {
        set_message('error', 'Please fill required fields.');
    }
}
\$stmt = \$pdo->prepare(\"SELECT * FROM students WHERE id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

\$classes = \$pdo->query(\"SELECT * FROM classes ORDER BY name\")->fetchAll();
\$sections = \$pdo->query(\"SELECT * FROM sections ORDER BY name\")->fetchAll();

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">Edit Student</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <form method=\"POST\" action=\"\">
            <h5 class=\"mb-3\">Student Details</h5>
            <div class=\"row mb-3\">
                <div class=\"col-md-6\">
                    <label>First Name *</label>
                    <input type=\"text\" name=\"first_name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['first_name']) ?>\" required>
                </div>
                <div class=\"col-md-6\">
                    <label>Last Name</label>
                    <input type=\"text\" name=\"last_name\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['last_name']) ?>\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Admission No *</label>
                    <input type=\"text\" name=\"admission_no\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['admission_no']) ?>\" required>
                </div>
                <div class=\"col-md-4\">
                    <label>Roll Number</label>
                    <input type=\"text\" name=\"roll_number\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['roll_number']) ?>\">
                </div>
                <div class=\"col-md-4\">
                    <label>Date of Birth</label>
                    <input type=\"date\" name=\"dob\" class=\"form-control\" value=\"<?= htmlspecialchars(\$record['dob']) ?>\">
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-md-4\">
                    <label>Gender</label>
                    <select name=\"gender\" class=\"form-control\">
                        <option value=\"Male\" <?= \$record['gender']=='Male'?'selected':'' ?>>Male</option>
                        <option value=\"Female\" <?= \$record['gender']=='Female'?'selected':'' ?>>Female</option>
                        <option value=\"Other\" <?= \$record['gender']=='Other'?'selected':'' ?>>Other</option>
                    </select>
                </div>
                <div class=\"col-md-4\">
                    <label>Class *</label>
                    <select name=\"class_id\" class=\"form-control\" required>
                        <option value=\"\">Select Class</option>
                        <?php foreach (\$classes as \$class): ?>
                        <option value=\"<?= \$class['id'] ?>\" <?= \$class['id']==\$record['class_id']?'selected':'' ?>><?= htmlspecialchars(\$class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class=\"col-md-4\">
                    <label>Section *</label>
                    <select name=\"section_id\" class=\"form-control\" required>
                        <option value=\"\">Select Section</option>
                        <?php foreach (\$sections as \$section): ?>
                        <option value=\"<?= \$section['id'] ?>\" <?= \$section['id']==\$record['section_id']?'selected':'' ?>><?= htmlspecialchars(\$section['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type=\"submit\" class=\"btn btn-primary\">Update Student</button>
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
        \$stmt = \$pdo->prepare(\"SELECT user_id FROM students WHERE id = ?\");
        \$stmt->execute([\$id]);
        \$student = \$stmt->fetch();
        if (\$student) {
            // Delete user, and cascade will delete student
            \$stmt2 = \$pdo->prepare(\"DELETE FROM users WHERE id = ?\");
            \$stmt2->execute([\$student['user_id']]);
            set_message('success', 'Student deleted successfully.');
        }
    } catch (PDOException \$e) {
        set_message('error', 'Error deleting student.');
    }
}
redirect('index.php');
";
file_put_contents($base . 'delete.php', $delete);

$view = "<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireRole('admin');
\$page_title = 'View Student';
\$id = \$_GET['id'] ?? 0;

\$stmt = \$pdo->prepare(\"SELECT s.*, c.name as class_name, sec.name as section_name, u.email FROM students s LEFT JOIN classes c ON s.class_id = c.id LEFT JOIN sections sec ON s.section_id = sec.id LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?\");
\$stmt->execute([\$id]);
\$record = \$stmt->fetch();
if (!\$record) redirect('index.php');

include '../../includes/header.php';
?>
<div class=\"d-sm-flex align-items-center justify-content-between mb-4\">
    <h1 class=\"h3 mb-0 text-gray-800\">View Student</h1>
    <a href=\"index.php\" class=\"btn btn-sm btn-secondary shadow-sm\">Back</a>
</div>
<div class=\"card shadow mb-4\">
    <div class=\"card-body\">
        <p><strong>Name:</strong> <?= htmlspecialchars(\$record['first_name'] . ' ' . \$record['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars(\$record['email']) ?></p>
        <p><strong>Admission No:</strong> <?= htmlspecialchars(\$record['admission_no']) ?></p>
        <p><strong>Roll No:</strong> <?= htmlspecialchars(\$record['roll_number']) ?></p>
        <p><strong>Class:</strong> <?= htmlspecialchars(\$record['class_name']) ?></p>
        <p><strong>Section:</strong> <?= htmlspecialchars(\$record['section_name']) ?></p>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
";
file_put_contents($base . 'view.php', $view);

echo "Students CRUD scaffolded successfully.";
?>
