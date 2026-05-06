<?php
/**
 * Plugin Name: AnonUsers Pro
 * Description: Advanced user management with roles, profiles, and registration.
 */

return [
    'name' => 'AnonUsers Pro',
    'version' => '1.3.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'settings.php?plugin=anon-users',
    'hooks' => [
        'system_init' => function() {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $action = $_GET['user_action'] ?? '';
            if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $config = load_config();
                if (!($config['allow_registration'] ?? true)) die('Registration is disabled.');

                $users_file = __DIR__ . '/../../content/users.json';
                $users = file_exists($users_file) ? json_decode(file_get_contents($users_file), true) : [];
                $username = sanitize($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                if ($username && $password) {
                    $users[] = [
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => 'Subscriber',
                        'nickname' => $username,
                        'bio' => ''
                    ];
                    file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
                    header('Location: index.php?page=login&msg=registered');
                    exit;
                }
            }
        },
        'render_content' => function($content) {
            if (strpos($content, '[login_form]') !== false) {
                ob_start();
                include __DIR__ . '/views/login.php';
                $content = str_replace('[login_form]', ob_get_clean(), $content);
            }
            if (strpos($content, '[register_form]') !== false) {
                $config = load_config();
                if ($config['allow_registration'] ?? true) {
                    ob_start();
                    include __DIR__ . '/views/register.php';
                    $content = str_replace('[register_form]', ob_get_clean(), $content);
                } else {
                    $content = str_replace('[register_form]', '<p>Registration is currently closed.</p>', $content);
                }
            }
            return $content;
        }
    ]
];
