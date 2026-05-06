<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login('settings');

$config = load_config();
$error = '';
$success = '';
$uploads_dir = __DIR__ . '/../uploads/';

// --- PLUGIN SETTINGS HANDLER ---
$plugin_to_configure = $_GET['plugin'] ?? '';
$all_plugins_data = get_enabled_plugins_data();

if ($plugin_to_configure && isset($all_plugins_data[$plugin_to_configure])) {
    $p_data = $all_plugins_data[$plugin_to_configure];
    $plugin_settings_file = __DIR__ . '/../plugins/' . $plugin_to_configure . '/admin/settings.php';

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head><meta charset="UTF-8"><title><?php echo htmlspecialchars($p_data['name']); ?> Settings</title><link rel="stylesheet" href="style.css"></head>
    <body>
    <?php include "sidebar.php"; ?>
    <div style="margin-left:310px; padding:2rem; margin-top:50px;">
        <h1><?php echo htmlspecialchars($p_data['name']); ?> Settings</h1>
        <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Settings saved successfully.</div><?php endif; ?>

        <?php
        if (file_exists($plugin_settings_file)) {
            // The plugin settings file should only contain the form content or specific logic
            include $plugin_settings_file;
        } else {
            echo "<div class='card'><p>No configurable options for this plugin.</p></div>";
        }
        ?>
        <br><a href="plugins.php" class="btn">&larr; Back to Plugins</a>
    </div>
    </body></html>
    <?php
    exit;
}
// --- END PLUGIN SETTINGS HANDLER ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) die('CSRF token validation failed.');
    $site_name = $_POST['site_name'] ?? $config['site_name'];
    $admin_nickname = $_POST['admin_nickname'] ?? $config['admin_nickname'] ?? 'Admin';
    $admin_about_me = $_POST['admin_about_me'] ?? $config['admin_about_me'] ?? '';
    $comments_enabled = isset($_POST['comments_enabled']);
    $disqus_shortname = sanitize($_POST['disqus_shortname'] ?? '');
    $posts_per_page = (int)($_POST['posts_per_page'] ?? 5);
    $sidebar_position = $_POST['sidebar_position'] ?? 'right';

    $new_config = $config;
    $new_config['site_name'] = $site_name;
    $new_config['admin_nickname'] = $admin_nickname;
    $new_config['admin_about_me'] = $admin_about_me;
    $new_config['comments_enabled'] = $comments_enabled;
    $new_config['disqus_shortname'] = $disqus_shortname;
    $new_config['posts_per_page'] = $posts_per_page;
    $new_config['sidebar_position'] = $sidebar_position;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $filename = 'avatar_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploads_dir . $filename)) $new_config['admin_avatar'] = $filename;
        }
    }
    if (!empty($_POST['new_password'])) $new_config['admin_pass'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    if (update_config($new_config)) { $success = "Settings updated."; $config = load_config(); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_demo') {
    if (!verify_csrf_token($_POST['csrf_token'])) die('CSRF token');
    if (import_demo_content()) { $success = "Demo imported."; $config = load_config(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Settings</title><link rel="stylesheet" href="style.css">
<style>
    .main-content { margin-left: 310px; margin-top: 50px; padding: 2rem; }
    .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
    .avatar-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 1px solid #ccc; }
    .profile-row { display: flex; gap: 20px; align-items: flex-start; }
</style>
</head>
<body>
<?php include "sidebar.php"; ?>
<div class="main-content">
    <h1>General Settings</h1>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
        <div class="card">
            <h3>⚙️ Site Configuration</h3>
            <label>Site Name</label><input type="text" name="site_name" value="<?php echo htmlspecialchars($config['site_name']); ?>" required style="width:100%; padding:10px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:15px;">
                <div><label>Posts Per Page</label><input type="number" name="posts_per_page" value="<?php echo $config['posts_per_page']; ?>" style="width:100%; padding:10px;"></div>
                <div><label>Sidebar Position</label><select name="sidebar_position" style="width:100%; padding:10px;"><option value="left" <?php echo $config['sidebar_position']==='left'?'selected':''; ?>>Left</option><option value="right" <?php echo $config['sidebar_position']==='right'?'selected':''; ?>>Right</option></select></div>
            </div>
            <br>
            <label><input type="checkbox" name="comments_enabled" <?php echo $config['comments_enabled']?'checked':''; ?>> Enable built-in comments</label>
            <br><br>
            <label>Disqus Shortname</label><input type="text" name="disqus_shortname" value="<?php echo htmlspecialchars($config['disqus_shortname'] ?? ''); ?>" style="width:100%; padding:10px;">

            <h3 style="margin-top:30px;">👤 Admin Profile</h3>
            <div class="profile-row">
                <div>
                    <label>Avatar</label><br>
                    <?php if (!empty($config['admin_avatar'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($config['admin_avatar']); ?>" class="avatar-preview">
                    <?php else: ?>
                        <div class="avatar-preview" style="background:#eee; display:flex; align-items:center; justify-content:center;">No Image</div>
                    <?php endif; ?>
                    <input type="file" name="avatar" style="margin-top:10px;">
                </div>
                <div style="flex:1;">
                    <label>Nickname</label><input type="text" name="admin_nickname" value="<?php echo htmlspecialchars($config['admin_nickname'] ?? 'Admin'); ?>" style="width:100%; padding:10px;">
                    <label style="margin-top:10px; display:block;">About Me / Bio</label>
                    <textarea name="admin_about_me" style="width:100%; height:80px; padding:10px;"><?php echo htmlspecialchars($config['admin_about_me'] ?? ''); ?></textarea>
                </div>
            </div>

            <h3 style="margin-top:30px;">🔒 Security</h3>
            <label>New Password (leave blank to keep current)</label><input type="password" name="new_password" style="width:100%; padding:10px;">
            <br><br>
            <button type="submit" class="btn btn-primary">Save All Settings</button>
        </div>
    </form>
</div>
</body></html>
