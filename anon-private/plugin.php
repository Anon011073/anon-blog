<?php
/**
 * Private Site for AnonBlog
 */
return [
    'name' => 'AnonPrivate',
    'description' => 'Makes your entire blog members-only. Note: Guests will be redirected to the profile/login page.',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'hooks' => [
        'system_init' => function() {
            $is_admin = isset($_SESSION['admin_logged_in']);
            $is_user = isset($_SESSION['anon_user']);
            $current_page = $_GET['page'] ?? '';

            if ($current_page === 'register' || $current_page === 'profile') return;
            if (basename($_SERVER['PHP_SELF']) === 'install.php') return;
            if (strpos($_SERVER['PHP_SELF'], 'admin/') !== false) return;

            if (!$is_admin && !$is_user) {
                header("Location: index.php?page=profile");
                die();
            }
        }
    ]
];
