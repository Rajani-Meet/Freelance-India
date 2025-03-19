<?php
ob_start(); // Start output buffering
session_start();
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch users to chat with
$users = $pdo->query("SELECT id, username, profile_picture FROM users WHERE id != $user_id")->fetchAll();

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_id'], $_POST['message_text'])) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $_POST['receiver_id'], $_POST['message_text']]);
    
    // Redirect to prevent form resubmission
    header("Location: chat.php?chat_with=" . $_POST['receiver_id']);
    exit;
}

// Fetch conversation with selected user
$chat_with = $_GET['chat_with'] ?? null;
$messages = [];
$chat_user = null;
if ($chat_with) {
    $stmt = $pdo->prepare("SELECT m.*, u.username, u.profile_picture FROM messages m 
                          LEFT JOIN users u ON m.sender_id = u.id
                          WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) 
                          ORDER BY m.created_at ASC");
    $stmt->execute([$user_id, $chat_with, $chat_with, $user_id]);
    $messages = $stmt->fetchAll();
    
    // Get chat user details
    $userStmt = $pdo->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
    $userStmt->execute([$chat_with]);
    $chat_user = $userStmt->fetch();
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm rounded-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Contacts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($users as $user): ?>
                            <a href="chat.php?chat_with=<?= $user['id'] ?>" 
                              class="list-group-item list-group-item-action d-flex align-items-center py-3 px-3 <?= ($chat_with == $user['id']) ? 'active' : '' ?>">
                                <div class="flex-shrink-0">
                                    <?php if ($user['profile_picture']): ?>
                                        <img src="../uploads/profile_pictures/<?= $user['profile_picture'] ?>" 
                                            class="rounded-circle" width="40" height="40" alt="<?= $user['username'] ?>">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0"><?= htmlspecialchars($user['username']) ?></h6>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <?php if (count($users) == 0): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-user-slash fa-2x mb-2"></i>
                                <p>No users available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="card shadow-sm rounded-lg">
                <div class="card-header bg-light d-flex align-items-center">
                    <?php if ($chat_with && $chat_user): ?>
                        <div class="d-flex align-items-center">
                            <?php if ($chat_user['profile_picture']): ?>
                                <img src="../uploads/profile_pictures/<?= $chat_user['profile_picture'] ?>" 
                                    class="rounded-circle me-2" width="45" height="45" alt="<?= $chat_user['username'] ?>">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                    style="width: 45px; height: 45px;">
                                    <?= strtoupper(substr($chat_user['username'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <h5 class="mb-0"><?= htmlspecialchars($chat_user['username']) ?></h5>
                        </div>
                    <?php else: ?>
                        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Messages</h5>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <?php if ($chat_with): ?>
                        <div id="chat-messages" class="chat-messages p-3 mb-3" style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                            <?php 
                            $date_shown = '';
                            foreach ($messages as $message):
                                $message_date = date('Y-m-d', strtotime($message['created_at']));
                                $is_current_user = $message['sender_id'] == $user_id;
                                
                                // Show date separator if it's a new date
                                if ($date_shown != $message_date):
                                    $date_shown = $message_date;
                                    $formatted_date = date('F j, Y', strtotime($message_date));
                            ?>
                                <div class="text-center my-3">
                                    <span class="badge bg-secondary px-3 py-2"><?= $formatted_date ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="message-wrapper d-flex <?= $is_current_user ? 'justify-content-end' : 'justify-content-start' ?> mb-3">
                                <?php if (!$is_current_user): ?>
                                <div class="flex-shrink-0 me-2">
                                    <?php if ($message['profile_picture']): ?>
                                        <img src="../uploads/profile_pictures/<?= $message['profile_picture'] ?>" 
                                            class="rounded-circle" width="35" height="35" alt="<?= $message['username'] ?>">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 35px; height: 35px; font-size: 0.8rem;">
                                            <?= strtoupper(substr($message['username'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="message-content" style="max-width: 75%;">
                                    <div class="message-bubble p-3 rounded-3 <?= $is_current_user ? 'bg-primary text-white' : 'bg-white border' ?>">
                                        <?= nl2br(htmlspecialchars($message['message_text'])) ?>
                                    </div>
                                    <div class="message-time small text-muted mt-1 <?= $is_current_user ? 'text-end' : '' ?>">
                                        <?= date('g:i A', strtotime($message['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (count($messages) == 0): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <h6>No messages yet</h6>
                                    <p>Send a message to start the conversation</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST" class="message-form">
                            <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">
                            <div class="input-group">
                                <textarea name="message_text" class="form-control" placeholder="Type your message..." required rows="2"></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Send
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-5 my-5">
                            <i class="fas fa-comment-alt fa-4x text-muted mb-3"></i>
                            <h4>Select a contact to start chatting</h4>
                            <p class="text-muted">Choose a contact from the list to begin a conversation</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom of chat on load
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Auto resize textarea
    const textarea = document.querySelector('textarea[name="message_text"]');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>