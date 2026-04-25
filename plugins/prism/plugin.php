<?php
return [
    'name' => 'Prism Syntax Highlighter',
    'description' => 'Adds syntax highlighting to code blocks using Prism.js. <br><br><strong>Usage:</strong> Use triple backticks with the language name in your post editor. <br><br><strong>Example:</strong><br><pre>```php\necho "Hello World";\n```</pre>',
    'version' => '1.1',
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
            // Convert markdown code blocks to Prism-compatible HTML
            $markdown = preg_replace('/```([a-zA-Z0-9]+)\n(.*?)\n```/s', '<pre><code class="language-$1">$2</code></pre>', $markdown);
            return $markdown;
        }
    ]
];
