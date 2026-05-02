<?php
/**
 * Members Only Plugin for AnonBlog
 */

return [
    'name' => 'AnonPrivate',
    'description' => 'Redirects guests to login page if they try to access the site.',
    'author' => 'AnonBlog Team',
    'version' => '1.0.0',
    'hooks' => [
        'system_init' => function() {
            if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) return;
            if (isset($_GET['page']) && ($_GET['page'] === 'login' || $_GET['page'] === 'register')) return;

            if (!isset($_SESSION['anon_user']) && !isset($_SESSION['admin_logged_in'])) {
                header("Location: index.php?page=login");
                exit;
            }
        }
    ]
];
