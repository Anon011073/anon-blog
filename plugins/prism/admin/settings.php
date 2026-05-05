<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_prism'])) {
    update_config(['prism_theme' => $_POST['prism_theme']]);
    echo "<script>window.location.href='settings.php?plugin=prism&success=1';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en"><head><link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div style="margin-left:310px; padding:2rem;">
    <h1>Prism Syntax Highlighter Settings</h1>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Settings saved.</div><?php endif; ?>
    <div class="card" style="background:#fff; padding:20px; border-radius:8px;">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            <label>Prism Theme</label>
            <select name="prism_theme" style="width:100%; padding:10px; margin-top:10px;">
                <option value="prism" <?php echo ($config['prism_theme'] ?? '') === 'prism' ? 'selected' : ''; ?>>Default (Light)</option>
                <option value="okaidia" <?php echo ($config['prism_theme'] ?? '') === 'okaidia' ? 'selected' : ''; ?>>Okaidia (Dark)</option>
                <option value="tomorrow" <?php echo ($config['prism_theme'] ?? '') === 'tomorrow' ? 'selected' : ''; ?>>Tomorrow Night</option>
            </select>
            <br><br>
            <button type="submit" name="save_prism" class="btn btn-primary">Save Settings</button>
            <a href="plugins.php" class="btn">&larr; Back</a>
        </form>
    </div>
</div>
</body></html>
