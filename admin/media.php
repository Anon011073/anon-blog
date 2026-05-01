<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

require_login('media');

$config = load_config();
$uploads_dir = __DIR__ . '/../uploads/';
$error = '';
$success = '';

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $file = $_FILES['file'];
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    $check = getimagesize($file['tmp_name']);

    if ($check === false) {
        $error = "File is not an image.";
    } elseif (!in_array($ext, $allowed_exts)) {
        $error = "Invalid file extension. Only JPG, PNG, GIF, and WEBP are allowed.";
    } elseif ($file['size'] > 5000000) { // 5MB limit
        $error = "File is too large. Max 5MB.";
    } else {
        $filename = basename($file['name']);
        $target_file = $uploads_dir . $filename;

        // Ensure unique filename
        $i = 1;
        while (file_exists($target_file)) {
            $parts = pathinfo($filename);
            $new_filename = $parts['filename'] . '_' . $i . '.' . $parts['extension'];
            $target_file = $uploads_dir . $new_filename;
            $i++;
        }

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $success = "File uploaded successfully.";
        } else {
            $error = "Failed to move uploaded file.";
        }
    }
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media - Admin Panel</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .sidebar { width: 250px; background: #333; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .main-content { flex: 1; padding: 2rem; margin-left: 310px; margin-top: 50px; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-danger { background: #d9534f; color: #fff; }
        .error { color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 2rem; }
        .media-item { background: #eee; border-radius: 4px; overflow: hidden; position: relative; }
        .media-item img { width: 100%; aspect-ratio: 1; object-fit: cover; display: block; }
        .media-info { padding: 0.5rem; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center; }
        .media-info span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100px; }
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>
    <div class="main-content">
        <h1>Media Library</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <label for="file">Upload Image:</label>
                <input type="file" id="file" name="file" required>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>

        <div class="media-grid">
            <?php foreach ($images as $img):
                $img_name = basename($img);
            ?>
                <div class="media-item" data-filename="<?php echo htmlspecialchars($img_name); ?>">
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
        <div class="card" style="margin-top: 40px; border-top: 4px solid #007bff;">
            <h3>🖼️ Gallery Shortcode Helper</h3>
            <p>Select images by clicking on them to generate a gallery shortcode.</p>
            <div id="selected-list" style="margin-bottom: 15px; font-style: italic; color: #666;">No images selected.</div>
            <code id="generated-shortcode" style="display: block; background: #f0f0f0; padding: 10px; margin-bottom: 15px;">[gallery images=""]</code>
            <button class="btn btn-primary" onclick="copyShortcode()">Copy Shortcode</button>
            <button class="btn" onclick="clearSelection()">Clear Selection</button>
        </div>

        <script>
            let selectedImages = [];
            document.querySelectorAll('.media-item').forEach(item => {
                item.style.cursor = 'pointer';
                item.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' || e.target.parentElement.tagName === 'A') return;

                    const filename = this.getAttribute('data-filename');
                    const index = selectedImages.indexOf(filename);

                    if (index > -1) {
                        selectedImages.splice(index, 1);
                        this.style.outline = 'none';
                        this.style.opacity = '1';
                    } else {
                        selectedImages.push(filename);
                        this.style.outline = '4px solid #007bff';
                        this.style.opacity = '0.8';
                    }

                    updateHelper();
                });
            });

            function updateHelper() {
                const list = document.getElementById('selected-list');
                const code = document.getElementById('generated-shortcode');

                if (selectedImages.length === 0) {
                    list.innerText = 'No images selected.';
                    code.innerText = '[gallery images=""]';
                } else {
                    list.innerText = 'Selected: ' + selectedImages.join(', ');
                    code.innerText = '[gallery images="' + selectedImages.join(', ') + '"]';
                }
            }

            function copyShortcode() {
                const code = document.getElementById('generated-shortcode').innerText;
                navigator.clipboard.writeText(code).then(() => {
                    alert('Shortcode copied to clipboard!');
                });
            }

            function clearSelection() {
                selectedImages = [];
                document.querySelectorAll('.media-item').forEach(item => {
                    item.style.outline = 'none';
                    item.style.opacity = '1';
                });
                updateHelper();
            }
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
