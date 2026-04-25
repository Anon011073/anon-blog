<?php
/**
 * Authentication handling
 */

require_once __DIR__ . '/functions.php';

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
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
        redirect('/admin/login.php');
    }
}
