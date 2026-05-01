<?php
/**
 * Categories & Tags for AnonBlog
 */
return [
    'name' => 'AnonTax: Categories & Tags',
    'description' => 'Organize your posts with categories and tags.',
    'version' => '1.0.0',
    'author' => 'AnonBlog Team',
    'hooks' => [
        'post_save_pre' => function($post_data) {
            $post_data['category'] = $_POST['category'] ?? 'Uncategorized';
            $post_data['tags'] = array_map('trim', explode(',', $_POST['tags'] ?? ''));
            return $post_data;
        },
        'post_form_after' => function($post) {
            $cat = $post['category'] ?? 'Uncategorized';
            $tags = isset($post['tags']) ? implode(', ', $post['tags']) : '';
            return "
            <div class='form-group'>
                <label>Category</label>
                <input type='text' name='category' value='" . htmlspecialchars($cat) . "'>
            </div>
            <div class='form-group'>
                <label>Tags (comma separated)</label>
                <input type='text' name='tags' value='" . htmlspecialchars($tags) . "'>
            </div>";
        }
    ]
];
