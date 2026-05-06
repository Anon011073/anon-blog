<?php $include_part('header'); ?>

<?php if (empty($posts)): ?>
    <p>No posts found.</p>
<?php else: ?>
    <?php
    $opts = $config['theme_options'] ?? [];
    $layout = $opts['front_page_layout'] ?? 'default';
    $img_pos = $opts['featured_image_position'] ?? 'top';
    ?>

    <div class="post-list-wrapper <?php echo 'template-' . $layout; ?>">
        <div class="post-list">
            <?php foreach ($posts as $post): ?>
                <article class="post-card <?php echo 'img-' . ($layout === 'grid' || $layout === 'grid_sidebar' ? 'top' : $img_pos); ?>">
                    <?php if (!empty($post['featured_image'])): ?>
                        <div class="post-thumbnail">
                            <a href="index.php?post=<?php echo $post['slug']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="" loading="lazy">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="post-content">
                        <h2 class="post-title"><a href="index.php?post=<?php echo $post['slug']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        <div class="post-meta">Published on <?php echo format_date($post['date']); ?></div>
                        <div class="post-excerpt"><?php echo nl2br(htmlspecialchars($post['excerpt'])); ?></div>
                        <a href="index.php?post=<?php echo $post['slug']; ?>" class="read-more">Read More &rarr;</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php $include_part('footer'); ?>
