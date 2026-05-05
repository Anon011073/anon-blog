<?php
/**
 * Plugin Name: Prism Syntax Highlighter
 * Description: Lightweight syntax highlighting for code blocks.
 */

return [
    'name' => 'Prism Syntax Highlighter',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'settings.php?plugin=prism',
    'hooks' => [
        'system_header' => function() {
            $config = load_config();
            $prism_theme = $config['prism_theme'] ?? 'prism';
            echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/'.htmlspecialchars($prism_theme).'.min.css">';
        },
        'system_footer' => function() {
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>';
        }
    ]
];
