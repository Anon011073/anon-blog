<?php
require_once __DIR__ . '/../app/functions.php';
$config = load_config();
$admin_base = (strpos($_SERVER['PHP_SELF'], '/plugins/') !== false) ? '../../../admin/' : '';

// Role-based access control for Sidebar
$user_role = 'Admin';
if (isset($_SESSION['anon_user'])) {
    $user_role = $_SESSION['anon_user']['role'] ?? 'Subscriber';
}
$is_admin = isset($_SESSION['admin_logged_in']);
?>
<div class="sidebar" style="background: #222; width: 250px; flex-shrink: 0; color: #fff; padding: 1rem;">
    <h2><?php echo htmlspecialchars($config['site_name'] ?? 'AnonBlog'); ?></h2>
    <ul style="list-style: none; padding: 0;">
        <?php if (has_permission('dashboard')): ?>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>index.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <?php endif; ?>

        <?php if (has_permission('posts')): ?>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>posts.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'posts.php' || basename($_SERVER['PHP_SELF']) == 'post_edit.php' ? 'active' : ''; ?>">Posts</a></li>
        <?php endif; ?>

        <?php if (has_permission('pages')): ?>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>pages.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pages.php' || basename($_SERVER['PHP_SELF']) == 'page_edit.php' ? 'active' : ''; ?>">Pages</a></li>
        <?php endif; ?>

        <?php if (has_permission('media')): ?>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>media.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : ''; ?>">Media</a></li>
        <?php endif; ?>

        <?php if (has_permission('comments')): ?>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>comments.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'comments.php' ? 'active' : ''; ?>">Comments</a></li>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <?php if (in_array('anon-users', $config['enabled_plugins'] ?? [])): ?>
                <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>../plugins/anon-users/admin/manage.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo strpos($_SERVER['PHP_SELF'], 'anon-users/admin/manage.php') !== false ? 'active' : ''; ?>">Users</a></li>
            <?php endif; ?>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>settings.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Settings</a></li>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>theme_options.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'theme_options.php' ? 'active' : ''; ?>">Theme Options</a></li>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>menu.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">Menu</a></li>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>widgets.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'widgets.php' ? 'active' : ''; ?>">Widgets</a></li>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>plugins.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'plugins.php' ? 'active' : ''; ?>">Plugins</a></li>
            <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>backup.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;" class="<?php echo basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : ''; ?>">Backup / Restore</a></li>
        <?php endif; ?>

        <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>../index.php" target="_blank" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;">View Site</a></li>
        <li style="margin-bottom: 10px;"><a href="<?php echo $admin_base; ?>logout.php" style="color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px;">Logout</a></li>
    </ul>
    <style>
        .sidebar a.active { background: #444; color: #fff !important; }
        .sidebar a:hover { background: #333; color: #fff !important; }
    </style>
    <div style="padding: 10px; font-size: 0.8rem; color: #666; border-top: 1px solid #333; margin-top: 20px;">
        Version: <?php echo ANONBLOG_VERSION; ?>
    </div>
</div>
