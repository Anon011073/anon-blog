<?php
return [
    'name' => 'Prism Syntax Highlighter',
    'description' => 'Adds syntax highlighting to code blocks using Prism.js. <br><br><strong>Usage:</strong> Use triple backticks with the language name in your post editor. <br><br><strong>Example:</strong><br><pre>```php\necho "Hello World";\n```</pre>',
    'version' => '1.3',
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
        'render_content' => function($content) {
            // First, try to match markdown code blocks wrapped in literal <pre> tags (common if user follows old habits in Jodit)
            $content = preg_replace_callback('/(?:&lt;pre&gt;)?```([a-zA-Z0-9]*)\n?(.*?)\n?```(?:&lt;\/pre&gt;)?/s', function($matches) {
                $lang = $matches[1] ?: 'plain';
                $code = $matches[2];

                // Clean up Jodit artifacts inside code
                $code = str_replace(['<br>', '<br />', '&nbsp;'], ["\n", "\n", ' '], $code);
                $code = strip_tags($code);
                $code = htmlspecialchars_decode($code);

                return '<pre><code class="language-'.$lang.'">'.htmlspecialchars($code).'</code></pre>';
            }, $content);

            return $content;
        }
    ]
];
