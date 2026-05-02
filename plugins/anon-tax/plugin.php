<?php
/**
 * Categories & Tags for AnonBlog
 */

return [
    'name' => 'AnonTax',
    'description' => 'Adds Categories and Tags to posts.',
    'author' => 'AnonBlog Team',
    'version' => '1.0.0',
    'hooks' => [
        'render_content' => function($content) {
            // This plugin mostly acts on data structures, but could add UI elements here
            return $content;
        }
    ]
];
