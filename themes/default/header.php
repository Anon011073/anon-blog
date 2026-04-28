<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($post) ? htmlspecialchars($post['title']) . ' - ' : (isset($page) ? htmlspecialchars($page['title']) . ' - ' : ''); ?><?php echo htmlspecialchars($config['site_name']); ?></title>

    <?php
    $body_font = $config['body_font'] ?? 'Inter';
    $title_font = $config['title_font'] ?? 'Poppins';
    $fonts_to_load = array_unique([$body_font, $title_font]);
    $google_fonts_url = "https://fonts.googleapis.com/css2?family=" . implode('&family=', array_map(function($f) { return str_replace(' ', '+', $f) . ':wght@400;700'; }, $fonts_to_load)) . "&display=swap";
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo $google_fonts_url; ?>" rel="stylesheet">

    <link rel="stylesheet" href="themes/default/style.css">
    <?php
    $theme_assets = get_plugin_assets();
    foreach ($theme_assets['css'] as $css) echo '<link rel="stylesheet" href="'.$css.'">';
    ?>
    <style>
        :root {
            --accent-color: <?php echo $config['primary_color'] ?? '#007bff'; ?>;
            --body-font: '<?php echo $body_font; ?>', sans-serif;
            --title-font: '<?php echo $title_font; ?>', sans-serif;
            --body-font-size: <?php echo $config['body_font_size'] ?? '16px'; ?>;
            --title-font-size: <?php echo $config['title_font_size'] ?? '32px'; ?>;
            --widget-title-font-size: <?php echo $config['widget_title_font_size'] ?? '20px'; ?>;
            --container-width: <?php echo $config['container_width'] ?? '1100px'; ?>;
            --sidebar-width: <?php echo $config['sidebar_width'] ?? '300px'; ?>;
        }
        <?php echo $config['custom_css'] ?? ''; ?>
        .container { max-width: var(--container-width); }
        .site-sidebar { width: var(--sidebar-width); }
        .btn, .btn-primary, .read-more, .widget h3 { --accent-color: <?php echo $config['primary_color'] ?? '#007bff'; ?>; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<?php
$body_classes = [];
if (($config['sidebar_position'] ?? 'right') === 'left') $body_classes[] = 'sidebar-left';

$is_front_page = !isset($post) && !isset($page);
$template = $config['front_page_template'] ?? 'default';

if ($is_front_page) {
    if ($template !== 'default') {
        $body_classes[] = 'no-sidebar';
    }
} else {
    // Single post/page
    if ($template === 'single_column') {
        $body_classes[] = 'no-sidebar';
    } elseif (($config['single_post_sidebar'] ?? 'yes') === 'no') {
        $body_classes[] = 'no-sidebar';
    }
}
?>
<body class="<?php echo implode(' ', $body_classes); ?>">
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <div class="site-title">
                    <a href="index.php"><?php echo htmlspecialchars($config['site_name']); ?></a>
                </div>
                <nav class="main-nav">
                    <ul>
                        <?php
                        $menu = $config['menu'] ?? [['label' => 'Home', 'url' => 'index.php']];
                        foreach ($menu as $item):
                            $label = $item['label'] ?? $item['title'] ?? 'Link';
                        ?>
                            <li><a href="<?php echo htmlspecialchars($item['url'] ?? '#'); ?>"><?php echo htmlspecialchars($label); ?></a></li>
                        <?php endforeach; ?>
                        <?php if ($config['show_search_menu'] ?? false): ?>
                        <li class="menu-search">
                            <form action="index.php" method="GET">
                                <input type="text" name="s" placeholder="Search..." required>
                                <button type="submit" aria-label="Search">🔍</button>
                            </form>
                        </li>
                        <?php endif; ?>
                        <li class="theme-toggle-li">
                            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
                                <span class="light-icon">☀️</span>
                                <span class="dark-icon">🌙</span>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="site-layout">
            <main class="site-main">
