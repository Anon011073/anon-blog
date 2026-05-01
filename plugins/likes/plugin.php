<?php
/**
 * Like/Dislike Plugin for AnonBlog
 */

$config = load_config();
$likes_options = $config['likes_options'] ?? ['icon_set' => 'thumbs'];
$set = $likes_options['icon_set'];

$icons = [
    'thumbs' => ['pos' => '👍', 'neg' => '👎'],
    'hearts' => ['pos' => '❤️', 'neg' => '💔'],
    'stars'  => ['pos' => '⭐', 'neg' => '💀']
];
$pos_icon = $icons[$set]['pos'] ?? '👍';
$neg_icon = $icons[$set]['neg'] ?? '👎';

return [
    'name' => 'Likes & Dislikes',
    'description' => 'Add simple like and dislike buttons to your posts.',
    'author' => 'AnonBlog Team',
    'version' => '1.2.0',
    'settings_url' => '../plugins/likes/admin/settings.php',
    'hooks' => [
        'render_content' => function($content) use ($pos_icon, $neg_icon) {
            if (isset($_GET['post'])) {
                $slug = $_GET['post'];
                $counts = get_like_counts($slug);

                // Construct path for AJAX dynamically to handle subfolders
                $root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                // Get the base directory where AnonBlog is installed
                $base = str_replace(['index.php', 'admin/'], '', $_SERVER['SCRIPT_NAME']);
                $ajax_url = $root . $base . "plugins/likes/ajax.php";

                $html = '
                <div class="anon-likes" style="margin-top: 30px; padding: 20px; border-top: 2px solid rgba(128,128,128,0.2); display: flex; gap: 20px; align-items: center; clear: both;">
                    <button type="button" class="like-btn" onclick="anonLike(\''.$slug.'\', \'like\')" style="background: rgba(128,128,128,0.08); border: 1px solid rgba(128,128,128,0.2); padding: 10px 20px; cursor: pointer; border-radius: 8px; color: inherit; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <span>'.$pos_icon.'</span> <span class="like-count" style="font-weight: bold; min-width: 15px; color: inherit;">'.$counts['likes'].'</span>
                    </button>
                    <button type="button" class="dislike-btn" onclick="anonLike(\''.$slug.'\', \'dislike\')" style="background: rgba(128,128,128,0.08); border: 1px solid rgba(128,128,128,0.2); padding: 10px 20px; cursor: pointer; border-radius: 8px; color: inherit; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <span>'.$neg_icon.'</span> <span class="dislike-count" style="font-weight: bold; min-width: 15px; color: inherit;">'.$counts['dislikes'].'</span>
                    </button>
                </div>
                <script>
                if (typeof anonLike !== "function") {
                    function anonLike(slug, type) {
                        const url = "'.$ajax_url.'?slug=" + encodeURIComponent(slug) + "&type=" + type;
                        fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                document.querySelector(".like-count").innerText = data.likes;
                                document.querySelector(".dislike-count").innerText = data.dislikes;
                                if(data.message === "Already voted") {
                                    alert("You have already voted on this post.");
                                }
                            }
                        }).catch(err => {
                            console.error("Like error:", err);
                            // Fallback if full URL fails
                            fetch("plugins/likes/ajax.php?slug=" + encodeURIComponent(slug) + "&type=" + type)
                            .then(r => r.json()).then(d => {
                                if(d.success) {
                                    document.querySelector(".like-count").innerText = d.likes;
                                    document.querySelector(".dislike-count").innerText = d.dislikes;
                                }
                            });
                        });
                    }
                }
                </script>
                <style>
                    .like-btn:hover, .dislike-btn:hover { background: rgba(128,128,128,0.15) !important; transform: translateY(-2px); }
                    .like-btn:active, .dislike-btn:active { transform: translateY(0); }
                </style>
                ';
                return $content . $html;
            }
            return $content;
        }
    ]
];

if (!function_exists('get_like_counts')) {
    function get_like_counts($slug) {
        $file = __DIR__ . '/../../config/likes.json';
        $data = [];
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true) ?: [];
        }
        return $data[$slug] ?? ['likes' => 0, 'dislikes' => 0];
    }
}
