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

$defaults = [
    'body_font' => 'Inter',
    'title_font' => 'Poppins',
    'body_font_size' => '16px',
    'title_font_size' => '32px',
    'widget_title_font_size' => '20px',
    'primary_color' => '#007bff',
    'container_width' => '1100px',
    'sidebar_width' => '300px',
    'front_page_template' => 'default',
    'single_post_sidebar' => 'yes',
    'featured_image_position' => 'top',
    'custom_css' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    if (isset($_POST['reset'])) {
        if ($is_custom_theme) {
            $reset_options = [];
            foreach ($theme_meta['options'] as $opt) {
                $reset_options[$opt['name']] = $opt['default'] ?? '';
            }
            update_config(['theme_options' => $reset_options]);
        } else {
            $reset_config = $defaults;
            // Preserve custom CSS on reset
            $reset_config['custom_css'] = $config['custom_css'] ?? '';
            update_config($reset_config);
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
                'body_font' => $_POST['body_font'] ?? $defaults['body_font'],
                'title_font' => $_POST['title_font'] ?? $defaults['title_font'],
                'body_font_size' => $_POST['body_font_size'] ?? $defaults['body_font_size'],
                'title_font_size' => $_POST['title_font_size'] ?? $defaults['title_font_size'],
                'widget_title_font_size' => $_POST['widget_title_font_size'] ?? $defaults['widget_title_font_size'],
                'primary_color' => $_POST['primary_color'] ?? $defaults['primary_color'],
                'container_width' => $_POST['container_width'] ?? $defaults['container_width'],
                'sidebar_width' => $_POST['sidebar_width'] ?? $defaults['sidebar_width'],
                'front_page_template' => $_POST['front_page_template'] ?? $defaults['front_page_template'],
                'single_post_sidebar' => $_POST['single_post_sidebar'] ?? $defaults['single_post_sidebar'],
                'featured_image_position' => $_POST['featured_image_position'] ?? $defaults['featured_image_position'],
                'custom_css' => $_POST['custom_css'] ?? $defaults['custom_css'],
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
        .main-content { flex: 1; padding: 2rem; margin-left: 310px; margin-top: 50px; overflow-y: auto; }
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
                            <?php elseif ($opt['type'] === 'font'): ?>
                                <select name="<?php echo $opt['name']; ?>">
                                    <?php foreach ($google_fonts as $font): ?>
                                        <option value="<?php echo $font; ?>" <?php echo $val === $font ? 'selected' : ''; ?>><?php echo $font; ?></option>
                                    <?php endforeach; ?>
                                </select>
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
                    <!-- Default Theme Options -->
                    <h3>Layout Settings</h3>
                    <div class="form-group">
                        <label for="front_page_template">Front Page Template</label>
                        <select id="front_page_template" name="front_page_template">
                            <option value="default" <?php echo ($config['front_page_template'] ?? '') === 'default' ? 'selected' : ''; ?>>Default (List + Sidebar)</option>
                            <option value="grid" <?php echo ($config['front_page_template'] ?? '') === 'grid' ? 'selected' : ''; ?>>Grid (2-3 Columns, No Sidebar)</option>
                            <option value="single_column" <?php echo ($config['front_page_template'] ?? '') === 'single_column' ? 'selected' : ''; ?>>Single Column (No Sidebar)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="single_post_sidebar">Show Sidebar on Single Post Page</label>
                        <select id="single_post_sidebar" name="single_post_sidebar">
                            <option value="yes" <?php echo ($config['single_post_sidebar'] ?? 'yes') === 'yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="no" <?php echo ($config['single_post_sidebar'] ?? 'yes') === 'no' ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="featured_image_position">Featured Image Position (List & Single Post)</label>
                        <select id="featured_image_position" name="featured_image_position">
                            <option value="top" <?php echo ($config['featured_image_position'] ?? '') === 'top' ? 'selected' : ''; ?>>Above Title (Full Width)</option>
                            <option value="left" <?php echo ($config['featured_image_position'] ?? '') === 'left' ? 'selected' : ''; ?>>Left of Content (Thumbnail)</option>
                        </select>
                    </div>

                    <hr>
                    <h3>Typography Settings</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="title_font">Title Font</label>
                            <select id="title_font" name="title_font">
                                <?php foreach ($google_fonts as $font): ?>
                                    <option value="<?php echo $font; ?>" <?php echo ($config['title_font'] ?? $defaults['title_font']) === $font ? 'selected' : ''; ?>><?php echo $font; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="body_font">Body Font</label>
                            <select id="body_font" name="body_font">
                                <?php foreach ($google_fonts as $font): ?>
                                    <option value="<?php echo $font; ?>" <?php echo ($config['body_font'] ?? $defaults['body_font']) === $font ? 'selected' : ''; ?>><?php echo $font; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="title_font_size">Title Font Size</label>
                            <input type="text" id="title_font_size" name="title_font_size" value="<?php echo htmlspecialchars($config['title_font_size'] ?? $defaults['title_font_size']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="body_font_size">Body Font Size</label>
                            <input type="text" id="body_font_size" name="body_font_size" value="<?php echo htmlspecialchars($config['body_font_size'] ?? $defaults['body_font_size']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="widget_title_font_size">Widget Title Size</label>
                            <input type="text" id="widget_title_font_size" name="widget_title_font_size" value="<?php echo htmlspecialchars($config['widget_title_font_size'] ?? $defaults['widget_title_font_size']); ?>">
                        </div>
                    </div>

                    <hr>
                    <h3>Style Settings</h3>
                    <div class="form-group">
                        <label for="primary_color">Primary Accent Color</label>
                        <input type="color" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars($config['primary_color'] ?? '#007bff'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="container_width">Container Max-Width</label>
                        <input type="text" id="container_width" name="container_width" value="<?php echo htmlspecialchars($config['container_width'] ?? '1100px'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="sidebar_width">Sidebar Width</label>
                        <input type="text" id="sidebar_width" name="sidebar_width" value="<?php echo htmlspecialchars($config['sidebar_width'] ?? '300px'); ?>">
                    </div>

                    <hr>
                    <h3>Custom CSS</h3>
                    <div class="form-group">
                        <label for="custom_css">Additional CSS</label>
                        <textarea id="custom_css" name="custom_css" style="height: 150px; font-family: monospace;"><?php echo htmlspecialchars($config['custom_css'] ?? ''); ?></textarea>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="submit" name="reset" class="btn btn-secondary" onclick="return confirm('Reset to theme defaults?')">Reset to Default</button>
            </form>
        </div>
    </div>
</body>
</html>
