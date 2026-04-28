<div class="anon-users-profile">
    <?php if (isset($_SESSION['anon_user'])):
        $user = $_SESSION['anon_user'];
    ?>
        <h3>Welcome, <?php echo htmlspecialchars($user['nickname']); ?></h3>
        <p>Role: <?php echo htmlspecialchars($user['role'] ?? 'Subscriber'); ?></p>

        <div class="news-section card" style="background: #fff; padding: 15px; border-radius: 8px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4>📢 Latest News</h4>
            <?php
            $news = get_admin_news();
            if (empty($news)): ?>
                <p>No news yet.</p>
            <?php else:
                foreach ($news as $item): ?>
                <div class="news-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong> <span style="font-size: 0.8rem; color: #888;"><?php echo $item['date']; ?></span>
                    <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="user-actions" style="margin-top: 20px;">
            <a href="?logout=1" class="btn btn-danger" style="background: #d9534f; color: #fff; padding: 5px 10px; border-radius: 4px; text-decoration: none;">Logout</a>
        </div>
    <?php else: ?>
        <p>Please login to see your profile.</p>
    <?php endif; ?>
</div>
