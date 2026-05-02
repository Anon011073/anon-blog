<?php
require_once __DIR__ . '/../../../app/auth.php';
require_once __DIR__ . '/../../../app/functions.php';

require_login('plugins');

$config = load_config();
$cache_config = $config['cache_options'] ?? [
    'enabled' => true,
    'ttl' => 3600
];

if (isset($_GET['clear'])) {
    $cache_dir = __DIR__ . '/../../../config/cache/';
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '*.html');
        foreach ($files as $file) if (is_file($file)) unlink($file);
    }
    header("Location: settings.php?success=cleared");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) die('CSRF Failed');

    $new_cache = [
        'enabled' => isset($_POST['enabled']),
        'ttl' => (int)$_POST['ttl']
    ];

    update_config(['cache_options' => $new_cache]);
    header("Location: settings.php?success=saved");
    die();
}

$cache_dir = __DIR__ . '/../../../config/cache/';
$cache_count = is_dir($cache_dir) ? count(glob($cache_dir . '*.html')) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cache Settings - AnonBlog Admin</title>
    <link rel="stylesheet" href="../../../admin/style.css">
    <style>
        .main-content { margin-left: 310px; margin-top: 60px; padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="number"], select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 15px; cursor: pointer; border-radius: 4px; border: none; }
        .btn-primary { background: #2271b1; color: white; }
        .btn-danger { background: #dc3545; color: white; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .warning-box { background: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; margin-bottom: 20px; color: #856404; }
    </style>
</head>
<body>
    <?php include "../../../admin/sidebar.php"; ?>
    <div class="main-content">
        <h1>Cache Management</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_GET['success'] == 'cleared' ? 'Cache cleared successfully!' : 'Settings saved!'; ?>
            </div>
        <?php endif; ?>

        <div class="warning-box">
            <strong>⚠️ Note:</strong> Caching improves speed but might show outdated content or styles after you change settings.
            <strong>The cache is now automatically cleared whenever you update plugin settings.</strong>
        </div>

        <div class="card">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="enabled" <?php echo ($cache_config['enabled'] ?? true) ? 'checked' : ''; ?>>
                        Enable Caching (Global)
                    </label>
                    <p class="help">If disabled, the site will always render pages from scratch (recommended during development).</p>
                </div>

                <div class="form-group">
                    <label>Cache Expiration (Seconds)</label>
                    <input type="number" name="ttl" value="<?php echo $cache_config['ttl'] ?? 3600; ?>" min="60">
                    <p class="help">How long a cached page remains valid. 3600 = 1 hour.</p>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                    <a href="settings.php?clear=1" class="btn btn-danger" onclick="return confirm('Clear all cached files?')">Clear Cache Now (<?php echo $cache_count; ?> files)</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
