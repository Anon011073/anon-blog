<?php $include_part('header'); ?>

<article class="page-full">
    <header class="page-header">
        <h1 class="page-title"><?php echo htmlspecialchars($page['title']); ?></h1>
    </header>

    <div class="page-content">
        <?php
        // Run content hooks for pages as well
        $content = $page['content'];
        $plugins = glob(__DIR__ . '/../../plugins/*/plugin.php');
        $enabled_plugins = $config['enabled_plugins'] ?? [];
        foreach ($plugins as $plugin) {
            $plugin_name = basename(dirname($plugin));
            if (!in_array($plugin_name, $enabled_plugins)) continue;
            $plugin_data = include $plugin;
            if (isset($plugin_data['hooks']['render_content'])) {
                $content = $plugin_data['hooks']['render_content']($content);
            }
        }
        echo $content;
        ?>
    </div>
</article>

<?php $include_part('footer'); ?>
