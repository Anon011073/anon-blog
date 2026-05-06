<div class="card" style="background:#fff; padding:20px; border-radius:8px;">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
            <input type="checkbox" name="private_mode" <?php echo ($config['private_mode'] ?? false) ? 'checked' : ''; ?> style="width:20px; height:20px;">
            <span>Enable Private Mode (Require login to view site)</span>
        </label>
        <br>
        <button type="submit" name="save_private" class="btn btn-primary">Save Settings</button>
    </form>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_private'])) {
    update_config(['private_mode' => isset($_POST['private_mode'])]);
    echo "<script>window.location.href='settings.php?plugin=anon-private&success=1';</script>";
    exit;
}
?>
