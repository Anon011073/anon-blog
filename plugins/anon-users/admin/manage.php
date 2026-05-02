<?php
require_once __DIR__ . '/../../../app/auth.php';
require_once __DIR__ . '/../../../app/functions.php';
require_once __DIR__ . '/../app/users.php';
require_once __DIR__ . '/../app/news.php';

require_login();

$config = load_config();
$users = get_anon_users();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anon_action'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) die('CSRF Failed');

    if ($_POST['anon_action'] === 'update_user') {
        $username = $_POST['username'];
        $user = get_anon_user($username);
        if ($user) {
            $user['role'] = $_POST['role'];
            $user['auto_approve_posts'] = isset($_POST['auto_approve_posts']);
            $user['auto_approve_comments'] = isset($_POST['auto_approve_comments']);
            $user['permissions'] = $_POST['permissions'] ?? [];
            save_anon_user($user);
            $success = "User '$username' updated.";
            $users = get_anon_users();
        }
    } elseif ($_POST['anon_action'] === 'post_news') {
        add_admin_news($_POST['title'], $_POST['content']);
        $success = "News published to users.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - AnonBlog Admin</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .main-content { flex: 1; padding: 2rem; margin-left: 280px; margin-top: 50px; overflow-y: auto; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .card h2 { margin-top: 0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .success { background: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        input[type="text"], textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../../admin/sidebar.php'; ?>
    <div class="main-content">
        <h1>User Management</h1>
        <?php if ($success): ?><div style="background: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 20px;"><?php echo $success; ?></div><?php endif; ?>

        <div class="card">
            <h2>Post News to Users</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <input type="hidden" name="anon_action" value="post_news">
                <div style="margin-bottom: 10px;">
                    <input type="text" name="title" placeholder="News Title" required style="width: 100%; padding: 8px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <textarea name="content" placeholder="Content for users..." required style="width: 100%; height: 60px; padding: 8px;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publish News</button>
            </form>

            <h3 style="margin-top: 20px;">Current & Previous News</h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <?php
                $all_news = get_admin_news();
                if (empty($all_news)): ?>
                    <p>No news posted yet.</p>
                <?php else:
                    foreach ($all_news as $item): ?>
                    <div style="border-bottom: 1px solid #eee; padding: 10px 0; display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                            <span style="font-size: 0.8rem; color: #888;">(<?php echo $item['date']; ?>)</span>
                            <p style="margin: 5px 0; font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="card">
            <h2>User Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nickname</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Options</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                            <input type="hidden" name="anon_action" value="update_user">
                            <input type="hidden" name="username" value="<?php echo $u['username']; ?>">
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['nickname']); ?></td>
                            <td>
                                <select name="role">
                                    <option value="Subscriber" <?php echo ($u['role'] ?? '') === 'Subscriber' ? 'selected' : ''; ?>>Subscriber</option>
                                    <option value="Author" <?php echo ($u['role'] ?? '') === 'Author' ? 'selected' : ''; ?>>Author</option>
                                </select>
                            </td>
                            <td>
                                <?php $perms = $u['permissions'] ?? []; ?>
                                <div style="font-size: 0.8rem;">
                                    <label><input type="checkbox" name="permissions[]" value="dashboard" <?php echo in_array('dashboard', $perms) ? 'checked' : ''; ?>> Dash</label>
                                    <label><input type="checkbox" name="permissions[]" value="pages" <?php echo in_array('pages', $perms) ? 'checked' : ''; ?>> Pages</label><br>
                                    <label><input type="checkbox" name="permissions[]" value="media" <?php echo in_array('media', $perms) ? 'checked' : ''; ?>> Media</label>
                                    <label><input type="checkbox" name="permissions[]" value="comments" <?php echo in_array('comments', $perms) ? 'checked' : ''; ?>> Comm</label>
                                </div>
                            </td>
                            <td>
                                <label><input type="checkbox" name="auto_approve_posts" <?php echo ($u['auto_approve_posts'] ?? false) ? 'checked' : ''; ?>> Auto-Post</label><br>
                                <label><input type="checkbox" name="auto_approve_comments" <?php echo ($u['auto_approve_comments'] ?? false) ? 'checked' : ''; ?>> Auto-Comment</label>
                            </td>
                            <td><button type="submit" class="btn btn-primary">Save</button></td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p><a href="../../admin/index.php">← Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
