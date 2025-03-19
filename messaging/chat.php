
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch users to chat with
$users = $pdo->query("SELECT id, username FROM users WHERE id != $user_id")->fetchAll();

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_id'], $_POST['message_text'])) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $_POST['receiver_id'], $_POST['message_text']]);
}

// Fetch conversation with selected user
$chat_with = $_GET['chat_with'] ?? null;
$messages = [];
if ($chat_with) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->execute([$user_id, $chat_with, $chat_with, $user_id]);
    $messages = $stmt->fetchAll();
}
?>

<div class="container mt-5">
    <h1>Messaging System</h1>
    <div class="row">
        <div class="col-md-4">
            <h4>Users</h4>
            <ul class="list-group">
                <?php foreach ($users as $user): ?>
                    <li class="list-group-item">
                        <a href="chat.php?chat_with=<?= $user['id'] ?>"><?= $user['username'] ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-8">
            <?php if ($chat_with): ?>
                <h4>Chat</h4>
                <div class="chat-box border p-3 mb-3" style="height: 400px; overflow-y: scroll;">
                    <?php foreach ($messages as $message): ?>
                        <p><strong><?= $message['sender_id'] == $user_id ? 'You' : $user['username'] ?>:</strong> <?= $message['message_text'] ?></p>
                    <?php endforeach; ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">
                    <textarea name="message_text" class="form-control mb-3" placeholder="Type your message here" required></textarea>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            <?php else: ?>
                <p>Select a user to start chatting.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
