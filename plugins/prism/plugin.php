<?php
/**
 * Plugin Name: Prism Syntax Highlighter
 * Description: High-performance syntax highlighting for 200+ languages.
 */

return [
    'name' => 'Prism Syntax Highlighter',
    'version' => '1.3.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'settings.php?plugin=prism',
    'hooks' => [
        'system_header' => function() {
            $config = load_config();
            $theme = $config['prism_theme'] ?? 'prism';
            $show_line_numbers = $config['prism_line_numbers'] ?? false;

            $theme_url = "https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/";
            if ($theme === 'prism') {
                $theme_url .= "prism.min.css";
            } else {
                $theme_url .= "prism-" . htmlspecialchars($theme) . ".min.css";
            }

            echo '<link rel="stylesheet" href="'.$theme_url.'">';
            if ($show_line_numbers) {
                echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css">';
            }
        },
        'system_footer' => function() {
            $config = load_config();
            $show_line_numbers = $config['prism_line_numbers'] ?? false;

            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>';
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>';
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>';
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>';

            if ($show_line_numbers) {
                echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>';
                echo '<script>document.addEventListener("DOMContentLoaded", function() { var pre = document.getElementsByTagName("pre"); for (var i=0; i<pre.length; i++) { pre[i].classList.add("line-numbers"); } });</script>';
            }
        }
    ]
];
