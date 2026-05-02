<?php
/**
 * Ajax handler for Likes/Dislikes
 */

require_once __DIR__ . '/../../app/functions.php';

$slug = $_GET['slug'] ?? '';
$type = $_GET['type'] ?? '';

if (!$slug || !in_array($type, ['like', 'dislike'])) {
    echo json_encode(['success' => false]);
    exit;
}

$file = __DIR__ . '/../../config/likes.json';
$data = [];
if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true) ?: [];
}

if (!isset($data[$slug])) {
    $data[$slug] = ['likes' => 0, 'dislikes' => 0];
}

// Check session to prevent multiple likes from same session (very basic protection)
session_start();
$voted_key = "voted_" . $slug;
if (isset($_SESSION[$voted_key])) {
    // Already voted
    echo json_encode(['success' => true, 'likes' => $data[$slug]['likes'], 'dislikes' => $data[$slug]['dislikes'], 'message' => 'Already voted']);
    exit;
}

if ($type === 'like') {
    $data[$slug]['likes']++;
} else {
    $data[$slug]['dislikes']++;
}

$_SESSION[$voted_key] = true;

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'likes' => $data[$slug]['likes'],
    'dislikes' => $data[$slug]['dislikes']
]);
