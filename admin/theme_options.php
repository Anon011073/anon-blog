<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login();

$config = load_config();
$error = '';
$success = '';

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
        $new_theme_config = $defaults;
    } else {
        $new_theme_config = [
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
    }

    if (update_config($new_theme_config)) {
        $success = "Theme options updated successfully.";
        $config = load_config();
    } else {
        $error = "Failed to update theme options.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Options - Admin Panel</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .sidebar { width: 250px; background: #333; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .main-content { flex: 1; padding: 2rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], select, input[type="color"] { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="color"] { width: 100%; height: 50px; cursor: pointer; border: 1px solid #ccc; padding: 2px; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 1rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .error { color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Theme Options</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

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
                    <small>Only applies to Default and Grid templates. "Single Column" template never shows sidebar.</small>
                </div>

                <div class="form-group">
                    <label for="featured_image_position">Featured Image Position (List & Single Post)</label>
                    <select id="featured_image_position" name="featured_image_position">
                        <option value="top" <?php echo ($config['featured_image_position'] ?? '') === 'top' ? 'selected' : ''; ?>>Above Title (Full Width)</option>
                        <option value="left" <?php echo ($config['featured_image_position'] ?? '') === 'left' ? 'selected' : ''; ?>>Left of Content (Thumbnail)</option>
                    </select>
                    <small>Note: Grid template always uses "Above Title".</small>
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
                    <textarea id="custom_css" name="custom_css" style="width: 100%; height: 200px; font-family: monospace; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"><?php echo htmlspecialchars($config['custom_css'] ?? ''); ?></textarea>
                    <p style="font-size: 0.85rem; color: #666; margin-top: 10px;">
                        <strong>Commonly used classes:</strong><br>
                        <code>.post-title</code>, <code>.post-content</code>, <code>.site-header</code>, <code>.site-sidebar</code>, <code>.widget</code>, <code>.btn-primary</code>, <code>.site-footer</code>
                    </p>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="submit" name="reset" class="btn btn-secondary" onclick="return confirm('Reset theme settings to default?')">Reset to Default</button>
            </form>
        </div>
    </div>
</body>
</html>
