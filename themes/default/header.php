<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($post) ? htmlspecialchars($post['title']) . ' - ' : (isset($page) ? htmlspecialchars($page['title']) . ' - ' : ''); ?><?php echo htmlspecialchars($config['site_name']); ?></title>
    <link rel="stylesheet" href="themes/default/style.css">
    <?php
    $theme_assets = get_plugin_assets();
    foreach ($theme_assets['css'] as $css) echo '<link rel="stylesheet" href="'.$css.'">';
    ?>
    <style>
        :root {
            --accent-color: <?php echo $config['primary_color'] ?? '#007bff'; ?>;
            --font-family: <?php echo $config['theme_font'] ?? 'sans-serif'; ?>;
            --container-width: <?php echo $config['container_width'] ?? '1100px'; ?>;
            --sidebar-width: <?php echo $config['sidebar_width'] ?? '300px'; ?>;
        }
        body { font-family: var(--font-family); }
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
if (($config['front_page_template'] ?? 'default') !== 'default' && !isset($post) && !isset($page)) $body_classes[] = 'no-sidebar';
// For single post/page, we might also want no sidebar if configured, but request only mentioned front page templates
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
                        ?>
                            <li><a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['label']); ?></a></li>
                        <?php endforeach; ?>
                        <li>
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
