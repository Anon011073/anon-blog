<?php $include_part('header'); ?>

<?php
$img_pos = $config['featured_image_position'] ?? 'top';
?>

<article class="post-full">
    <header class="post-header">
        <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="post-meta">Published on <?php echo format_date($post['date']); ?></div>
    </header>

    <?php if (!empty($post['featured_image'])): ?>
        <div class="post-featured-image img-top">
            <img src="uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
        </div>
    <?php endif; ?>

    <div class="post-content">
        <?php echo markdown_to_html($post['content']); ?>
    </div>

    <section class="comments-section">
        <h3>Comments</h3>
        <?php
        $comments_enabled = ($config['comments_enabled'] ?? true) && ($post['comments_on'] ?? true);
        if ($comments_enabled):
            require_once __DIR__ . '/../../app/comments.php';
            $comments = get_comments($post['slug']);

            if (!empty($comments)):
                foreach ($comments as $comment):
                    if (!($comment['approved'] ?? false)) continue;
        ?>
                    <div class="comment">
                        <div class="comment-header">
                            <strong><?php echo htmlspecialchars($comment['nickname']); ?></strong>
                            <span class="comment-date"><?php echo format_date($comment['date']); ?></span>
                        </div>
                        <div class="comment-body">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                    </div>
        <?php
                endforeach;
            else:
                echo "<p>No comments yet. Be the first to comment!</p>";
            endif;
        ?>
            <hr>
            <h4>Leave a Comment</h4>
            <form action="app/comment_submit.php" method="POST" class="comment-form">
                <input type="hidden" name="post_slug" value="<?php echo htmlspecialchars($post['slug']); ?>">
                <div class="form-group">
                    <label for="nickname">Nickname</label>
                    <input type="text" id="nickname" name="nickname" required>
                </div>
                <div class="form-group">
                    <label for="comment_content">Comment</label>
                    <textarea id="comment_content" name="content" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Comment</button>
            </form>
        <?php else: ?>
            <p>Comments are closed for this post.</p>
        <?php endif; ?>
    </section>
</article>

<?php $include_part('footer'); ?>
