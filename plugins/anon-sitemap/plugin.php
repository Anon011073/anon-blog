<?php
/**
 * XML Sitemap Generator for AnonBlog
 */

return [
    'name' => 'AnonSitemap',
    'description' => 'Generates a dynamic XML sitemap at /sitemap.xml.',
    'author' => 'AnonBlog Team',
    'version' => '1.0.0',
    'hooks' => [
        'system_init' => function() {
            if (isset($_GET['sitemap'])) {
                header('Content-Type: application/xml');
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

                $root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $base = str_replace(['index.php', 'admin/'], '', $_SERVER['SCRIPT_NAME']);

                echo '<url><loc>'.$root.$base.'</loc><priority>1.0</priority></url>';

                require_once __DIR__ . '/../../app/posts.php';
                foreach (get_posts() as $p) {
                    echo '<url><loc>'.$root.$base.'?post='.$p['slug'].'</loc><priority>0.8</priority></url>';
                }

                echo '</urlset>';
                exit;
            }
        }
    ]
];
