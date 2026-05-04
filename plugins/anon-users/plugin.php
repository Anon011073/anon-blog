<?php
/**
 * Plugin Name: AnonUsers Pro
 * Description: Advanced user management with roles, profiles, and registration.
 */

return [
    'name' => 'AnonUsers Pro',
    'version' => '1.1.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'plugins.php?plugin=anon-users&page=settings',
    'hooks' => [
        'system_init' => function() {
            if (session_status() === PHP_SESSION_NONE) session_start();

            // Handle actions
            $action = $_GET['user_action'] ?? '';
            if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
                return str_replace('[login_form]', ob_get_clean(), $content);
            }
            if (strpos($content, '[register_form]') !== false) {
                ob_start();
                include __DIR__ . '/views/register.php';
                return str_replace('[register_form]', ob_get_clean(), $content);
            }
            return $content;
        },
        'post_footer' => function($post) {
            $author = $post['author'] ?? 'Admin';
            $users_file = __DIR__ . '/../../content/users.json';
            $users = file_exists($users_file) ? json_decode(file_get_contents($users_file), true) : [];
            $user_data = null;
            foreach ($users as $u) {
                if ($u['username'] === $author) { $user_data = $u; break; }
            }
            if ($user_data && !empty($user_data['bio'])) {
                echo '<div class="author-bio" style="margin-top:2rem; padding:1.5rem; background:rgba(0,0,0,0.05); border-radius:8px;">';
                echo '<h4>About ' . htmlspecialchars($user_data['nickname'] ?? $author) . '</h4>';
                echo '<p>' . nl2br(htmlspecialchars($user_data['bio'])) . '</p>';
                echo '</div>';
            }
        }
    ]
];
