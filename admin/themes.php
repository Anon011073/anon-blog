<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login('themes');

$config = load_config();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['theme_zip'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $file = $_FILES['theme_zip'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($ext === 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($file['tmp_name']) === TRUE) {
                $temp_extract = __DIR__ . '/../themes/temp_' . uniqid();
                mkdir($temp_extract, 0755, true);
                $zip->extractTo($temp_extract);
                $zip->close();

                $found_path = '';
                $it = new RecursiveDirectoryIterator($temp_extract);
                foreach (new RecursiveIteratorIterator($it) as $f) {
                    if (basename($f) === 'index.php') {
                        $found_path = dirname($f);
                        break;
                    }
                }

                if ($found_path) {
                    $theme_slug = basename($found_path);
                    $dest = __DIR__ . '/../themes/' . $theme_slug;
                    if (!is_dir($dest)) {
                        rename($found_path, $dest);
                        $success = "Theme '" . $theme_slug . "' installed successfully!";
                    } else {
                        $error = "A theme with the folder name '$theme_slug' already exists.";
                    }
                } else {
                    $error = "Invalid theme ZIP: 'index.php' not found.";
                }

                // Cleanup
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
    $theme_name = basename($_GET['delete']);
    if (!in_array($theme_name, ['default', 'starter'])) {
        $theme_dir = __DIR__ . '/../themes/' . $theme_name;
        if (is_dir($theme_dir)) {
            // Helper to delete dir
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($theme_dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
            rmdir($theme_dir);
            redirect('themes.php?success=Deleted');
        }
    }
}

if (isset($_GET['activate']) && isset($_GET['token'])) {
    if (!verify_csrf_token($_GET['token'])) {
        die('CSRF token validation failed.');
    }
    $theme_name = $_GET['activate'];
    if (update_config(['theme' => $theme_name, 'theme_options' => []])) {
        redirect('themes.php?success=Theme Activated');
    }
}

$themes = [];
$theme_dirs = glob(__DIR__ . '/../themes/*', GLOB_ONLYDIR);
foreach ($theme_dirs as $dir) {
    $name = basename($dir);
    // Support both theme.php and theme-config.php for metadata
    $meta_file = file_exists($dir . '/theme-config.php') ? $dir . '/theme-config.php' : (file_exists($dir . '/theme.php') ? $dir . '/theme.php' : null);
    if ($meta_file) {
        $data = include $meta_file;
        $themes[$name] = $data;
    } else {
        // Fallback for default theme which might not have a config file yet
        $themes[$name] = ['name' => ucfirst($name), 'author' => 'System'];
    }
}

$enabled_themes = $config['enabled_themes'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Themes - Admin Panel</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .sidebar { width: 250px; background: #333; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .main-content { flex: 1; padding: 2rem; margin-left: 280px; margin-top: 50px; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1rem; }
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-danger { background: #d9534f; color: #fff; }
        .theme-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 1rem 0; }
        .theme-info h3 { margin: 0; font-size: 1.1rem; }
        .theme-info p { margin: 5px 0; color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Themes</h1>

        <?php if ($error): ?>
            <div class="error" style="color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success" style="color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <h3>Add New Theme</h3>
            <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <input type="file" name="theme_zip" accept=".zip" required>
                <button type="submit" class="btn btn-primary" style="background: #007bff; color: #fff;">Upload & Install</button>
            </form>
        </div>

        <div class="card">
            <?php if (empty($themes)): ?>
                <p>No themes found.</p>
            <?php else: ?>
                <?php foreach ($themes as $name => $data):
                    $is_active = ($config['theme'] ?? 'default') === $name;
                ?>
                    <div class="theme-item" style="<?php echo $is_active ? 'border-left: 5px solid #28a745; background: #f9fff9;' : ''; ?>">
                        <div class="theme-info">
                            <h3><?php echo htmlspecialchars($data['name'] ?? $name); ?> <?php if ($is_active) echo '<span style="font-size: 0.7rem; background: #28a745; color: #fff; padding: 2px 5px; border-radius: 3px;">ACTIVE</span>'; ?></h3>
                            <p><?php echo htmlspecialchars($data['description'] ?? ''); ?></p>
                            <small>By <?php echo htmlspecialchars($data['author'] ?? 'Unknown'); ?></small>
                        </div>
                        <div class="theme-actions" style="display: flex; gap: 5px;">
                            <?php if (!$is_active): ?>
                                <a href="themes.php?activate=<?php echo $name; ?>&token=<?php echo get_csrf_token(); ?>" class="btn btn-success">Activate</a>
                                <?php if (!in_array($name, ['default', 'starter'])): ?>
                                    <a href="themes.php?delete=<?php echo $name; ?>&token=<?php echo get_csrf_token(); ?>" class="btn btn-danger" onclick="return confirm('Delete this theme permanently?')">Delete</a>
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
