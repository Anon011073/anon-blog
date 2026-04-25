            </main>
            <aside class="site-sidebar">
                <?php
                $sidebar_widgets = $config['widget_areas']['sidebar'] ?? ['search', 'recent_posts'];
                foreach ($sidebar_widgets as $w) $include_part('widget-' . $w);
                ?>
            </aside>
        </div>
    </div>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-widgets">
                <div class="footer-widget">
                    <?php
                    $f1_widgets = $config['widget_areas']['footer1'] ?? [];
                    if (empty($f1_widgets)) echo '<h3>About</h3><p>Welcome to '.htmlspecialchars($config['site_name']).'</p>';
                    else foreach ($f1_widgets as $w) $include_part('widget-' . $w);
                    ?>
                </div>
                <div class="footer-widget">
                    <?php
                    $f2_widgets = $config['widget_areas']['footer2'] ?? [];
                    if (empty($f2_widgets)) echo '<h3>Links</h3><ul><li><a href="index.php">Home</a></li></ul>';
                    else foreach ($f2_widgets as $w) $include_part('widget-' . $w);
                    ?>
                </div>
                <div class="footer-widget">
                    <?php
                    $f3_widgets = $config['widget_areas']['footer3'] ?? [];
                    if (empty($f3_widgets)) $include_part('widget-recent_posts', ['limit' => 3]);
                    else foreach ($f3_widgets as $w) $include_part('widget-' . $w);
                    ?>
                </div>
            </div>
            <div class="footer-bottom">
                <a href="#" id="back-to-top">Back to top ↑</a><br>
                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($config['site_name']); ?>. Built with Jules CMS.
            </div>
        </div>
    </footer>
    <script>
        const toggle = document.getElementById('theme-toggle');
        toggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        });

        document.getElementById('back-to-top').addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    </script>
    <?php
    $theme_assets = get_plugin_assets();
    foreach ($theme_assets['js'] as $js) echo '<script src="'.$js.'"></script>';
    ?>
</body>
</html>
