<?php
/**
 * Plugin Name: Header Master
 * Description: Advanced layout and design controls for site headers.
 */

return [
    'name' => 'Header Master',
    'version' => '1.2.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'settings.php?plugin=header-master',
    'hooks' => [
        'system_header' => function() {
            $config = load_config();
            $sticky = $config['header_sticky_global'] ?? false;
            $blur = $config['header_blur_global'] ?? false;
            $menu_align = $config['header_menu_align'] ?? 'right';

            ?>
            <style>
                <?php if ($sticky): ?>
                .site-header { position: sticky !important; top: 0; z-index: 1000; }
                <?php endif; ?>

                <?php if ($blur): ?>
                .site-header {
                    backdrop-filter: blur(10px) !important;
                    -webkit-backdrop-filter: blur(10px) !important;
                    background: rgba(51, 51, 51, 0.8) !important;
                }
                .dark .site-header { background: rgba(0, 0, 0, 0.8) !important; }
                <?php endif; ?>

                .main-nav ul { justify-content: <?php echo $menu_align === 'center' ? 'center' : ($menu_align === 'left' ? 'flex-start' : 'flex-end'); ?> !important; }
            </style>
            <?php
        }
    ]
];
