<?php
$widget_pos = ($config['theme_options'] ?? [])['widget_pos'] ?? 'bottom';
if ($widget_pos === 'bottom' || $widget_pos === 'both') {
    $include_part('widgets-area');
}
?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($config['site_name']); ?></p>
        </div>
    </footer>

    <script>
        // Dark Mode Toggle
        const toggle = document.getElementById('theme-toggle');
        toggle.addEventListener('click', () => {
            const body = document.body;
            const current = body.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            body.setAttribute('data-theme', next);

            // Save to cookie for PHP persistence
            document.cookie = "dark_mode=" + (next === 'dark' ? '1' : '0') + ";path=/;max-age=31536000";
        });
    </script>
    <?php
    $assets = get_plugin_assets();
    foreach ($assets['js'] as $js) echo '<script src="'.$js.'"></script>';
    ?>
</body>
</html>
