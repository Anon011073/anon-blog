<?php $include_part('header'); ?>

<article class="post-full">
    <header class="post-header">
        <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="post-meta">
            <time><?php echo format_date($post['date']); ?></time>
            <?php
            $show_tax = ($config['theme_options'] ?? [])['show_tax_meta'] ?? true;
            if ($show_tax): ?>
                <?php if (isset($post['category'])): ?>
                    <span class="meta-sep">&bull;</span>
                    <span class="category"><?php echo htmlspecialchars($post['category']); ?></span>
                <?php endif; ?>
                <?php if (!empty($post['tags'])): ?>
                    <div class="post-tags" style="margin-top: 10px;">
                        <?php foreach($post['tags'] as $tag): ?>
                            <span class="tag">#<?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </header>

    <?php if (!empty($post['featured_image'])): ?>
        <div class="post-featured-image">
            <img src="uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="">
        </div>
    <?php endif; ?>

    <div class="post-content">
        <?php echo markdown_to_html($post['content']); ?>
    </div>
</article>

<?php $include_part('footer'); ?>
