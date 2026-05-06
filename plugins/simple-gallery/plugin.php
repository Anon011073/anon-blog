<?php
/**
 * Plugin Name: Simple Gallery
 * Description: Shortcode [gallery ids="..."] for image grids.
 */

return [
    'name' => 'Simple Gallery',
    'version' => '1.1.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'settings.php?plugin=simple-gallery',
    'hooks' => [
        'render_content' => function($content) {
            if (preg_match('/\[gallery ids="([^"]+)"\]/', $content, $matches)) {
                $ids = explode(',', $matches[1]);
                $config = load_config();
                $columns = $config['gallery_columns'] ?? 3;
                $gap = $config['gallery_gap'] ?? '10px';

                $html = '<div class="gallery-grid" style="display:grid; grid-template-columns: repeat('.(int)$columns.', 1fr); gap:'.$gap.'; margin: 20px 0;">';
                foreach ($ids as $id) {
                    $id = trim($id);
                    if (empty($id)) continue;
                    $html .= '<div class="gallery-item"><img src="uploads/'.htmlspecialchars($id).'" style="width:100%; height:200px; object-fit:cover; border-radius:4px;"></div>';
                }
                $html .= '</div>';
                $content = str_replace($matches[0], $html, $content);
            }
            return $content;
        }
    ]
];
