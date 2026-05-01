<?php
/**
 * XML Sitemap for AnonBlog
 */
return [
    'name' => 'AnonSitemap',
    'description' => 'Generates an XML sitemap for Google. Access your sitemap at index.php?sitemap.',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'hooks' => [
        'system_init' => function() {
            if (isset($_GET['sitemap'])) {
                header("Content-Type: application/xml; charset=utf-8");
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

                // Add Homepage
                $base = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                echo "<url><loc>$base/index.php</loc></url>";

                // Add Posts
                require_once __DIR__ . '/../../app/posts.php';
                foreach (get_posts() as $p) {
                    echo "<url><loc>$base/index.php?post=" . $p['slug'] . "</loc><lastmod>" . $p['date'] . "</lastmod></url>";
                }

                echo '</urlset>';
                die();
            }
        }
    ]
];
