<?php
return [
    'name' => 'Prism Syntax Highlighter',
    'description' => 'Adds syntax highlighting to code blocks using Prism.js. <br><br><strong>Usage:</strong> Use triple backticks with the language name in your post editor. <br><br><strong>Example:</strong><br><pre>```php\necho "Hello World";\n```</pre>',
    'version' => '1.4',
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
            // Match potential <pre> wrappers with optional classes, containing backticks
            // Using &lt; and &gt; because Jodit/Markdown parser might have escaped them
            $pattern = '/(?:&lt;pre(?:\s+class=["\'](.*?)["\'])?&gt;)?\s*```([a-zA-Z0-9]*)\n?(.*?)\n?```\s*(?:&lt;\/pre&gt;)?/s';

            $content = preg_replace_callback($pattern, function($matches) {
                $pre_class = $matches[1] ?? '';
                $md_lang = $matches[2] ?? '';
                $code = $matches[3];

                $lang = 'plain';
                if ($md_lang) {
                    $lang = $md_lang;
                } elseif ($pre_class && preg_match('/language-([a-zA-Z0-9]+)/', $pre_class, $m)) {
                    $lang = $m[1];
                }

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
