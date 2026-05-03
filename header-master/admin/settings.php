<?php
require_once __DIR__ . '/../../../app/auth.php';
require_once __DIR__ . '/../../../app/functions.php';

require_login('plugins');

$config = load_config();
$header_options = $config['header_master'] ?? [
    'sticky' => false,
    'blur' => false,
    'layout' => 'default', // default, centered, stacked
    'header_img' => '',
    'header_height' => '200px'
];

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) die('CSRF Failed');

    $new_options = [
        'sticky' => isset($_POST['sticky']),
        'blur' => isset($_POST['blur']),
        'layout' => $_POST['layout'],
        'header_height' => $_POST['header_height'],
        'header_img' => $header_options['header_img'] // Keep existing
    ];

    // Handle Image Upload
    if (!empty($_FILES['header_img']['name'])) {
        $file = $_FILES['header_img'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $filename = 'header_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], __DIR__ . '/../../../uploads/' . $filename)) {
                $new_options['header_img'] = $filename;
            }
        }
    }

    if (isset($_POST['remove_img'])) {
        $new_options['header_img'] = '';
    }

    update_config(['header_master' => $new_options]);
    header("Location: settings.php?success=1");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Header Master Settings</title>
    <link rel="stylesheet" href="../../../admin/style.css">
    <style>.main-content { margin-left: 310px; margin-top: 60px; padding: 20px; }</style>
</head>
<body>
    <?php include "../../../admin/sidebar.php"; ?>
    <div class="main-content">
        <h1>Header Master Plugin</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Settings saved!</div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <div class="form-group">
                    <label><input type="checkbox" name="sticky" <?php echo ($header_options['sticky'] ?? false) ? 'checked' : ''; ?>> Sticky Header (Fixed at top)</label>
                </div>

                <div class="form-group">
                    <label><input type="checkbox" name="blur" <?php echo ($header_options['blur'] ?? false) ? 'checked' : ''; ?>> Enable Glassmorphism Blur (Apply to sticky header)</label>
                </div>

                <div class="form-group">
                    <label>Header Layout</label>
                    <select name="layout">
                        <option value="default" <?php echo ($header_options['layout'] == 'default') ? 'selected' : ''; ?>>Default (Title Left, Menu Right)</option>
                        <option value="centered" <?php echo ($header_options['layout'] == 'centered') ? 'selected' : ''; ?>>Centered Title (Menu in Nav)</option>
                        <option value="stacked" <?php echo ($header_options['layout'] == 'stacked') ? 'selected' : ''; ?>>Stacked (Title Top, Menu Below)</option>
                    </select>
                </div>

                <hr>

                <div class="form-group">
                    <label>Header Background Image</label>
                    <?php if (!empty($header_options['header_img'])): ?>
                        <img src="../../../uploads/<?php echo $header_options['header_img']; ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
                        <button type="submit" name="remove_img" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;">Remove Image</button>
                    <?php endif; ?>
                    <input type="file" name="header_img" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Header Height (px)</label>
                    <input type="text" name="header_height" value="<?php echo $header_options['header_height'] ?? '200px'; ?>">
                    <p class="help">Used if a background image is set.</p>
                </div>

                <button type="submit" class="btn btn-primary">Save Header Settings</button>
            </form>
        </div>
    </div>
</body>
</html>
