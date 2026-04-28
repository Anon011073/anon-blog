<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login('backup');

$config = load_config();
$error = '';
$success = '';

// Check if ZipArchive is available
if (!class_exists('ZipArchive')) {
    $error = "ZipArchive PHP extension is not enabled. Backup and Restore will not work.";
}

// Handle Backup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'backup') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $to_backup = $_POST['items'] ?? [];
    if (empty($to_backup)) {
        $error = "Please select at least one item to backup.";
    } else {
        $zip = new ZipArchive();
        $filename = "backup_" . date('Y-m-d_H-i-s') . ".zip";
        $filepath = sys_get_temp_dir() . '/' . $filename;

        if ($zip->open($filepath, ZipArchive::CREATE) !== TRUE) {
            $error = "Cannot create zip file.";
        } else {
            if (in_array('config', $to_backup)) {
                $zip->addFile(__DIR__ . '/../config/config.php', 'config/config.php');
            }
            if (in_array('posts', $to_backup)) {
                $posts = glob(__DIR__ . '/../content/posts/*.json');
                foreach ($posts as $f) $zip->addFile($f, 'content/posts/' . basename($f));
            }
            if (in_array('pages', $to_backup)) {
                $pages = glob(__DIR__ . '/../content/pages/*.json');
                foreach ($pages as $f) $zip->addFile($f, 'content/pages/' . basename($f));
            }
            if (in_array('comments', $to_backup)) {
                $comments = glob(__DIR__ . '/../content/comments/*.json');
                foreach ($comments as $f) $zip->addFile($f, 'content/comments/' . basename($f));
            }
            if (in_array('media', $to_backup)) {
                $media = glob(__DIR__ . '/../uploads/*');
                foreach ($media as $f) if (is_file($f)) $zip->addFile($f, 'uploads/' . basename($f));
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            unlink($filepath);
            exit;
        }
    }
}

// Handle Restore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restore') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please upload a valid backup ZIP file.";
    } else {
        $zip = new ZipArchive();
        if ($zip->open($_FILES['backup_file']['tmp_name']) === TRUE) {
            // Validate ZIP content basics
            $items_to_restore = $_POST['items'] ?? [];
            if (empty($items_to_restore)) {
                $error = "Please select at least one item to restore.";
            } else {
                foreach ($items_to_restore as $item) {
                    if ($item === 'config') {
                        $zip->extractTo(__DIR__ . '/../', 'config/config.php');
                    } elseif ($item === 'posts') {
                        array_map('unlink', glob(__DIR__ . '/../content/posts/*.json'));
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $name = $zip->getNameIndex($i);
                            if (strpos($name, 'content/posts/') === 0) $zip->extractTo(__DIR__ . '/../', $name);
                        }
                    } elseif ($item === 'pages') {
                        array_map('unlink', glob(__DIR__ . '/../content/pages/*.json'));
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $name = $zip->getNameIndex($i);
                            if (strpos($name, 'content/pages/') === 0) $zip->extractTo(__DIR__ . '/../', $name);
                        }
                    } elseif ($item === 'comments') {
                        array_map('unlink', glob(__DIR__ . '/../content/comments/*.json'));
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $name = $zip->getNameIndex($i);
                            if (strpos($name, 'content/comments/') === 0) $zip->extractTo(__DIR__ . '/../', $name);
                        }
                    } elseif ($item === 'media') {
                        array_map('unlink', glob(__DIR__ . '/../uploads/*'));
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $name = $zip->getNameIndex($i);
                            if (strpos($name, 'uploads/') === 0) $zip->extractTo(__DIR__ . '/../', $name);
                        }
                    }
                }
                $zip->close();
                $success = "Restoration successful! You might need to log in again if config was restored.";
                $config = load_config();
            }
        } else {
            $error = "Failed to open ZIP file.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup & Restore - Admin Panel</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .main-content { flex: 1; padding: 2rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .sidebar { width: 250px; background: #222; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 4px; cursor: pointer; border: none; font-size: 1rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-danger { background: #d9534f; color: #fff; }
        .error { color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .item-list { margin: 15px 0; }
        .item-list label { display: block; margin-bottom: 10px; cursor: pointer; }
        .warning { color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Backup & Restore</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="card">
                <h2>Create Backup</h2>
                <p>Generate a ZIP file containing your blog's data.</p>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    <input type="hidden" name="action" value="backup">
                    <div class="item-list">
                        <label><input type="checkbox" name="items[]" value="config" checked> Config & Settings (includes Custom CSS, Site Title)</label>
                        <label><input type="checkbox" name="items[]" value="posts" checked> Posts</label>
                        <label><input type="checkbox" name="items[]" value="pages" checked> Pages</label>
                        <label><input type="checkbox" name="items[]" value="comments" checked> Comments</label>
                        <label>
                            <input type="checkbox" name="items[]" value="media" checked> Media Uploads
                            <div class="warning" style="margin: 5px 0 0 25px;">Large blogs may result in very large ZIP files.</div>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Download Backup ZIP</button>
                </form>
            </div>

            <div class="card">
                <h2>Restore Backup</h2>
                <div class="warning">
                    <strong>WARNING:</strong> Restoring will <strong>DELETE</strong> current data in the selected categories and replace it with the backup content. This cannot be undone.
                </div>
                <form method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you absolutely sure? Current content in selected categories will be PERMANENTLY DELETED.')">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    <input type="hidden" name="action" value="restore">
                    <div class="form-group">
                        <label for="backup_file">Upload Backup ZIP:</label>
                        <input type="file" id="backup_file" name="backup_file" accept=".zip" required style="margin-top: 10px;">
                    </div>
                    <div class="item-list">
                        <label><input type="checkbox" name="items[]" value="config"> Config & Settings</label>
                        <label><input type="checkbox" name="items[]" value="posts"> Posts</label>
                        <label><input type="checkbox" name="items[]" value="pages"> Pages</label>
                        <label><input type="checkbox" name="items[]" value="comments"> Comments</label>
                        <label><input type="checkbox" name="items[]" value="media"> Media Uploads</label>
                    </div>
                    <button type="submit" class="btn btn-danger">Start Restoration</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
