<?php
/**
 * Authentication handling
 */

require_once __DIR__ . '/functions.php';

function is_logged_in() {
    // Check for main admin
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return true;
    }
    // Check for Author role via AnonUsers
    if (isset($_SESSION['anon_user'])) {
        $user = $_SESSION['anon_user'];
        if (($user['role'] ?? '') === 'Author') {
            return true;
        }
    }
    return false;
}

function login($username, $password) {
    $config = load_config();
    if ($username === $config['admin_user'] && password_verify($password, $config['admin_pass'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_name'] = $username;
        return true;
    }
    return false;
}

function logout() {
    $_SESSION = [];
    session_destroy();
}

function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}
