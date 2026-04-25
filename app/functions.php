<?php
/**
 * Core utility functions
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
