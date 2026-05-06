<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($post) ? htmlspecialchars($post['title']) . ' - ' : ''; echo htmlspecialchars($config['site_name']); ?></title>

    <?php
    $opts = $config['theme_options'] ?? [];
    $body_font = $opts['body_font'] ?? 'Inter';
    $title_font = $opts['title_font'] ?? 'Poppins';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($body_font); ?>:wght@400;700&family=<?php echo urlencode($title_font); ?>:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="themes/default/style.css">

    <style>
        :root {
            --accent-color: <?php echo $opts['primary_color'] ?? '#007bff'; ?>;
            --body-font: '<?php echo $body_font; ?>', sans-serif;
            --title-font: '<?php echo $title_font; ?>', sans-serif;
            --container-width: <?php echo ($opts['container_width'] ?? 1100) . 'px'; ?>;
            --sidebar-width: <?php echo ($opts['sidebar_width'] ?? 300) . 'px'; ?>;
            --site-title-size: <?php echo ($opts['site_title_font_size'] ?? 24) . 'px'; ?>;
            --body-font-size: <?php echo ($opts['body_font_size'] ?? 16) . 'px'; ?>;
            --title-font-size: <?php echo ($opts['title_font_size'] ?? 32) . 'px'; ?>;
        }

        .site-title a {
            font-size: var(--site-title-size);
            <?php if (!empty($opts['site_title_border'])): ?>
            border: 2px solid var(--header-text);
            padding: <?php echo ($opts['site_title_padding'] ?? 10) . 'px'; ?>;
            border-radius: <?php echo ($opts['site_title_border_radius'] ?? 8) . 'px'; ?>;
            display: inline-block;
            <?php endif; ?>
            <?php if (!empty($opts['site_title_underline'])): ?>
            position: relative; text-decoration: none;
            <?php endif; ?>
        }
        <?php if (!empty($opts['site_title_underline'])): ?>
        .site-title a::after {
            content: ''; position: absolute; left: 0; bottom: -5px; width: 70%; height: 3px; background: var(--accent-color);
        }
        <?php endif; ?>

        <?php echo $opts['custom_css'] ?? ''; ?>
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <?php
    foreach (get_enabled_plugins_data() as $p) {
        if (isset($p['hooks']['system_header'])) echo $p['hooks']['system_header']();
    }
    ?>
</head>
<?php
$is_front = !isset($post) && !isset($page);
$layout = $opts['front_page_layout'] ?? 'default';
$body_classes = [];

if ($is_front) {
    if ($layout === 'grid' || $layout === 'single_column') $body_classes[] = 'no-sidebar';
} else {
    if (($opts['single_post_sidebar'] ?? 'yes') === 'no') $body_classes[] = 'no-sidebar';
}
?>
<body class="<?php echo implode(' ', $body_classes); ?>">
    <header class="site-header">
        <div class="container header-inner">
            <div class="site-title">
                <a href="index.php"><?php echo htmlspecialchars($config['site_name']); ?></a>
            </div>
            <nav class="main-nav">
                <ul>
                    <?php
                    $menu = $config['menu'] ?? [['label' => 'Home', 'url' => 'index.php']];
                    foreach ($menu as $item): ?>
                        <li><a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['label'] ?? $item['title']); ?></a></li>
                    <?php endforeach; ?>
                    <li class="theme-toggle-li">
                        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
                            <span class="light-icon">☀️</span>
                            <span class="dark-icon">🌙</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="site-layout">
            <main class="site-main">
