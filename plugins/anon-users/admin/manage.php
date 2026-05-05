<?php
require_once __DIR__ . '/../../../app/auth.php';
require_once __DIR__ . '/../../../app/functions.php';

require_login('users');

$users_file = __DIR__ . '/../../../content/users.json';
$news_file = __DIR__ . '/../../../content/news.json';

$users = file_exists($users_file) ? json_decode(file_get_contents($users_file), true) : [];
$news = file_exists($news_file) ? json_decode(file_get_contents($news_file), true) : [];

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_news'])) {
        $news[] = [
            'date' => date('Y-m-d H:i'),
            'message' => sanitize($_POST['message']),
            'author' => $_SESSION['user_name']
        ];
        file_put_contents($news_file, json_encode($news, JSON_PRETTY_PRINT));
        $success = "News broadcasted.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users & News - AnonUsers</title>
    <link rel="stylesheet" href="../../../admin/style.css">
    <style>
        body { font-family: sans-serif; display: flex; margin: 0; background: #f4f4f4; }
        .main-content { flex: 1; padding: 2rem; margin-left: 250px; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>AnonUsers Pro Management</h1>
        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

        <div class="card">
            <h3>System Users</h3>
            <table>
                <thead><tr><th>Username</th><th>Nickname</th><th>Role</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['nickname'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($u['role']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Broadcast News</h3>
            <form method="POST">
                <textarea name="message" style="width:100%; height:80px; margin-bottom:10px;" placeholder="Message to all users..."></textarea>
                <button type="submit" name="add_news" class="btn btn-primary">Send News</button>
            </form>
        </div>

        <p><a href="../../../admin/plugins.php">&larr; Back to Plugins</a></p>
    </div>
</body>
</html>
