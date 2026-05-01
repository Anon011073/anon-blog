        </main>

        <?php if ($config['theme_options']['show_sidebar'] ?? true): ?>
            <aside class="site-sidebar">
                <?php
                $sidebar_widgets = $config['widget_areas']['sidebar'] ?? ['search', 'recent_posts'];
                foreach ($sidebar_widgets as $w) $include_part('widget-' . $w);
                ?>
                <div class="widget">
                    <h3>About Us</h3>
                    <p>This is a hardcoded widget area in the starter theme to show you how to add static content.</p>
                </div>
            </aside>
        <?php endif; ?>
    </div>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($config['theme_options']['footer_text'] ?? 'AnonBlog'); ?></p>
        </div>
    </footer>
</body>
</html>
