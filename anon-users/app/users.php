<?php
/**
 * User roles and registration logic for AnonUsers plugin
 */

define('USERS_DIR', __DIR__ . '/../../../content/users/');

if (!is_dir(USERS_DIR)) {
    mkdir(USERS_DIR, 0755, true);
}

function get_anon_users() {
    $users = [];
    $files = glob(USERS_DIR . '*.json');
    foreach ($files as $file) {
        $user = json_decode(file_get_contents($file), true);
        if ($user) $users[] = $user;
    }
    return $users;
}

function get_anon_user($username) {
    $file = USERS_DIR . $username . '.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return null;
}

function save_anon_user($data) {
    $username = $data['username'];
    $file = USERS_DIR . $username . '.json';
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function delete_anon_user($username) {
    $file = USERS_DIR . $username . '.json';
    if (file_exists($file)) return unlink($file);
    return false;
}

function anon_authenticate($username, $password) {
    $user = get_anon_user($username);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}
