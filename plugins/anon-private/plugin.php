<?php
/**
 * Plugin Name: AnonPrivate
 * Description: Restrict site access to logged in users.
 */

return [
    'name' => 'AnonPrivate',
    'hooks' => [
        'system_init' => function() {
            $config = load_config();
            if (($config['private_mode'] ?? false) && !isset($_SESSION['admin_logged_in'])) {
                if (strpos($_SERVER['PHP_SELF'], 'login.php') === false && (!isset($_GET['page']) || $_GET['page'] !== 'login')) {
                    header('Location: admin/login.php');
                    exit;
                }
            }
        }
    ]
];
