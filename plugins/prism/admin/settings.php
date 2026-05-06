<div class="card" style="background:#fff; padding:20px; border-radius:8px;">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

        <div class="form-group">
            <label>Choose a Syntax Theme</label>
            <select name="prism_theme" style="width:100%; padding:12px; margin-top:10px; border:1px solid #ccc; border-radius:4px;">
                <option value="prism" <?php echo ($config['prism_theme'] ?? '') === 'prism' ? 'selected' : ''; ?>>Prism (Default Light)</option>
                <option value="dark" <?php echo ($config['prism_theme'] ?? '') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                <option value="funky" <?php echo ($config['prism_theme'] ?? '') === 'funky' ? 'selected' : ''; ?>>Funky</option>
                <option value="okaidia" <?php echo ($config['prism_theme'] ?? '') === 'okaidia' ? 'selected' : ''; ?>>Okaidia (Monokai)</option>
                <option value="twilight" <?php echo ($config['prism_theme'] ?? '') === 'twilight' ? 'selected' : ''; ?>>Twilight</option>
                <option value="coy" <?php echo ($config['prism_theme'] ?? '') === 'coy' ? 'selected' : ''; ?>>Coy</option>
                <option value="solarizedlight" <?php echo ($config['prism_theme'] ?? '') === 'solarizedlight' ? 'selected' : ''; ?>>Solarized Light</option>
                <option value="tomorrow" <?php echo ($config['prism_theme'] ?? '') === 'tomorrow' ? 'selected' : ''; ?>>Tomorrow Night</option>
            </select>
        </div>

        <div class="form-group" style="margin-top:20px;">
            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="prism_line_numbers" <?php echo ($config['prism_line_numbers'] ?? false) ? 'checked' : ''; ?> style="width:20px; height:20px;">
                <span>Display Line Numbers</span>
            </label>
        </div>

        <br>
        <button type="submit" name="save_prism" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<div class="card" style="margin-top:20px; background:#f9f9f9; padding:20px; border-radius:8px;">
    <h3>How to use</h3>
    <p>Wrap your code blocks like this in the editor:</p>
    <pre style="background:#eee; padding:10px; border-radius:4px;"><code>&lt;pre&gt;&lt;code class="language-php"&gt;
&lt;?php echo "Hello World"; ?&gt;
&lt;/code&gt;&lt;/pre&gt;</code></pre>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_prism'])) {
    update_config([
        'prism_theme' => $_POST['prism_theme'],
        'prism_line_numbers' => isset($_POST['prism_line_numbers'])
    ]);
    echo "<script>window.location.href='settings.php?plugin=prism&success=1';</script>";
    exit;
}
?>
