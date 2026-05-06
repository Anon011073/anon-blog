<div class="card" style="background:#fff; padding:20px; border-radius:8px;">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

        <div class="form-group">
            <label>Number of Columns</label>
            <input type="number" name="gallery_columns" value="<?php echo $config['gallery_columns'] ?? 3; ?>" min="1" max="6" style="width:100%; padding:10px;">
        </div>

        <div class="form-group" style="margin-top:20px;">
            <label>Gap between images (e.g. 10px or 1rem)</label>
            <input type="text" name="gallery_gap" value="<?php echo htmlspecialchars($config['gallery_gap'] ?? '10px'); ?>" style="width:100%; padding:10px;">
        </div>

        <br>
        <button type="submit" name="save_gallery" class="btn btn-primary">Save Settings</button>
    </form>
</div>

<div class="card" style="margin-top:20px;">
    <h3>Usage</h3>
    <p>In your post editor, use the following shortcode:</p>
    <code>[gallery ids="image1.jpg,image2.png,image3.webp"]</code>
    <p>You can find the filenames in the <strong>Media</strong> library.</p>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_gallery'])) {
    update_config([
        'gallery_columns' => (int)$_POST['gallery_columns'],
        'gallery_gap' => $_POST['gallery_gap']
    ]);
    echo "<script>window.location.href='settings.php?plugin=simple-gallery&success=1';</script>";
    exit;
}
?>
