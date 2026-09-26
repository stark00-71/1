<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
requireRole('admin');
$page_title = 'My Profile';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (!empty($first_name) && !empty($last_name) && !empty($phone)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE admins SET first_name=?, last_name=?, phone=? WHERE user_id=?");
            $stmt->execute([$first_name, $last_name, $phone, $user_id]);
            
            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/admins/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'admin_' . $user_id . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                        // Update profile photo
                        $stmt_pic = $pdo->prepare("UPDATE admins SET profile_photo=? WHERE user_id=?");
                        $stmt_pic->execute([$new_filename, $user_id]);
                        $_SESSION['profile_photo'] = $new_filename;
                    }
                } else {
                    $error_msg = "Invalid file type. Only JPG, JPEG, PNG and GIF are allowed.";
                }
            }
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }
            $success_msg = "Profile updated successfully.";
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error_msg = "Error updating profile: " . $e->getMessage();
        }
    } else {
        $error_msg = "First name, last name, and phone number are required.";
    }
}

$stmt = $pdo->prepare("SELECT a.*, u.email, u.username FROM admins a JOIN users u ON a.user_id = u.id WHERE a.user_id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch();

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
                <?php if (!empty($admin['profile_photo']) && file_exists('../uploads/admins/' . $admin['profile_photo'])): ?>
                    <img src="<?= BASE_URL ?>/uploads/admins/<?= htmlspecialchars($admin['profile_photo']) ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user-shield fa-5x text-gray-300 mb-3"></i>
                <?php endif; ?>
                <h5 class="font-weight-bold"><?= htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) ?></h5>
                <p class="text-muted">Username: <?= htmlspecialchars($admin['username']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($admin['email']) ?></p>
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
                        <div class="col-md-6">
                            <label>Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>First Name *</label>
                            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($admin['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($admin['last_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Phone *</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
