<div class="card" style="background:#fff; padding:20px; border-radius:8px;">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

        <div class="form-group">
            <label><input type="checkbox" name="header_sticky_global" <?php echo ($config['header_sticky_global'] ?? false) ? 'checked' : ''; ?>> Enable Sticky Header (Global Override)</label>
        </div>

        <div class="form-group" style="margin-top:15px;">
            <label><input type="checkbox" name="header_blur_global" <?php echo ($config['header_blur_global'] ?? false) ? 'checked' : ''; ?>> Enable Glassmorphism/Blur (Global Override)</label>
        </div>

        <div class="form-group" style="margin-top:20px;">
            <label>Menu Alignment</label>
            <select name="header_menu_align" style="width:100%; padding:10px; margin-top:5px;">
                <option value="right" <?php echo ($config['header_menu_align'] ?? 'right') === 'right' ? 'selected' : ''; ?>>Right</option>
                <option value="center" <?php echo ($config['header_menu_align'] ?? 'center') === 'center' ? 'selected' : ''; ?>>Center</option>
                <option value="left" <?php echo ($config['header_menu_align'] ?? 'left') === 'left' ? 'selected' : ''; ?>>Left</option>
            </select>
        </div>

        <br>
        <button type="submit" name="save_header" class="btn btn-primary">Save Header Config</button>
    </form>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_header'])) {
    update_config([
        'header_sticky_global' => isset($_POST['header_sticky_global']),
        'header_blur_global' => isset($_POST['header_blur_global']),
        'header_menu_align' => $_POST['header_menu_align']
    ]);
    echo "<script>window.location.href='settings.php?plugin=header-master&success=1';</script>";
    exit;
}
?>
