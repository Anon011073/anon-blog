<?php
return [
    'name' => 'Prism Syntax Highlighter',
    'description' => 'Adds syntax highlighting to code blocks using Prism.js.',
    'version' => '1.0',
    'author' => 'Jules',
    'assets' => [
        'css' => [
            'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css'
        ],
        'js' => [
            'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js'
        ]
    ],
    'hooks' => [
        'markdown_to_html' => function($markdown) {
            // Prism uses <pre><code class="language-xxxx">
            // Our basic markdown converter doesn't do code blocks well, so let's add them
            $markdown = preg_replace('/```([a-zA-Z0-9]+)\n(.*?)\n```/s', '<pre><code class="language-$1">$2</code></pre>', $markdown);
            return $markdown;
        }
    ]
];
