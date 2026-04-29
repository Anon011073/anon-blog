<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login('plugins');

$config = load_config();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['plugin_zip'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $file = $_FILES['plugin_zip'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($ext === 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($file['tmp_name']) === TRUE) {
                // Extract to a temporary directory first to determine the structure
                $temp_extract = __DIR__ . '/../plugins/temp_' . uniqid();
                mkdir($temp_extract, 0755, true);
                $zip->extractTo($temp_extract);
                $zip->close();

                // Find the directory containing plugin.php
                $found_path = '';
                $it = new RecursiveDirectoryIterator($temp_extract);
                foreach (new RecursiveIteratorIterator($it) as $f) {
                    if (basename($f) === 'plugin.php') {
                        $found_path = dirname($f);
                        break;
                    }
                }

                if ($found_path) {
                    $plugin_slug = basename($found_path);
                    $dest = __DIR__ . '/../plugins/' . $plugin_slug;
                    if (!is_dir($dest)) {
                        rename($found_path, $dest);
                        $success = "Plugin '" . $plugin_slug . "' installed successfully!";
                    } else {
                        $error = "A plugin with the folder name '$plugin_slug' already exists.";
                    }
                } else {
                    $error = "Invalid plugin ZIP: 'plugin.php' not found.";
                }

                // Cleanup temp dir
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($temp_extract, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    if (file_exists($fileinfo->getRealPath())) $todo($fileinfo->getRealPath());
                }
                if (is_dir($temp_extract)) rmdir($temp_extract);
            } else {
                $error = "Failed to open ZIP file.";
            }
        } else {
            $error = "Please upload a valid ZIP file.";
        }
    } else {
        $error = "Upload error: " . $file['error'];
    }
}

if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (!verify_csrf_token($_GET['token'])) {
        die('CSRF token validation failed.');
    }
    $plugin_name = basename($_GET['delete']);
    if (!in_array($plugin_name, ['prism', 'contact'])) {
        $plugin_dir = __DIR__ . '/../plugins/' . $plugin_name;
        if (is_dir($plugin_dir)) {
            // Helper to delete dir
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($plugin_dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
            rmdir($plugin_dir);
            redirect('plugins.php?success=Deleted');
        }
    }
}

if (isset($_GET['toggle']) && isset($_GET['token'])) {
    if (!verify_csrf_token($_GET['token'])) {
        die('CSRF token validation failed.');
    }
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
        .main-content { flex: 1; padding: 2rem; margin-left: 250px; margin-top: 50px; }
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
<?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Plugins</h1>

        <?php if ($error): ?>
            <div class="error" style="color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success" style="color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <h3>Add New Plugin</h3>
            <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <input type="file" name="plugin_zip" accept=".zip" required>
                <button type="submit" class="btn btn-primary" style="background: #007bff; color: #fff;">Upload & Install</button>
            </form>
        </div>

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
                            <?php if ($is_enabled && $name === 'anon-users'): ?>
                                <br><a href="../plugins/anon-users/admin/manage.php" style="font-size: 0.8rem; color: #007bff;">Manage Users & News</a>
                            <?php endif; ?>
                        </div>
                        <div class="plugin-actions" style="display: flex; gap: 5px;">
                            <?php if ($is_enabled): ?>
                                <a href="plugins.php?toggle=<?php echo $name; ?>&token=<?php echo get_csrf_token(); ?>" class="btn btn-danger">Deactivate</a>
                            <?php else: ?>
                                <a href="plugins.php?toggle=<?php echo $name; ?>&token=<?php echo get_csrf_token(); ?>" class="btn btn-success">Activate</a>
                                <?php if (!in_array($name, ['prism', 'contact'])): ?>
                                    <a href="plugins.php?delete=<?php echo $name; ?>&token=<?php echo get_csrf_token(); ?>" class="btn btn-danger" onclick="return confirm('Delete this plugin permanently?')">Delete</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
