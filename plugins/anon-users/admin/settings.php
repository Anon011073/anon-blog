<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_users'])) {
    update_config(['allow_registration' => isset($_POST['allow_registration'])]);
    echo "<script>window.location.href='settings.php?plugin=anon-users&success=1';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>AnonUsers Settings</title><link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div style="margin-left:310px; padding:2rem;">
    <h1>AnonUsers Pro Settings</h1>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Settings saved.</div><?php endif; ?>
    <div class="card" style="background:#fff; padding:20px; border-radius:8px;">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            <label><input type="checkbox" name="allow_registration" <?php echo ($config['allow_registration'] ?? true) ? 'checked' : ''; ?>> Allow public registration</label>
            <br><br>
            <button type="submit" name="save_users" class="btn btn-primary">Save Settings</button>
            <a href="anon-users/admin/manage.php" class="btn btn-secondary">Manage Users & News</a>
            <a href="plugins.php" class="btn">&larr; Back</a>
        </form>
    </div>
</div>
</body></html>
