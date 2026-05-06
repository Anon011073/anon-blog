<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_cache'])) {
    $cache_dir = __DIR__ . '/../../content/cache';
    if (is_dir($cache_dir)) {
        foreach (glob($cache_dir . '/*.html') as $file) unlink($file);
    }
    echo "<script>window.location.href='settings.php?plugin=fast-cache&success=cleared';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en"><head><link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div style="margin-left:310px; padding:2rem;">
    <h1>Fast Cache Settings</h1>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Cache cleared successfully.</div><?php endif; ?>
    <div class="card" style="background:#fff; padding:20px; border-radius:8px;">
        <p>Status: <span style="color:green; font-weight:bold;">Active</span></p>
        <p>HTML caching is improving your site's performance. The cache is automatically cleared when you save posts or change settings.</p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            <button type="submit" name="clear_cache" class="btn btn-danger">Clear Cache Manually</button>
            <a href="plugins.php" class="btn">&larr; Back</a>
        </form>
    </div>
</div>
</body></html>
