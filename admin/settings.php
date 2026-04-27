<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login();

$config = load_config();
$error = '';
$success = '';
$uploads_dir = __DIR__ . '/../uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $site_name = $_POST['site_name'] ?? $config['site_name'];
    $admin_nickname = $_POST['admin_nickname'] ?? $config['admin_nickname'] ?? 'Admin';
    $comments_enabled = isset($_POST['comments_enabled']);
    $show_excerpts = isset($_POST['show_excerpts']);
    $posts_per_page = (int)($_POST['posts_per_page'] ?? 5);
    $sidebar_position = $_POST['sidebar_position'] ?? 'right';

    $new_config = [
        'site_name' => $site_name,
        'admin_nickname' => $admin_nickname,
        'comments_enabled' => $comments_enabled,
        'show_excerpts' => $show_excerpts,
        'posts_per_page' => $posts_per_page,
        'sidebar_position' => $sidebar_position,
    ];

    // Handle avatar upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed) && getimagesize($file['tmp_name'])) {
            $filename = 'avatar_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploads_dir . $filename)) {
                $new_config['admin_avatar'] = $filename;
            }
        } else {
            $error = "Invalid avatar file.";
        }
    } else {
        $new_config['admin_avatar'] = $config['admin_avatar'] ?? '';
    }

    // Handle password change
    if (!empty($_POST['new_password'])) {
        $new_config['admin_pass'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    } else {
        $new_config['admin_pass'] = $config['admin_pass'];
    }

    if (empty($error)) {
        if (update_config($new_config)) {
            $success = "Settings updated successfully.";
            $config = load_config();
        } else {
            $error = "Failed to update settings.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .sidebar { width: 250px; background: #333; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .main-content { flex: 1; padding: 2rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], input[type="password"], select, input[type="file"], input[type="number"] { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 1rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .error { color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .avatar-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 1px solid #ccc; }

        .section-header { cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: #eee; padding: 10px 15px; border-radius: 4px; margin-bottom: 10px; }
        .section-content { padding: 15px; border: 1px solid #eee; border-top: none; border-radius: 0 0 4px 4px; margin-bottom: 20px; }
        .icon { font-size: 1.2rem; }
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>
    <div class="main-content">
        <div class="header">
            <h1>General Settings</h1>
        </div>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

            <div class="card">
                <div class="section-header">
                    <span><strong>⚙️ Site Settings</strong></span>
                </div>
                <div class="section-content">
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($config['site_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="comments_enabled" <?php echo ($config['comments_enabled'] ?? true) ? 'checked' : ''; ?>> Enable comments globally
                        </label>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="show_excerpts" <?php echo ($config['show_excerpts'] ?? true) ? 'checked' : ''; ?>> Show excerpts on homepage
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="posts_per_page">Posts per page</label>
                        <input type="number" id="posts_per_page" name="posts_per_page" value="<?php echo htmlspecialchars($config['posts_per_page'] ?? 5); ?>" min="1">
                    </div>

                    <div class="form-group">
                        <label for="sidebar_position">Sidebar Position (Default Template)</label>
                        <select id="sidebar_position" name="sidebar_position">
                            <option value="left" <?php echo ($config['sidebar_position'] ?? '') === 'left' ? 'selected' : ''; ?>>Left</option>
                            <option value="right" <?php echo ($config['sidebar_position'] ?? '') === 'right' ? 'selected' : ''; ?>>Right</option>
                        </select>
                    </div>
                </div>

                <div class="section-header">
                    <span><strong>👤 Profile Settings</strong></span>
                </div>
                <div class="section-content">
                    <div class="form-group">
                        <label for="admin_nickname">Admin Nickname</label>
                        <input type="text" id="admin_nickname" name="admin_nickname" value="<?php echo htmlspecialchars($config['admin_nickname'] ?? 'Admin'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Admin Avatar</label>
                        <?php if (!empty($config['admin_avatar'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($config['admin_avatar']); ?>" class="avatar-preview" alt="Avatar">
                        <?php endif; ?>
                        <input type="file" name="avatar">
                    </div>
                </div>

                <div class="section-header">
                    <span><strong>🔒 Security</strong></span>
                </div>
                <div class="section-content">
                    <div class="form-group">
                        <label for="new_password">Change Admin Password (leave blank to keep current)</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>
                </div>

                <div style="padding: 15px;">
                    <button type="submit" class="btn btn-primary">Save All Settings</button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
