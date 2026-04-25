<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login();

$config = load_config();
$error = '';
$success = '';

$defaults = [
    'theme_font' => 'sans-serif',
    'primary_color' => '#007bff',
    'container_width' => '1100px',
    'sidebar_width' => '300px',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    if (isset($_POST['reset'])) {
        $new_theme_config = $defaults;
    } else {
        $new_theme_config = [
            'theme_font' => $_POST['theme_font'] ?? $defaults['theme_font'],
            'primary_color' => $_POST['primary_color'] ?? $defaults['primary_color'],
            'container_width' => $_POST['container_width'] ?? $defaults['container_width'],
            'sidebar_width' => $_POST['sidebar_width'] ?? $defaults['sidebar_width'],
        ];
    }

    if (update_config($new_theme_config)) {
        $success = "Theme options updated successfully.";
        $config = load_config();
    } else {
        $error = "Failed to update theme options.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Options - Admin Panel</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .sidebar { width: 250px; background: #333; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .main-content { flex: 1; padding: 2rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], select, input[type="color"] { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="color"] { width: 100%; height: 50px; cursor: pointer; border: 1px solid #ccc; padding: 2px; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 1rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .error { color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><?php echo htmlspecialchars($config['site_name']); ?></h2>
        <ul>
            <li><a href="index.php">Posts</a></li>
            <li><a href="pages.php">Pages</a></li>
            <li><a href="media.php">Media</a></li>
            <li><a href="comments.php">Comments</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="theme_options.php" class="active">Theme Options</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="widgets.php">Widgets</a></li>
            <li><a href="plugins.php">Plugins</a></li>
            <li><a href="/" target="_blank">View Site</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Theme Options</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <div class="form-group">
                    <label for="theme_font">Font Family</label>
                    <select id="theme_font" name="theme_font">
                        <option value="sans-serif" <?php echo ($config['theme_font'] ?? '') === 'sans-serif' ? 'selected' : ''; ?>>Sans-serif</option>
                        <option value="serif" <?php echo ($config['theme_font'] ?? '') === 'serif' ? 'selected' : ''; ?>>Serif</option>
                        <option value="monospace" <?php echo ($config['theme_font'] ?? '') === 'monospace' ? 'selected' : ''; ?>>Monospace</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="primary_color">Primary Accent Color</label>
                    <input type="color" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars($config['primary_color'] ?? '#007bff'); ?>">
                    <small>Used for buttons, links, and accents.</small>
                </div>

                <div class="form-group">
                    <label for="container_width">Container Max-Width</label>
                    <input type="text" id="container_width" name="container_width" value="<?php echo htmlspecialchars($config['container_width'] ?? '1100px'); ?>">
                </div>

                <div class="form-group">
                    <label for="sidebar_width">Sidebar Width</label>
                    <input type="text" id="sidebar_width" name="sidebar_width" value="<?php echo htmlspecialchars($config['sidebar_width'] ?? '300px'); ?>">
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="submit" name="reset" class="btn btn-secondary" onclick="return confirm('Reset theme settings to default?')">Reset to Default</button>
            </form>
        </div>
    </div>
</body>
</html>
