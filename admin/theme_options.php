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

// Use custom theme options if available, otherwise use defaults for standard theme
$is_custom_theme = ($current_theme !== 'default' && $theme_meta && isset($theme_meta['options']));

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
        if ($is_custom_theme) {
            $defaults = [];
            foreach ($theme_meta['options'] as $opt) {
                $defaults[$opt['name']] = $opt['default'] ?? '';
            }
            update_config(['theme_options' => $defaults]);
        } else {
            $defaults = [
                'primary_color' => '#007bff',
                'body_font' => 'Inter',
                'title_font' => 'Poppins',
                'body_font_size' => '16px',
                'title_font_size' => '32px',
                'widget_title_font_size' => '20px',
                'container_width' => '1100px',
                'sidebar_width' => '300px',
                'front_page_template' => 'default',
                'single_post_sidebar' => 'yes',
                'featured_image_position' => 'top',
                'custom_css' => $config['custom_css'] ?? ''
            ];
            update_config($defaults);
        }
        $success = "Options reset to defaults.";
    } else {
        if ($is_custom_theme) {
            $new_options = [];
            foreach ($theme_meta['options'] as $opt) {
                if ($opt['type'] === 'checkbox') {
                    $new_options[$opt['name']] = isset($_POST[$opt['name']]);
                } else {
                    $new_options[$opt['name']] = $_POST[$opt['name']] ?? $opt['default'];
                }
            }
            update_config(['theme_options' => $new_options]);
        } else {
            $new_config = [
                'body_font' => $_POST['body_font'] ?? 'Inter',
                'title_font' => $_POST['title_font'] ?? 'Poppins',
                'body_font_size' => $_POST['body_font_size'] ?? '16px',
                'title_font_size' => $_POST['title_font_size'] ?? '32px',
                'widget_title_font_size' => $_POST['widget_title_font_size'] ?? '20px',
                'primary_color' => $_POST['primary_color'] ?? '#007bff',
                'container_width' => $_POST['container_width'] ?? '1100px',
                'sidebar_width' => $_POST['sidebar_width'] ?? '300px',
                'front_page_template' => $_POST['front_page_template'] ?? 'default',
                'single_post_sidebar' => $_POST['single_post_sidebar'] ?? 'yes',
                'featured_image_position' => $_POST['featured_image_position'] ?? 'top',
                'custom_css' => $_POST['custom_css'] ?? '',
            ];
            update_config($new_config);
        }
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
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; flex-direction: column; min-height: 100vh; background: #f4f4f4; }
        .main-container { display: flex; flex: 1; }
        .main-content { flex: 1; padding: 2rem; overflow-y: auto; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], select, input[type="color"], textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="color"] { height: 50px; padding: 2px; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 1rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
    </style>
</head>
<body>
    <?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Theme Options (<?php echo htmlspecialchars($theme_meta['name'] ?? ucfirst($current_theme)); ?>)</h1>

        <?php if ($success): ?><div style="background: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 20px; border-radius: 4px;"><?php echo $success; ?></div><?php endif; ?>

        <div class="card">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <?php if ($is_custom_theme): ?>
                    <?php foreach ($theme_meta['options'] as $opt):
                        $val = $config['theme_options'][$opt['name']] ?? $opt['default'];
                    ?>
                        <div class="form-group">
                            <label><?php echo $opt['label']; ?></label>
                            <?php if ($opt['type'] === 'text'): ?>
                                <input type="text" name="<?php echo $opt['name']; ?>" value="<?php echo htmlspecialchars($val); ?>">
                            <?php elseif ($opt['type'] === 'color'): ?>
                                <input type="color" name="<?php echo $opt['name']; ?>" value="<?php echo htmlspecialchars($val); ?>">
                            <?php elseif ($opt['type'] === 'checkbox'): ?>
                                <input type="checkbox" name="<?php echo $opt['name']; ?>" <?php echo $val ? 'checked' : ''; ?>>
                            <?php elseif ($opt['type'] === 'select'): ?>
                                <select name="<?php echo $opt['name']; ?>">
                                    <?php foreach ($opt['options'] as $k => $v): ?>
                                        <option value="<?php echo $k; ?>" <?php echo $val == $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default Theme Options (Visible only for 'default' theme) -->
                    <h3>Layout Settings</h3>
                    <div class="form-group">
                        <label>Front Page Template</label>
                        <select name="front_page_template">
                            <option value="default" <?php echo ($config['front_page_template'] ?? '') === 'default' ? 'selected' : ''; ?>>Default</option>
                            <option value="grid" <?php echo ($config['front_page_template'] ?? '') === 'grid' ? 'selected' : ''; ?>>Grid</option>
                            <option value="single_column" <?php echo ($config['front_page_template'] ?? '') === 'single_column' ? 'selected' : ''; ?>>Single Column</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Accent Color</label>
                        <input type="color" name="primary_color" value="<?php echo $config['primary_color'] ?? '#007bff'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Additional CSS</label>
                        <textarea name="custom_css" style="height: 150px; font-family: monospace;"><?php echo htmlspecialchars($config['custom_css'] ?? ''); ?></textarea>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="submit" name="reset" class="btn btn-secondary" onclick="return confirm('Reset to theme defaults?')">Reset to Default</button>
            </form>
        </div>
    </div>
</body>
</html>
