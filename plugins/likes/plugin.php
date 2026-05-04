<?php
/**
 * Plugin Name: Likes/Dislikes
 * Description: Interactive AJAX-based voting system for posts.
 */

return [
    'name' => 'Likes/Dislikes',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'hooks' => [
        'system_header' => function() {
            ?>
            <style>
                .anon-likes { display: flex; gap: 1rem; margin: 1rem 0; }
                .anon-like-btn { cursor: pointer; border: none; background: #eee; padding: 5px 12px; border-radius: 4px; transition: transform 0.2s; }
                .anon-like-btn:hover { background: #ddd; }
                .anon-like-btn.pop { transform: scale(1.2); }
            </style>
            <script>
                function vote(slug, type) {
                    fetch('index.php?vote_slug=' + slug + '&vote_type=' + type)
                        .then(r => r.json())
                        .then(data => {
                            if(data.success) {
                                document.getElementById('count-' + type + '-' + slug).innerText = data.count;
                                const btn = document.getElementById('btn-' + type + '-' + slug);
                                btn.classList.add('pop');
                                setTimeout(() => btn.classList.remove('pop'), 200);
                            }
                        });
                }
            </script>
            <?php
        },
        'system_init' => function() {
            if (isset($_GET['vote_slug']) && isset($_GET['vote_type'])) {
                $slug = $_GET['vote_slug'];
                $type = $_GET['vote_type'];
                $file = __DIR__ . '/../../content/votes-' . $slug . '.json';
                $votes = file_exists($file) ? json_decode(file_get_contents($file), true) : ['likes' => 0, 'dislikes' => 0];

                if ($type === 'like') $votes['likes']++;
                else if ($type === 'dislike') $votes['dislikes']++;

                file_put_contents($file, json_encode($votes));
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'count' => ($type === 'like' ? $votes['likes'] : $votes['dislikes'])]);
                exit;
            }
        },
        'post_footer' => function($post) {
            $slug = $post['slug'];
            $file = __DIR__ . '/../../content/votes-' . $slug . '.json';
            $votes = file_exists($file) ? json_decode(file_get_contents($file), true) : ['likes' => 0, 'dislikes' => 0];
            ?>
            <div class="anon-likes">
                <button id="btn-like-<?php echo $slug; ?>" class="anon-like-btn" onclick="vote('<?php echo $slug; ?>', 'like')">
                    👍 <span id="count-like-<?php echo $slug; ?>"><?php echo $votes['likes']; ?></span>
                </button>
                <button id="btn-dislike-<?php echo $slug; ?>" class="anon-like-btn" onclick="vote('<?php echo $slug; ?>', 'dislike')">
                    👎 <span id="count-dislike-<?php echo $slug; ?>"><?php echo $votes['dislikes']; ?></span>
                </button>
            </div>
            <?php
        }
    ]
];
