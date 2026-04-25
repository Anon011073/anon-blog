<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/posts.php';
require_once __DIR__ . '/../app/functions.php';

require_login();

$slug = $_GET['slug'] ?? '';
$post = null;
$config = load_config();

if ($slug) {
    $post = get_post($slug);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $featured_image = $_POST['featured_image'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $new_slug = $_POST['slug'] ?? generate_slug($title);
    $comments_on = isset($_POST['comments_on']) ? true : false;

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $post_data = [
            'title' => $title,
            'slug' => $new_slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => $featured_image,
            'date' => $date,
            'comments_on' => $comments_on
        ];

        // If slug changed, delete old file
        if ($slug && $slug !== $new_slug) {
            delete_post($slug);
        }

        if (save_post($post_data)) {
            $success = "Post saved successfully.";
            $post = $post_data;
            $slug = $new_slug;
        } else {
            $error = "Failed to save post.";
        }
    }
}

// Get images for featured image selection
$images = glob(__DIR__ . '/../uploads/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? 'Edit Post' : 'Create Post'; ?> - Admin Panel</title>
    <!-- Jodit CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.2/jodit.min.css"/>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; min-height: 100vh; background: #f4f4f4; }
        .sidebar { width: 250px; background: #333; color: #fff; padding: 1rem; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 2rem; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1rem; }
        .sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 0.5rem; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #444; color: #fff; }
        .main-content { flex: 1; padding: 2rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], input[type="date"], textarea, select { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 1rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .error { color: #d9534f; background: #f2dede; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { color: #5cb85c; background: #dff0d8; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 10px; max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; }
        .image-item { cursor: pointer; border: 2px solid transparent; }
        .image-item img { width: 100%; height: auto; display: block; }
        .image-item.selected { border-color: #007bff; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><?php echo htmlspecialchars($config['site_name']); ?></h2>
        <ul>
            <li><a href="index.php" class="active">Posts</a></li>
            <li><a href="pages.php">Pages</a></li>
            <li><a href="media.php">Media</a></li>
            <li><a href="comments.php">Comments</a></li>
            <li><a href="settings.php">Settings</a></li><li><a href="theme_options.php">Theme Options</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="widgets.php">Widgets</a></li>
            <li><a href="plugins.php">Plugins</a></li>
            <li><a href="/" target="_blank">View Site</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1><?php echo $post ? 'Edit Post' : 'Create New Post'; ?></h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="slug">Slug (Leave empty to auto-generate)</label>
                    <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($post['date'] ?? date('Y-m-d')); ?>">
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content"><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="excerpt">Excerpt (Optional)</label>
                    <textarea id="excerpt" name="excerpt" style="height: 100px;"><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Featured Image</label>
                    <input type="hidden" id="featured_image" name="featured_image" value="<?php echo htmlspecialchars($post['featured_image'] ?? ''); ?>">
                    <div class="image-grid">
                        <div class="image-item <?php echo empty($post['featured_image']) ? 'selected' : ''; ?>" onclick="selectImage('')">
                            <div style="width: 100%; aspect-ratio: 1; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">No Image</div>
                        </div>
                        <?php foreach ($images as $img):
                            $img_name = basename($img);
                            $selected = ($post['featured_image'] ?? '') === $img_name ? 'selected' : '';
                        ?>
                            <div class="image-item <?php echo $selected; ?>" onclick="selectImage('<?php echo $img_name; ?>', this)">
                                <img src="../uploads/<?php echo $img_name; ?>" alt="">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="comments_on" <?php echo ($post['comments_on'] ?? true) ? 'checked' : ''; ?>> Enable comments for this post
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Save Post</button>
                <a href="index.php" class="btn">Cancel</a>
            </form>
        </div>
    </div>

    <!-- Jodit JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.2/jodit.min.js"></script>
    <script>
        const editor = new Jodit('#content', {
            height: 400
        });

        function selectImage(imgName, el) {
            document.getElementById('featured_image').value = imgName;
            document.querySelectorAll('.image-item').forEach(item => item.classList.remove('selected'));
            if (el) {
                el.classList.add('selected');
            } else {
                document.querySelector('.image-item').classList.add('selected');
            }
        }
    </script>
</body>
</html>
