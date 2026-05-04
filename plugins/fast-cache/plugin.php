<?php
/**
 * Plugin Name: Fast Cache
 * Description: Simple HTML caching for faster page loads.
 */

return [
    'name' => 'Fast Cache',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'hooks' => [
        'system_init' => function() {
            if (!empty($_POST) || !empty($_GET['s']) || isset($_GET['vote_slug'])) return;

            $cache_dir = __DIR__ . '/../../content/cache';
            if (!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);

            $cache_key = md5($_SERVER['REQUEST_URI']);
            $cache_file = $cache_dir . '/' . $cache_key . '.html';

            if (file_exists($cache_file) && (time() - file_mtime($cache_file) < 3600)) {
                echo file_get_contents($cache_file);
                echo "<!-- Cached: " . date('Y-m-d H:i:s', file_mtime($cache_file)) . " -->";
                exit;
            }

            ob_start(function($buffer) use ($cache_file) {
                if (!empty($buffer)) {
                    file_put_contents($cache_file, $buffer);
                }
                return $buffer;
            });
        },
        'post_saved_after' => function() {
            // Clear cache on update
            $cache_dir = __DIR__ . '/../../content/cache';
            if (is_dir($cache_dir)) {
                $files = glob($cache_dir . '/*.html');
                foreach ($files as $file) unlink($file);
            }
        }
    ]
];
