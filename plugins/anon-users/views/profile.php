<div class="anon-users-profile">
    <?php if (isset($_SESSION['anon_user'])):
        $user = $_SESSION['anon_user'];
    ?>
        <h3>Welcome, <?php echo htmlspecialchars($user['nickname']); ?></h3>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?><br>
        <strong>Role:</strong> <?php echo htmlspecialchars($user['role'] ?? 'Subscriber'); ?></p>

        <div class="news-section card" style="background: #fff; padding: 15px; border-radius: 8px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4>📢 Latest News</h4>
            <?php
            $news = get_admin_news();
            if (empty($news)): ?>
                <p>No news yet.</p>
            <?php else:
                foreach ($news as $item): ?>
                <div class="news-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong> <span style="font-size: 0.8rem; color: #333;"><?php echo $item['date']; ?></span>
                    <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="user-content" style="margin-top: 30px;">
            <h4>📝 Your Activity</h4>
            <?php
            require_once __DIR__ . '/../../../app/posts.php';
            require_once __DIR__ . '/../../../app/comments.php';
            $user_posts = array_filter(get_posts(true), function($p) use ($user) { return ($p['author'] ?? '') === $user['username']; });
            $all_comments = [];
            $post_files = glob(__DIR__ . '/../../../content/comments/*.json');
            foreach ($post_files as $f) {
                $comments = json_decode(file_get_contents($f), true) ?: [];
                foreach ($comments as $c) {
                    if (($c['nickname'] ?? '') === $user['nickname']) {
                        $c['post_slug'] = basename($f, '.json');
                        $all_comments[] = $c;
                    }
                }
            }
            ?>
            <h5>Your Posts (<?php echo count($user_posts); ?>)</h5>
            <?php if (empty($user_posts)): ?>
                <p>You haven't posted any articles yet.</p>
            <?php else: ?>
                <ul>
                <?php foreach ($user_posts as $p): ?>
                    <li><a href="index.php?post=<?php echo $p['slug']; ?>"><?php echo htmlspecialchars($p['title']); ?></a> (<?php echo $p['status'] ?? 'published'; ?>)</li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h5 style="margin-top: 20px;">Your Recent Comments</h5>
            <?php if (empty($all_comments)): ?>
                <p>No comments found.</p>
            <?php else: ?>
                <ul style="font-size: 0.9rem;">
                <?php foreach (array_slice($all_comments, 0, 10) as $c): ?>
                    <li>On <a href="index.php?post=<?php echo $c['post_slug']; ?>"><?php echo $c['post_slug']; ?></a>: "<?php echo htmlspecialchars(substr($c['content'], 0, 50)); ?>..."</li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="user-actions" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <a href="?logout=1" class="btn btn-danger" style="background: #d9534f; color: #fff; padding: 5px 10px; border-radius: 4px; text-decoration: none;">Logout</a>
            <?php if (($user['role'] ?? '') === 'Author'): ?>
                <a href="admin/posts.php" class="btn" style="background: #333; color: #fff; padding: 5px 10px; border-radius: 4px; text-decoration: none; margin-left: 10px;">Go to Admin Panel</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Please login to see your profile.</p>
    <?php endif; ?>
</div>
