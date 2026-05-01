<?php
/**
 * Simple HTML Caching Plugin for AnonBlog
 */

$config = load_config();
$cache_options = $config['cache_options'] ?? ['enabled' => true, 'ttl' => 3600];

return [
    'name' => 'Fast Cache',
    'description' => 'Speed up your site by caching rendered HTML pages.',
    'author' => 'AnonBlog Team',
    'version' => '1.2.0',
    'settings_url' => '../plugins/cache/admin/settings.php',
    'hooks' => [
        'system_init' => function() use ($cache_options) {
            if (!$cache_options['enabled']) return;

            // Do not cache for admin or POST requests
            if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || $_SERVER['REQUEST_METHOD'] === 'POST') {
                return;
            }

            $cache_dir = __DIR__ . '/../../config/cache/';
            if (!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);

            $cache_key = md5($_SERVER['REQUEST_URI'] . ($_SERVER['QUERY_STRING'] ?? ''));
            $cache_file = $cache_dir . $cache_key . '.html';

            // Cache TTL check
            if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_options['ttl'])) {
                echo file_get_contents($cache_file);
                echo "\n<!-- Cached version " . date('Y-m-d H:i:s', filemtime($cache_file)) . " -->";
                exit;
            }

            // Start output buffering to capture the rendered page
            ob_start(function($buffer) use ($cache_file) {
                if (strlen($buffer) > 100 && http_response_code() === 200) {
                    file_put_contents($cache_file, $buffer);
                }
                return $buffer;
            });
        },
        'post_saved' => function($slug) {
            clear_anon_cache();
        },
        'post_deleted' => function($slug) {
            clear_anon_cache();
        },
        'config_updated' => function() {
            clear_anon_cache();
        }
    ]
];

function clear_anon_cache() {
    $cache_dir = __DIR__ . '/../../config/cache/';
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '*.html');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
    }
}
