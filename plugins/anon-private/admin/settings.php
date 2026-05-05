<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_private'])) {
    update_config(['private_mode' => isset($_POST['private_mode'])]);
    echo "<script>window.location.href='settings.php?plugin=anon-private&success=1';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en"><head><link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div style="margin-left:310px; padding:2rem;">
    <h1>AnonPrivate Settings</h1>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Settings saved.</div><?php endif; ?>
    <div class="card" style="background:#fff; padding:20px; border-radius:8px;">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            <label><input type="checkbox" name="private_mode" <?php echo ($config['private_mode'] ?? false) ? 'checked' : ''; ?>> Enable Private Site Mode (Requires login to view site)</label>
            <br><br>
            <button type="submit" name="save_private" class="btn btn-primary">Save Settings</button>
            <a href="plugins.php" class="btn">&larr; Back</a>
        </form>
    </div>
</div>
</body></html>
