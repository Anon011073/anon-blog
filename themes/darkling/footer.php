            <?php
            $widgets = $config['widget_areas'] ?? [];
            if (!empty($widgets['lower'])): ?>
                <div class="widget-area">
                    <?php foreach ($widgets['lower'] as $w):
                        if (file_exists(__DIR__ . "/widget-{$w}.php")) include __DIR__ . "/widget-{$w}.php";
                    endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($config['site_name']); ?></p>
        </div>
    </footer>

    <?php
    foreach (get_enabled_plugins_data() as $p) {
        if (isset($p['hooks']['system_footer'])) echo $p['hooks']['system_footer']();
    }
    ?>
</body>
</html>
