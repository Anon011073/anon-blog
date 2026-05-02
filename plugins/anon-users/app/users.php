<?php
/**
 * AnonUsers internal logic
 */

define('ANON_USERS_DIR', __DIR__ . '/../../../content/users/');
if (!is_dir(ANON_USERS_DIR)) mkdir(ANON_USERS_DIR, 0755, true);

function get_anon_user($username) {
    $file = ANON_USERS_DIR . sanitize($username) . '.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return null;
}

function save_anon_user($data) {
    $file = ANON_USERS_DIR . sanitize($data['username']) . '.json';
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function anon_authenticate($user, $pass) {
    $data = get_anon_user($user);
    if ($data && password_verify($pass, $data['password'])) {
        return $data;
    }
    return null;
}
