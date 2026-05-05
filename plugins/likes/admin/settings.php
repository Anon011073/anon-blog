<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_likes'])) {
    update_config(['likes_enabled' => isset($_POST['likes_enabled'])]);
    echo "<script>window.location.href='settings.php?plugin=likes&success=1';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>Likes Settings</title><link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div style="margin-left:310px; padding:2rem;">
    <h1>Likes/Dislikes Settings</h1>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Settings saved.</div><?php endif; ?>
    <div class="card" style="background:#fff; padding:20px; border-radius:8px;">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            <label><input type="checkbox" name="likes_enabled" <?php echo ($config['likes_enabled'] ?? true) ? 'checked' : ''; ?>> Enable post voting</label>
            <br><br>
            <button type="submit" name="save_likes" class="btn btn-primary">Save Settings</button>
            <a href="plugins.php" class="btn">&larr; Back</a>
        </form>
    </div>
</div>
</body></html>
