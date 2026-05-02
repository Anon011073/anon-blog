<?php
/**
 * News system for users
 */

define('NEWS_FILE', __DIR__ . '/../../../content/news.json');

function get_admin_news() {
    if (file_exists(NEWS_FILE)) {
        return json_decode(file_get_contents(NEWS_FILE), true) ?: [];
    }
    return [];
}

function add_admin_news($title, $content) {
    $news = get_admin_news();
    array_unshift($news, [
        'title' => sanitize($title),
        'content' => sanitize($content),
        'date' => date('Y-m-d H:i')
    ]);
    file_put_contents(NEWS_FILE, json_encode(array_slice($news, 0, 20), JSON_PRETTY_PRINT));
}
