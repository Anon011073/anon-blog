<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login('theme_options');

$config = load_config();
$error = '';
$success = '';

$current_theme = $config['theme'] ?? 'default';
$theme_config_file = __DIR__ . '/../themes/' . $current_theme . '/theme-config.php';
$theme_meta = file_exists($theme_config_file) ? include $theme_config_file : null;

if (!$theme_meta) {
    die("Theme configuration not found for '{$current_theme}'.");
}

$google_fonts = [
    'Inter', 'Poppins', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Oswald',
    'Raleway', 'PT Sans', 'Merriweather', 'Noto Sans', 'Playfair Display',
    'Ubuntu', 'Lora', 'Quicksand', 'Fira Sans', 'Work Sans', 'Libre Baskerville',
    'Josefin Sans', 'Archivo'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    if (isset($_POST['reset'])) {
        $reset_options = [];
        foreach ($theme_meta['options'] as $opt) {
            $name = $opt['name'];
            $reset_options[$name] = $opt['default'] ?? '';
        }
        update_config(['theme_options' => $reset_options]);
        $success = "Options reset to defaults.";
    } else {
        $new_options = [];
        foreach ($theme_meta['options'] as $opt) {
            $name = $opt['name'];
            if ($opt['type'] === 'checkbox') {
                $new_options[$name] = isset($_POST[$name]);
            } else {
                $new_options[$name] = $_POST[$name] ?? ($opt['default'] ?? '');
            }
        }
        update_config(['theme_options' => $new_options]);
        $success = "Options updated successfully.";
    }
    $config = load_config();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Options - AnonBlog Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .main-content { margin-left: 310px; margin-top: 60px; padding: 20px; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], input[type="number"], select, input[type="color"], textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="color"] { height: 50px; padding: 2px; }
        .alert { padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
        .alert-success { background: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; }
    </style>
</head>
<body>
    <?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Theme Options (<?php echo htmlspecialchars($theme_meta['name']); ?>)</h1>

        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

        <div class="card">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <?php foreach ($theme_meta['options'] as $opt):
                    $name = $opt['name'];
                    // Fallback to legacy config keys if theme_options is not set (for migration)
                    if (isset($config['theme_options'][$name])) {
                        $val = $config['theme_options'][$name];
                    } else {
                        $val = $config[$name] ?? ($opt['default'] ?? '');
                    }
                ?>
                    <div class="form-group">
                        <label><?php echo $opt['label']; ?></label>
                        <?php if ($opt['type'] === 'text' || $opt['type'] === 'number'): ?>
                            <input type="<?php echo $opt['type']; ?>" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($val); ?>">
                        <?php elseif ($opt['type'] === 'font'): ?>
                            <select name="<?php echo $name; ?>">
                                <?php foreach ($google_fonts as $font): ?>
                                    <option value="<?php echo $font; ?>" <?php echo $val === $font ? 'selected' : ''; ?>><?php echo $font; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($opt['type'] === 'color'): ?>
                            <input type="color" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($val); ?>">
                        <?php elseif ($opt['type'] === 'checkbox'): ?>
                            <input type="checkbox" name="<?php echo $name; ?>" <?php echo $val ? 'checked' : ''; ?> value="1">
                        <?php elseif ($opt['type'] === 'select'): ?>
                            <select name="<?php echo $name; ?>">
                                <?php foreach ($opt['options'] as $k => $v): ?>
                                    <option value="<?php echo $k; ?>" <?php echo $val == $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($opt['type'] === 'textarea'): ?>
                            <textarea name="<?php echo $name; ?>" style="height: 100px; font-family: monospace;"><?php echo htmlspecialchars($val); ?></textarea>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="submit" name="reset" class="btn btn-secondary" onclick="return confirm('Reset to theme defaults?')">Reset to Default</button>
            </form>
        </div>
    </div>
</body>
</html>
