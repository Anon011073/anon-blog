<?php
/**
 * User Roles & Management for AnonBlog
 */

require_once __DIR__ . '/app/users.php';
require_once __DIR__ . '/app/news.php';

// Metadata for the plugin system
$plugin_meta = [
    'name' => 'AnonUsers Pro',
    'description' => 'Adds User Registration, Roles (Author/Subscriber), Profiles, and Admin News.',
    'version' => '1.0.1',
    'author' => 'AnonBlog Team',
    'hooks' => [
        'render_content' => function($content) {
            // Shortcodes for [register], [login], [profile]
            if (strpos($content, '[register]') !== false) {
                $content = str_replace('[register]', anon_render_registration(), $content);
            }
            if (strpos($content, '[login]') !== false) {
                $content = str_replace('[login]', anon_render_login(), $content);
            }
            if (strpos($content, '[profile]') !== false) {
                // If not logged in, show login form on profile page
                if (!isset($_SESSION['anon_user'])) {
                    $content = str_replace('[profile]', '<h3 style="text-align:center;">Please log in to view your profile.</h3>' . anon_render_login(), $content);
                } else {
                    $content = str_replace('[profile]', anon_render_profile(), $content);
                }
            }
            return $content;
        }
    ]
];

// Handle Frontend Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anon_action'])) {
    if ($_POST['anon_action'] === 'register') {
        $username = sanitize($_POST['username']);
        $nickname = sanitize($_POST['nickname']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if (!get_anon_user($username)) {
            save_anon_user([
                'username' => $username,
                'nickname' => $nickname,
                'password' => $password,
                'role' => 'Subscriber'
            ]);
            $_SESSION['anon_msg'] = "Registration successful! Please login.";
            header("Location: index.php?page=login");
            exit;
        } else {
            $_SESSION['anon_error'] = "Username already exists.";
        }
    } elseif ($_POST['anon_action'] === 'login') {
        $user = anon_authenticate($_POST['username'], $_POST['password']);
        if ($user) {
            $_SESSION['anon_user'] = $user;
            header("Location: index.php?page=profile");
            exit;
        } else {
            $_SESSION['anon_error'] = "Invalid credentials.";
        }
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['anon_user']);
    header("Location: index.php");
    exit;
}

if (!function_exists('anon_render_registration')) {
    function anon_render_registration() {
        ob_start();
        include __DIR__ . '/views/register.php';
        return ob_get_clean();
    }
}

if (!function_exists('anon_render_login')) {
    function anon_render_login() {
        ob_start();
        include __DIR__ . '/views/login.php';
        return ob_get_clean();
    }
}

if (!function_exists('anon_render_profile')) {
    function anon_render_profile() {
        ob_start();
        include __DIR__ . '/views/profile.php';
        return ob_get_clean();
    }
}

return $plugin_meta;
