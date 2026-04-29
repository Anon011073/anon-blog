        </main>

        <?php
        // STARTER THEME: Conditional Sidebar based on Theme Options
        if ($config['theme_options']['show_sidebar'] ?? true): ?>
            <aside class="site-sidebar">
                <div class="widget">
                    <h3>About Us</h3>
                    <p>This is a widget area in the starter theme.</p>
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
