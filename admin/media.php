<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login('media');

$config = load_config();
$uploads_dir = __DIR__ . '/../uploads/';
$error = '';
$success = '';

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $files = $_FILES['files'];
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $uploaded_count = 0;
    $errors = [];

    // Max 5 files
    $count = min(count($files['name']), 5);

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        $check = getimagesize($files['tmp_name'][$i]);

        if ($check === false) {
            $errors[] = "{$files['name'][$i]} is not an image.";
        } elseif (!in_array($ext, $allowed_exts)) {
            $errors[] = "{$files['name'][$i]} has an invalid extension.";
        } elseif ($files['size'][$i] > 5000000) {
            $errors[] = "{$files['name'][$i]} is too large (max 5MB).";
        } else {
            $filename = basename($files['name'][$i]);
            $target_file = $uploads_dir . $filename;

            $j = 1;
            while (file_exists($target_file)) {
                $parts = pathinfo($filename);
                $new_filename = $parts['filename'] . '_' . $j . '.' . $parts['extension'];
                $target_file = $uploads_dir . $new_filename;
                $j++;
            }

            if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                $uploaded_count++;
            } else {
                $errors[] = "Failed to upload {$files['name'][$i]}.";
            }
        }
    }

    if ($uploaded_count > 0) $success = "$uploaded_count images uploaded successfully.";
    if (!empty($errors)) $error = implode('<br>', $errors);
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verify_csrf_token($_GET['token'])) {
        $file_to_delete = $uploads_dir . basename($_GET['delete']);
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
            $success = "File deleted.";
        }
    }
}

$images = glob($uploads_dir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
// Sort images by date (newest first)
usort($images, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .main-content { flex: 1; padding: 2rem; margin-left: 310px; margin-top: 50px; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-danger { background: #d9534f; color: #fff; }
        .error { color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; margin-top: 2rem; }
        .media-item { background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; position: relative; transition: 0.2s; }
        .media-item:hover { border-color: #007bff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .media-item img { width: 100%; aspect-ratio: 1; object-fit: cover; display: block; }
        .media-info { padding: 0.5rem; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; }
        .media-info span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100px; }
        .selected-badge { position: absolute; top: 10px; right: 10px; background: #007bff; color: #fff; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid #fff; visibility: hidden; }
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Media Library</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <label for="file" style="display: block; font-weight: bold; margin-bottom: 10px;">Upload Images (Max 5 at once):</label>
                <div style="display: flex; gap: 10px;">
                    <input type="file" id="file" name="files[]" multiple required accept="image/*" style="flex: 1; border: 1px dashed #ccc; padding: 10px; border-radius: 4px;">
                    <button type="submit" class="btn btn-primary">Upload All</button>
                </div>
            </form>
        </div>

        <div class="media-grid">
            <?php foreach ($images as $img):
                $img_name = basename($img);
            ?>
                <div class="media-item" data-filename="<?php echo htmlspecialchars($img_name); ?>">
                    <div class="selected-badge">✓</div>
                    <img src="../uploads/<?php echo $img_name; ?>" alt="">
                    <div class="media-info">
                        <span title="<?php echo htmlspecialchars($img_name); ?>"><?php echo htmlspecialchars($img_name); ?></span>
                        <div style="display: flex; gap: 4px;">
                            <a href="media_crop.php?img=<?php echo urlencode($img_name); ?>" class="btn-primary btn" style="padding: 2px 5px; font-size: 0.7rem;" title="Crop">✂️</a>
                            <a href="media.php?delete=<?php echo urlencode($img_name); ?>&token=<?php echo get_csrf_token(); ?>" class="btn-danger btn" style="padding: 2px 5px; font-size: 0.7rem;" onclick="return confirm('Delete this image?')">X</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        $plugins = get_enabled_plugins_data();
        if (isset($plugins['gallery'])): ?>
        <div class="card" id="gallery-helper" style="margin-top: 40px; border-top: 5px solid #007bff;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">🖼️ Gallery Shortcode Helper</h3>
                <span id="selected-count" style="background: #007bff; color: #fff; padding: 2px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">0 selected</span>
            </div>
            <p style="font-size: 0.9rem; margin-bottom: 15px;">Click images above to select them. Then copy this shortcode into your post content.</p>
            <div id="selected-list" style="margin-bottom: 10px; font-size: 0.85rem; color: #666; max-height: 60px; overflow-y: auto; background: #f9f9f9; padding: 10px; border-radius: 4px; border: 1px solid #eee;">No images selected.</div>

            <div style="display: flex; gap: 10px; align-items: center; background: #333; padding: 10px; border-radius: 4px;">
                <code id="generated-shortcode" style="flex: 1; color: #fff; font-size: 0.95rem; border: none; background: transparent;">[gallery images=""]</code>
                <button class="btn btn-primary" onclick="copyShortcode()" style="background: #28a745;">Copy</button>
                <button class="btn" onclick="clearSelection()" style="background: #666; color: #fff;">Clear</button>
            </div>
        </div>

        <script>
            let selectedImages = [];
            document.querySelectorAll('.media-item').forEach(item => {
                item.style.cursor = 'pointer';
                item.addEventListener('click', function(e) {
                    // Don't trigger if clicking buttons
                    if (e.target.tagName === 'A' || e.target.parentElement.tagName === 'A' || e.target.tagName === 'BUTTON') return;

                    const filename = this.getAttribute('data-filename');
                    const index = selectedImages.indexOf(filename);
                    const badge = this.querySelector('.selected-badge');

                    if (index > -1) {
                        selectedImages.splice(index, 1);
                        this.style.outline = 'none';
                        badge.style.visibility = 'hidden';
                    } else {
                        selectedImages.push(filename);
                        this.style.outline = '3px solid #007bff';
                        badge.style.visibility = 'visible';
                    }

                    updateHelper();
                });
            });

            function updateHelper() {
                const list = document.getElementById('selected-list');
                const code = document.getElementById('generated-shortcode');
                const countBadge = document.getElementById('selected-count');

                if (selectedImages.length === 0) {
                    list.innerText = 'No images selected.';
                    code.innerText = '[gallery images=""]';
                    countBadge.innerText = '0 selected';
                } else {
                    list.innerText = selectedImages.join(', ');
                    code.innerText = '[gallery images="' + selectedImages.join(', ') + '"]';
                    countBadge.innerText = selectedImages.length + ' selected';
                }
            }

            function copyShortcode() {
                const text = document.getElementById('generated-shortcode').innerText;
                const tempInput = document.createElement("input");
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                alert('Gallery shortcode copied to clipboard!');
            }

            function clearSelection() {
                selectedImages = [];
                document.querySelectorAll('.media-item').forEach(item => {
                    item.style.outline = 'none';
                    item.querySelector('.selected-badge').style.visibility = 'hidden';
                });
                updateHelper();
            }
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
