<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($post) ? htmlspecialchars($post['title']) . ' - ' : (isset($page) ? htmlspecialchars($page['title']) . ' - ' : ''); echo htmlspecialchars($config['site_name']); ?></title>

    <?php
    $options = $config['theme_options'] ?? [];
    $site_width = $options['site_width'] ?? 650;
    $body_font = $options['body_font'] ?? 'Inter';
    $title_font = $options['title_font'] ?? 'Playfair Display';
    $primary_color = $options['primary_color'] ?? '#000000';
    $header_blur = $options['header_blur'] ?? true;
    ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($body_font); ?>:wght@400;700&family=<?php echo urlencode($title_font); ?>:wght@700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="themes/popeye/style.css">

    <?php
    $assets = get_plugin_assets();
    foreach ($assets['css'] as $css) echo '<link rel="stylesheet" href="'.$css.'">';
    ?>

    <style>
        :root {
            --site-width: <?php echo $site_width; ?>px;
            --body-font: '<?php echo $body_font; ?>', sans-serif;
            --title-font: '<?php echo $title_font; ?>', serif;
            --primary-color: <?php echo $primary_color; ?>;
        }
        <?php if ($header_blur): ?>
        .site-header {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.8);
        }
        [data-theme="dark"] .site-header {
            background: rgba(18, 18, 18, 0.8);
        }
        <?php endif; ?>
        <?php echo $options['custom_css'] ?? ''; ?>
    </style>
    <?php
    foreach (get_enabled_plugins_data() as $p) {
        if (isset($p['hooks']['system_header'])) echo $p['hooks']['system_header']();
    }
    ?>
</head>
<body data-theme="<?php echo $config['dark_mode'] ?? false ? 'dark' : 'light'; ?>">
    <header class="site-header">
        <div class="container header-inner">
            <div class="site-branding">
                <a href="index.php" class="site-title"><?php echo htmlspecialchars($config['site_name']); ?></a>
            </div>
            <nav class="site-nav">
                <?php
                $menu = $config['menu'] ?? [['label' => 'Home', 'url' => 'index.php']];
                foreach ($menu as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['label'] ?? $item['title']); ?></a>
                <?php endforeach; ?>

                <?php if ($config['show_search_menu'] ?? false): ?>
                    <form action="index.php" method="GET" class="nav-search">
                        <input type="text" name="s" placeholder="Search..." style="background: rgba(128,128,128,0.1); border: none; padding: 5px 10px; border-radius: 4px; color: inherit; font-size: 0.9rem; width: 120px;">
                    </form>
                <?php endif; ?>

                <button id="theme-toggle" class="theme-toggle" title="Toggle Dark Mode">
                    <span class="sun-icon">☀️</span>
                    <span class="moon-icon">🌙</span>
                </button>
            </nav>
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            <?php
            $widget_pos = $options['widget_pos'] ?? 'bottom';
            if ($widget_pos === 'top' || $widget_pos === 'both') {
                $include_part('widgets-area');
            }
            ?>
