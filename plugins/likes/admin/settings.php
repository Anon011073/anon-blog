<div class="card" style="background:#fff; padding:20px; border-radius:8px;">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
            <input type="checkbox" name="likes_enabled" <?php echo ($config['likes_enabled'] ?? true) ? 'checked' : ''; ?> style="width:20px; height:20px;">
            <span>Enable Post Voting (Likes/Dislikes)</span>
        </label>
        <br>
        <button type="submit" name="save_likes" class="btn btn-primary">Save Settings</button>
    </form>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_likes'])) {
    update_config(['likes_enabled' => isset($_POST['likes_enabled'])]);
    echo "<script>window.location.href='settings.php?plugin=likes&success=1';</script>";
    exit;
}
?>
