<?php
/**
 * Installer for Lightweight Blogging CMS
 */

$config_file = __DIR__ . '/config/config.php';

// If already installed, disable the installer
if (file_exists($config_file)) {
    die('CMS is already installed. Please remove install.php for security.');
}

$errors = [];
$success = false;

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    $errors[] = "PHP version 7.4.0 or higher is required. Your version: " . PHP_VERSION;
}

// Check write permissions
$dirs_to_check = [
    'content',
    'content/posts',
    'content/pages',
    'content/comments',
    'config',
    'uploads',
    'plugins'
];

foreach ($dirs_to_check as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    if (!is_dir($full_path)) {
        if (!mkdir($full_path, 0755, true)) {
            $errors[] = "Failed to create directory: $dir";
        }
    }
    if (!is_writable($full_path)) {
        $errors[] = "Directory is not writable: $dir";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'] ?? '';
    $admin_user = $_POST['admin_user'] ?? '';
    $admin_pass = $_POST['admin_pass'] ?? '';

    if (empty($site_name) || empty($admin_user) || empty($admin_pass)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);

        $config_content = "<?php\n";
        $config_content .= "return [\n";
        $config_content .= "    'site_name' => " . var_export($site_name, true) . ",\n";
        $config_content .= "    'admin_user' => " . var_export($admin_user, true) . ",\n";
        $config_content .= "    'admin_pass' => " . var_export($hashed_password, true) . ",\n";
        $config_content .= "    'admin_nickname' => 'Admin',\n";
        $config_content .= "    'admin_avatar' => '',\n";
        $config_content .= "    'theme' => 'default',\n";
        $config_content .= "    'comments_enabled' => true,\n";
        $config_content .= "    'disqus_shortname' => '',\n";
        $config_content .= "    'show_search_menu' => false,\n";
        $config_content .= "    'show_excerpts' => true,\n";
        $config_content .= "    'posts_per_page' => 5,\n";
        $config_content .= "    'sidebar_position' => 'right',\n";
        $config_content .= "    'widgets' => [\n";
        $config_content .= "        'search' => true,\n";
        $config_content .= "        'recent_posts' => true,\n";
        $config_content .= "    ],\n";
        $config_content .= "    'widget_areas' => [\n";
        $config_content .= "        'sidebar' => ['search', 'recent_posts'],\n";
        $config_content .= "        'footer1' => [],\n";
        $config_content .= "        'footer2' => [],\n";
        $config_content .= "        'footer3' => [],\n";
        $config_content .= "    ],\n";
        $config_content .= "    'enabled_plugins' => ['prism'],\n";
        $config_content .= "];\n";

        if (file_put_contents($config_file, $config_content)) {
            $success = true;
        } else {
            $errors[] = "Failed to write config file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Installer</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .install-box { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { font-size: 1.5rem; margin-bottom: 1.5rem; text-align: center; }
        .error { color: #d9534f; background: #f2dede; padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.9rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 1rem; border-radius: 4px; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: #007bff; border: none; color: #fff; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="install-box">
        <h1>CMS Installation</h1>

        <?php if ($success): ?>
            <div class="success">
                <p>Installation successful!</p>
                <p><strong>Security Notice:</strong> Please delete <code>install.php</code> manually.</p>
                <p><a href="admin/login.php">Go to Admin Panel</a></p>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" required>
                </div>
                <div class="form-group">
                    <label for="admin_user">Admin Username</label>
                    <input type="text" id="admin_user" name="admin_user" required>
                </div>
                <div class="form-group">
                    <label for="admin_pass">Admin Password</label>
                    <input type="password" id="admin_pass" name="admin_pass" required>
                </div>
                <button type="submit">Install CMS</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
