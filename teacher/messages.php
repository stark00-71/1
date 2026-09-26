<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
// Role check dynamically based on directory
$role = basename(dirname(__DIR__));
if ($role === 'admin') requireRole('admin');
else if ($role === 'teacher') requireRole('teacher');
else if ($role === 'student') requireRole('student');

$page_title = 'Messages';
$user_id = $_SESSION['user_id'];

// Basic placeholder fetch
$stmt = $pdo->prepare("SELECT m.*, u.username as sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? ORDER BY m.created_at DESC");
$stmt->execute([$user_id]);
$inbox = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Messages</h1>
    <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#composeModal">
        <i class="fas fa-edit fa-sm text-white-50"></i> Compose
    </button>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="card shadow mb-4">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action active"><i class="fas fa-inbox me-2"></i> Inbox <span class="badge bg-danger float-end">0</span></a>
                    <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-paper-plane me-2"></i> Sent</a>
                    <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-trash me-2"></i> Trash</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <?php foreach($inbox as $msg): ?>
                            <tr class="<?= !$msg['is_read'] ? 'fw-bold bg-light' : '' ?>">
                                <td><?= htmlspecialchars($msg['sender_name']) ?></td>
                                <td><?= htmlspecialchars($msg['subject']) ?></td>
                                <td class="text-end text-muted small"><?= date('M d, g:i a', strtotime($msg['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($inbox)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Your inbox is empty.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compose Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label>To</label>
            <select class="form-select"><option>Select Recipient</option></select>
          </div>
          <div class="mb-3">
            <label>Subject</label>
            <input type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label>Message</label>
            <textarea class="form-control" rows="5"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send Message</button>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>