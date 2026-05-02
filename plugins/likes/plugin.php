<?php
/**
 * Like/Dislike Plugin for AnonBlog
 */

$config = load_config();
$likes_options = $config['likes_options'] ?? ['icon_set' => 'thumbs'];
$set = $likes_options['icon_set'] ?? 'thumbs';

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
    'version' => '1.2.4',
    'settings_url' => '../plugins/likes/admin/settings.php',
    'hooks' => [
        'render_content' => function($content) use ($pos_icon, $neg_icon) {
            if (isset($_GET['post'])) {
                $slug = $_GET['post'];

                // Fetch counts directly here to avoid undefined function issues
                $file = __DIR__ . '/../../config/likes.json';
                $data = [];
                if (file_exists($file)) {
                    $data = json_decode(file_get_contents($file), true) ?: [];
                }
                $counts = $data[$slug] ?? ['likes' => 0, 'dislikes' => 0];

                $root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $base = str_replace(['index.php', 'admin/'], '', $_SERVER['SCRIPT_NAME']);
                $ajax_url = $root . $base . "plugins/likes/ajax.php";

                $html = '
                <div class="anon-likes" style="margin-top: 30px; padding: 20px; border-top: 2px solid rgba(128,128,128,0.2); display: flex; gap: 20px; align-items: center; clear: both;">
                    <button type="button" class="like-btn" onclick="anonLike(\''.$slug.'\', \'like\')" style="background: rgba(128,128,128,0.1); border: 1px solid rgba(128,128,128,0.2); padding: 10px 20px; cursor: pointer; border-radius: 8px; color: inherit; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; transition: 0.3s;">
                        <span>'.$pos_icon.'</span> <span class="like-count" style="font-weight: bold; color: inherit;">'.$counts['likes'].'</span>
                    </button>
                    <button type="button" class="dislike-btn" onclick="anonLike(\''.$slug.'\', \'dislike\')" style="background: rgba(128,128,128,0.1); border: 1px solid rgba(128,128,128,0.2); padding: 10px 20px; cursor: pointer; border-radius: 8px; color: inherit; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; transition: 0.3s;">
                        <span>'.$neg_icon.'</span> <span class="dislike-count" style="font-weight: bold; color: inherit;">'.$counts['dislikes'].'</span>
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
                        }).catch(err => console.error("Like error:", err));
                    }
                }
                </script>
                ';
                return $content . $html;
            }
            return $content;
        }
    ]
];
