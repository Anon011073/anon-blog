<?php
/**
 * Header Master Plugin for AnonBlog
 */

$config = load_config();
$header_options = $config['header_master'] ?? [
    'sticky' => false,
    'blur' => false,
    'layout' => 'default',
    'header_img' => '',
    'header_height' => '200px'
];

return [
    'name' => 'Header Master',
    'description' => 'Advanced header controls: Sticky, Blur, Layouts, and Header Image.',
    'author' => 'AnonBlog Team',
    'version' => '1.0.0',
    'settings_url' => '../plugins/header-master/admin/settings.php',
    'hooks' => [
        'render_content' => function($content) {
            // We mainly use system_header for this plugin
            return $content;
        },
        'system_header' => function() use ($header_options) {
            $css = '<style>';

            if ($header_options['sticky']) {
                $css .= '
                .site-header {
                    position: fixed !important;
                    top: 0;
                    left: 0;
                    right: 0;
                    z-index: 9999;
                    width: 100%;
                }
                body { padding-top: 80px; }
                ';
            }

            if ($header_options['blur'] && $header_options['sticky']) {
                $css .= '
                .site-header {
                    backdrop-filter: blur(12px) !important;
                    -webkit-backdrop-filter: blur(12px) !important;
                    background: rgba(255,255,255,0.7) !important;
                }
                [data-theme="dark"] .site-header {
                    background: rgba(0,0,0,0.7) !important;
                }
                ';
            }

            if (!empty($header_options['header_img'])) {
                $css .= '
                .site-header-image {
                    width: 100%;
                    height: '.$header_options['header_height'].';
                    background-image: url("uploads/'.$header_options['header_img'].'");
                    background-size: cover;
                    background-position: center;
                    margin-bottom: 30px;
                }
                ';
            }

            if ($header_options['layout'] === 'centered') {
                $css .= '
                .header-inner { flex-direction: column !important; text-align: center !important; gap: 15px; }
                ';
            } elseif ($header_options['layout'] === 'stacked') {
                $css .= '
                .header-inner { flex-direction: column !important; }
                .site-nav { width: 100%; justify-content: center; border-top: 1px solid rgba(128,128,128,0.1); padding-top: 15px; margin-top: 10px; }
                ';
            }

            $css .= '</style>';

            $html = '';
            if (!empty($header_options['header_img'])) {
                $html = '<div class="site-header-image"></div>';
            }

            return $css . $html;
        }
    ]
];
