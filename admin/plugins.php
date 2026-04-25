<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login();

$config = load_config();
$error = '';
$success = '';

if (isset($_GET['toggle'])) {
    $plugin_name = $_GET['toggle'];
    $enabled_plugins = $config['enabled_plugins'] ?? [];

    if (in_array($plugin_name, $enabled_plugins)) {
        $enabled_plugins = array_diff($enabled_plugins, [$plugin_name]);
    } else {
        $enabled_plugins[] = $plugin_name;
    }

    if (update_config(['enabled_plugins' => array_values($enabled_plugins)])) {
        redirect('plugins.php?success=1');
    }
}

$plugins = [];
$plugin_dirs = glob(__DIR__ . '/../plugins/*', GLOB_ONLYDIR);
foreach ($plugin_dirs as $dir) {
    $name = basename($dir);
    $plugin_file = $dir . '/plugin.php';
    if (file_exists($plugin_file)) {
        $data = include $plugin_file;
        $plugins[$name] = $data;
    }
}

$enabled_plugins = $config['enabled_plugins'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plugins - Admin Panel</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .sidebar { width: 250px; background: #333; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .main-content { flex: 1; padding: 2rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1rem; }
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-danger { background: #d9534f; color: #fff; }
        .plugin-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 1rem 0; }
        .plugin-info h3 { margin: 0; font-size: 1.1rem; }
        .plugin-info p { margin: 5px 0; color: #666; font-size: 0.9rem; }
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
            <li><a href="settings.php">Settings</a></li><li><a href="theme_options.php">Theme Options</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="widgets.php">Widgets</a></li>
            <li><a href="plugins.php" class="active">Plugins</a></li>
            <li><a href="/" target="_blank">View Site</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Plugins</h1>

        <div class="card">
            <?php if (empty($plugins)): ?>
                <p>No plugins found.</p>
            <?php else: ?>
                <?php foreach ($plugins as $name => $data):
                    $is_enabled = in_array($name, $enabled_plugins);
                ?>
                    <div class="plugin-item">
                        <div class="plugin-info">
                            <h3><?php echo htmlspecialchars($data['name'] ?? $name); ?></h3>
                            <p><?php echo htmlspecialchars($data['description'] ?? ''); ?></p>
                            <small>By <?php echo htmlspecialchars($data['author'] ?? 'Unknown'); ?></small>
                        </div>
                        <div class="plugin-actions">
                            <?php if ($is_enabled): ?>
                                <a href="plugins.php?toggle=<?php echo $name; ?>" class="btn btn-danger">Deactivate</a>
                            <?php else: ?>
                                <a href="plugins.php?toggle=<?php echo $name; ?>" class="btn btn-success">Activate</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
