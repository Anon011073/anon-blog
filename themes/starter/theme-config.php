<?php
/**
 * Starter Theme Configuration & Options
 * Use this file to define custom options for your theme.
 */

return [
    'name' => 'Starter Theme',
    'author' => 'AnonBlog Team',
    'version' => '1.0.0',
    'options' => [
        [
            'name' => 'heading_font',
            'label' => 'Heading Font',
            'type' => 'font',
            'default' => 'Montserrat'
        ],
        [
            'name' => 'accent_color',
            'label' => 'Accent Color',
            'type' => 'color',
            'default' => '#e91e63'
        ],
        [
            'name' => 'show_sidebar',
            'label' => 'Show Sidebar',
            'type' => 'checkbox',
            'default' => true
        ],
        /*
        // Example of a select option (commented out)
        [
            'name' => 'layout_style',
            'label' => 'Layout Style',
            'type' => 'select',
            'options' => ['boxed' => 'Boxed', 'wide' => 'Full Width'],
            'default' => 'wide'
        ],
        */
    ]
];
