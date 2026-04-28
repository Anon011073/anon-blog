<?php
/**
 * Admin News logic for AnonUsers plugin
 */

define('NEWS_FILE', __DIR__ . '/../../../content/news.json');

function get_admin_news() {
    if (!file_exists(NEWS_FILE)) return [];
    return json_decode(file_get_contents(NEWS_FILE), true) ?: [];
}

function add_admin_news($title, $content) {
    $news = get_admin_news();
    array_unshift($news, [
        'id' => uniqid(),
        'title' => $title,
        'content' => $content,
        'date' => date('Y-m-d H:i:s')
    ]);
    file_put_contents(NEWS_FILE, json_encode($news, JSON_PRETTY_PRINT));
}
