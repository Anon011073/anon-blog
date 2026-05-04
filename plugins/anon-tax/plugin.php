<?php
/**
 * Plugin Name: AnonTax
 * Description: Categories and Tags management.
 */

return [
    'name' => 'AnonTax',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'settings_url' => 'plugins.php?plugin=anon-tax&page=settings',
    'hooks' => [
        'post_form_after' => function($post) {
            $categories = $post['categories'] ?? '';
            $tags = $post['tags'] ?? '';
            ?>
            <div class="form-group" style="margin-top:1rem; padding:1rem; border:1px solid #ddd; background:#f9f9f9;">
                <h4>Taxonomy (AnonTax)</h4>
                <div class="form-row">
                    <div class="col">
                        <label>Categories (comma separated)</label>
                        <input type="text" name="categories" value="<?php echo htmlspecialchars($categories); ?>" placeholder="e.g. News, Tech">
                    </div>
                    <div class="col">
                        <label>Tags (comma separated)</label>
                        <input type="text" name="tags" value="<?php echo htmlspecialchars($tags); ?>" placeholder="e.g. php, tutorial">
                    </div>
                </div>
            </div>
            <?php
        },
        'post_saved_pre' => function(&$post_data) {
            $post_data['categories'] = sanitize($_POST['categories'] ?? '');
            $post_data['tags'] = sanitize($_POST['tags'] ?? '');
        },
        'post_meta_after' => function($post) {
            if (!empty($post['categories'])) {
                echo ' | Categories: ' . htmlspecialchars($post['categories']);
            }
            if (!empty($post['tags'])) {
                echo ' | Tags: ' . htmlspecialchars($post['tags']);
            }
        }
    ]
];
