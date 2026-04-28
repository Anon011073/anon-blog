<?php
/**
 * Core utility functions
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ANONBLOG_VERSION', '1.0.0-beta');

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a slug from a string
 */
function generate_slug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

/**
 * CSRF Protection
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Redirect to a URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Load configuration
 */
function load_config() {
    $config_path = __DIR__ . '/../config/config.php';
    if (!file_exists($config_path)) {
        return [];
    }
    return include $config_path;
}

/**
 * Update configuration
 */
function update_config($new_config) {
    $config_path = __DIR__ . '/../config/config.php';
    $current_config = load_config();
    $merged_config = array_merge($current_config, $new_config);

    $content = "<?php\nreturn " . var_export($merged_config, true) . ";\n";
    return file_put_contents($config_path, $content);
}

/**
 * Format date
 */
function format_date($date_string) {
    return date('F j, Y', strtotime($date_string));
}

/**
 * Import Demo Content
 */
function import_demo_content() {
    $demo_dir = __DIR__ . '/demo';
    $posts_dir = __DIR__ . '/../content/posts';
    $pages_dir = __DIR__ . '/../content/pages';
    $uploads_dir = __DIR__ . '/../uploads';
    $config_file = __DIR__ . '/../config/config.php';

    // Copy Posts
    $demo_posts = glob("$demo_dir/posts/*.json");
    foreach ($demo_posts as $post) {
        copy($post, "$posts_dir/" . basename($post));
    }

    // Copy Pages
    $demo_pages = glob("$demo_dir/pages/*.json");
    foreach ($demo_pages as $page) {
        copy($page, "$pages_dir/" . basename($page));
    }

    // Copy Uploads
    $demo_uploads = glob("$demo_dir/uploads/*");
    foreach ($demo_uploads as $upload) {
        if (is_file($upload)) {
            copy($upload, "$uploads_dir/" . basename($upload));
        }
    }

    // Update Config (Merge demo settings but keep credentials)
    $current_config = load_config();
    $demo_config_json = file_get_contents("$demo_dir/config/demo_config.json");
    $demo_config = json_decode($demo_config_json, true);

    if ($demo_config) {
        // We want to keep admin_user and admin_pass
        $demo_config['admin_user'] = $current_config['admin_user'];
        $demo_config['admin_pass'] = $current_config['admin_pass'];
        update_config($demo_config);
    }

    return true;
}
