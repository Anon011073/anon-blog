<?php
/**
 * Plugin Name: Simple Gallery
 * Description: Shortcode [gallery ids="..."] for image grids.
 */

return [
    'name' => 'Simple Gallery',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'settings.php?plugin=simple-gallery',
    'hooks' => [
        'render_content' => function($content) {
            if (preg_match('/\[gallery ids="([^"]+)"\]/', $content, $matches)) {
                $ids = explode(',', $matches[1]);
                $html = '<div class="gallery-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap:10px;">';
                foreach ($ids as $id) {
                    $id = trim($id);
                    $html .= '<div class="gallery-item"><img src="uploads/'.htmlspecialchars($id).'" style="width:100%; border-radius:4px;"></div>';
                }
                $html .= '</div>';
                $content = str_replace($matches[0], $html, $content);
            }
            return $content;
        }
    ]
];
