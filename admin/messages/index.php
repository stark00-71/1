<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
// Role check dynamically based on directory
$role = basename(dirname(__DIR__));
if ($role === 'admin') requireRole('admin');
else if ($role === 'teacher') requireRole('teacher');
else if ($role === 'student') requireRole('student');

$page_title = 'Messages';
$user_id = $_SESSION['user_id'];

$folder = $_GET['folder'] ?? 'inbox';

// Unread count
$unread_stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unread_stmt->execute([$user_id]);
$unread_count = $unread_stmt->fetchColumn();

// Fetch messages based on folder
if ($folder === 'sent') {
    $stmt = $pdo->prepare("SELECT m.*, u.username as other_name FROM messages m JOIN users u ON m.receiver_id = u.id WHERE m.sender_id = ? ORDER BY m.created_at DESC");
} else if ($folder === 'trash') {
    // Basic placeholder for trash (requires schema update for deleted flags)
    $stmt = $pdo->prepare("SELECT m.*, u.username as other_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE 1=0 AND m.receiver_id = ?");
} else {
    // Inbox
    $stmt = $pdo->prepare("SELECT m.*, u.username as other_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? ORDER BY m.created_at DESC");
}
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll();

// Fetch all users for the compose dropdown
$users_stmt = $pdo->query("SELECT id, username, role FROM users WHERE status = 'active' ORDER BY role, username");
$all_users = $users_stmt->fetchAll();

include '../../includes/header.php';
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
                    <a href="index.php?folder=inbox" class="list-group-item list-group-item-action <?= $folder === 'inbox' ? 'active' : '' ?>">
                        <i class="fas fa-inbox me-2"></i> Inbox 
                        <?php if($unread_count > 0): ?>
                            <span class="badge bg-danger float-end"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="index.php?folder=sent" class="list-group-item list-group-item-action <?= $folder === 'sent' ? 'active' : '' ?>"><i class="fas fa-paper-plane me-2"></i> Sent</a>
                    <a href="index.php?folder=trash" class="list-group-item list-group-item-action <?= $folder === 'trash' ? 'active' : '' ?>"><i class="fas fa-trash me-2"></i> Trash</a>
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
                            <?php foreach($messages as $msg): ?>
                            <tr class="<?= ($folder === 'inbox' && !$msg['is_read']) ? 'fw-bold bg-light' : '' ?>">
                                <td><?= htmlspecialchars($msg['other_name']) ?></td>
                                <td><?= htmlspecialchars($msg['subject']) ?></td>
                                <td class="text-end text-muted small"><?= date('M d, g:i a', strtotime($msg['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($messages)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No messages found in this folder.</td>
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
        <form id="composeForm" method="POST" action="send.php">
          <div class="mb-3">
            <label>To</label>
            <select name="receiver_id" class="form-control" required>
              <option value="">Select Recipient</option>
              <?php foreach ($all_users as $u): ?>
                <?php if ($u['id'] != $user_id): ?>
                  <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?> (<?= ucfirst($u['role']) ?>)</option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Message</label>
            <textarea name="body" class="form-control" rows="5" required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="composeForm" class="btn btn-primary">Send Message</button>
      </div>
    </div>
  </div>
</div>
<?php include '../../includes/footer.php'; ?>