<?php
require_once __DIR__ . '/../../../app/auth.php';
require_once __DIR__ . '/../../../app/functions.php';
require_once __DIR__ . '/../app/users.php';
require_once __DIR__ . '/../app/news.php';

require_login('plugins');

$config = load_config();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_title'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) die('CSRF Failed');
    add_admin_news($_POST['news_title'], $_POST['news_content']);
    $success = "News published!";
}

if (isset($_GET['delete_user'])) {
    if (!verify_csrf_token($_GET['token'])) die('CSRF Failed');
    $file = ANON_USERS_DIR . basename($_GET['delete_user']) . '.json';
    if (file_exists($file)) unlink($file);
    header("Location: manage.php?success=user_deleted");
    exit;
}

$user_files = glob(ANON_USERS_DIR . '*.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users & News - AnonBlog</title>
    <link rel="stylesheet" href="../../../admin/style.css">
    <style>.main-content { margin-left: 310px; margin-top: 60px; padding: 20px; }</style>
</head>
<body>
    <?php include "../../../admin/sidebar.php"; ?>
    <div class="main-content">
        <h1>AnonUsers Pro Management</h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="card">
                <h3>📢 Publish News to Users</h3>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    <div class="form-group"><label>Title</label><input type="text" name="news_title" required></div>
                    <div class="form-group"><label>Content</label><textarea name="news_content" required style="height:100px;"></textarea></div>
                    <button type="submit" class="btn btn-primary">Publish News</button>
                </form>
            </div>

            <div class="card">
                <h3>👥 Registered Users</h3>
                <table style="width:100%; text-align:left;">
                    <thead><tr><th>User</th><th>Role</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($user_files as $f):
                            $u = json_decode(file_get_contents($f), true);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['role'] ?? 'Subscriber'); ?></td>
                            <td><a href="manage.php?delete_user=<?php echo $u['username']; ?>&token=<?php echo get_csrf_token(); ?>" class="btn-danger" style="font-size:0.8rem; padding:2px 5px;" onclick="return confirm('Delete user?')">Delete</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
