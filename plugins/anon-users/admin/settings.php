<div class="card" style="background:#fff; padding:20px; border-radius:8px;">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
            <input type="checkbox" name="allow_registration" <?php echo ($config['allow_registration'] ?? true) ? 'checked' : ''; ?> style="width:20px; height:20px;">
            <span>Allow Public Registration</span>
        </label>
        <br>
        <button type="submit" name="save_users" class="btn btn-primary">Save Settings</button>
        <a href="anon-users/admin/manage.php" class="btn btn-secondary">Manage Users & News</a>
    </form>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_users'])) {
    update_config(['allow_registration' => isset($_POST['allow_registration'])]);
    echo "<script>window.location.href='settings.php?plugin=anon-users&success=1';</script>";
    exit;
}
?>
