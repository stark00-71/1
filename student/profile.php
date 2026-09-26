<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('student');
$page_title = 'My Profile';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $profile_photo = $_FILES['profile_photo'] ?? null;
    
    try {
        $photo_sql = "";
        $params = [$first_name, $last_name, $phone];
        
        if ($profile_photo && $profile_photo['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($profile_photo['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '_' . time() . '.' . $ext;
                $upload_dir = '../uploads/students/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $dest = $upload_dir . $new_filename;
                if (move_uploaded_file($profile_photo['tmp_name'], $dest)) {
                    $photo_sql = ", profile_photo=?";
                    $params[] = $new_filename;
                    $_SESSION['profile_photo'] = $new_filename;
                }
            } else {
                $error_msg = "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
            }
        }
        
        $params[] = $user_id;
        
        if (empty($error_msg) && !empty($first_name) && !empty($last_name)) {
            $stmt = $pdo->prepare("UPDATE students SET first_name=?, last_name=?, phone=? {$photo_sql} WHERE user_id=?");
            $stmt->execute($params);
            $_SESSION['name'] = $first_name . ' ' . $last_name;
            $success_msg = "Profile updated successfully.";
            
            // Refresh student data after update
            $stmt = $pdo->prepare("SELECT s.*, u.email, c.name as class_name, sec.name as section_name 
                                   FROM students s 
                                   JOIN users u ON s.user_id = u.id 
                                   LEFT JOIN classes c ON s.class_id = c.id 
                                   LEFT JOIN sections sec ON s.section_id = sec.id 
                                   WHERE s.user_id = ?");
            $stmt->execute([$user_id]);
            $student = $stmt->fetch();
        }
    } catch (PDOException $e) {
        $error_msg = "Error updating profile.";
    }
}

$stmt = $pdo->prepare("SELECT s.*, u.email, c.name as class_name, sec.name as section_name 
                       FROM students s 
                       JOIN users u ON s.user_id = u.id 
                       LEFT JOIN classes c ON s.class_id = c.id 
                       LEFT JOIN sections sec ON s.section_id = sec.id 
                       WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
</div>

<?php if ($success_msg): ?>
    <div class="alert alert-success"><?= $success_msg ?></div>
<?php endif; ?>
<?php if ($error_msg): ?>
    <div class="alert alert-danger"><?= $error_msg ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <?php if (!empty($student['profile_photo'])): ?>
                    <img src="<?= BASE_URL ?>/uploads/students/<?= htmlspecialchars($student['profile_photo']) ?>" class="img-profile rounded-circle mb-3 shadow" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff;">
                <?php else: ?>
                    <i class="fas fa-user-graduate fa-5x text-gray-300 mb-3"></i>
                <?php endif; ?>
                <h5 class="font-weight-bold"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h5>
                <p class="text-muted">Admission No: <?= htmlspecialchars($student['admission_no']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($student['class_name'] . ' - ' . $student['section_name']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($student['email']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Details</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Profile Picture</label>
                            <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>First Name *</label>
                            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" value="<?= htmlspecialchars($student['dob'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Blood Group</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($student['blood_group'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>