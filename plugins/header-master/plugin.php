<?php
/**
 * Plugin Name: Header Master
 * Description: Customize site header with glassmorphism and sticky options.
 */

return [
    'name' => 'Header Master',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'hooks' => [
        'system_header' => function() {
            $config = load_config();
            $sticky = $config['header_sticky'] ?? true;
            $blur = $config['header_blur'] ?? true;
            ?>
            <style>
                header {
                    <?php if ($sticky): ?>
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                    <?php endif; ?>

                    <?php if ($blur): ?>
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                    background: rgba(255, 255, 255, 0.8);
                    <?php endif; ?>
                }
                body.dark header {
                    background: rgba(30, 30, 30, 0.8);
                }
            </style>
            <?php
        }
    ]
];
