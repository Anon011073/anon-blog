<?php
/**
 * Like/Dislike Plugin for AnonBlog
 */

return [
    'name' => 'Likes & Dislikes',
    'description' => 'Add simple like and dislike buttons to your posts.',
    'author' => 'AnonBlog Team',
    'version' => '1.0.0',
    'assets' => [
        'css' => [],
        'js' => []
    ],
    'hooks' => [
        'render_content' => function($content) {
            // Only add to single post pages if we are in a post context
            // Since this hook is called in markdown_to_html, we need a way to know if we are on a post page.
            // A better way might be a footer hook or a specific 'post_footer' hook if I had one.
            // For now, let's inject it at the end of the content.

            if (isset($_GET['post'])) {
                $slug = $_GET['post'];
                $counts = get_like_counts($slug);

                $html = '
                <div class="anon-likes" data-slug="'.htmlspecialchars($slug).'" style="margin-top: 20px; padding: 15px; border-top: 1px solid #eee; display: flex; gap: 20px; align-items: center;">
                    <button class="like-btn" onclick="anonLike(\''.$slug.'\', \'like\')" style="background: none; border: 1px solid #ddd; padding: 5px 15px; cursor: pointer; border-radius: 4px;">
                        👍 <span class="like-count">'.$counts['likes'].'</span>
                    </button>
                    <button class="dislike-btn" onclick="anonLike(\''.$slug.'\', \'dislike\')" style="background: none; border: 1px solid #ddd; padding: 5px 15px; cursor: pointer; border-radius: 4px;">
                        👎 <span class="dislike-count">'.$counts['dislikes'].'</span>
                    </button>
                </div>
                <script>
                function anonLike(slug, type) {
                    fetch("plugins/likes/ajax.php?slug=" + slug + "&type=" + type)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            document.querySelector(".like-count").innerText = data.likes;
                            document.querySelector(".dislike-count").innerText = data.dislikes;
                        }
                    });
                }
                </script>
                ';
                return $content . $html;
            }
            return $content;
        }
    ]
];

function get_like_counts($slug) {
    $file = __DIR__ . '/../../config/likes.json';
    $data = [];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: [];
    }
    return $data[$slug] ?? ['likes' => 0, 'dislikes' => 0];
}
