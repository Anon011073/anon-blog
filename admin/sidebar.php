<div class="sidebar" style="background: #222;">
    <h2><?php echo htmlspecialchars($config['site_name']); ?></h2>
    <ul>
        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="posts.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'posts.php' || basename($_SERVER['PHP_SELF']) == 'post_edit.php' ? 'active' : ''; ?>">Posts</a></li>
        <li><a href="pages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pages.php' || basename($_SERVER['PHP_SELF']) == 'page_edit.php' ? 'active' : ''; ?>">Pages</a></li>
        <li><a href="media.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : ''; ?>">Media</a></li>
        <li><a href="comments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'comments.php' ? 'active' : ''; ?>">Comments</a></li>
        <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Settings</a></li>
        <li><a href="theme_options.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'theme_options.php' ? 'active' : ''; ?>">Theme Options</a></li>
        <li><a href="menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">Menu</a></li>
        <li><a href="widgets.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'widgets.php' ? 'active' : ''; ?>">Widgets</a></li>
        <li><a href="plugins.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'plugins.php' ? 'active' : ''; ?>">Plugins</a></li>
        <li><a href="backup.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : ''; ?>">Backup / Restore</a></li>
        <li><a href="../index.php" target="_blank">View Site</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
    <div style="padding: 10px; font-size: 0.8rem; color: #666; border-top: 1px solid #333; margin-top: 20px;">
        Version: <?php echo ANONBLOG_VERSION; ?>
    </div>
</div>
