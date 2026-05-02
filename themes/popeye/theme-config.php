<?php
/**
 * Popeye Theme Config
 */
return [
    'name' => 'Popeye',
    'description' => 'A clean, single-column focused theme for minimalists.',
    'author' => 'AnonBlog Team',
    'version' => '1.0.1',
    'options' => [
        [
            'name' => 'site_width',
            'label' => 'Site Content Width (px)',
            'type' => 'number',
            'default' => 650
        ],
        [
            'name' => 'header_blur',
            'label' => 'Enable Glassmorphism Header',
            'type' => 'checkbox',
            'default' => true
        ],
        [
            'name' => 'widget_pos',
            'label' => 'Widget Area Placement',
            'type' => 'select',
            'options' => [
                'top' => 'Above Content',
                'bottom' => 'Below Content',
                'both' => 'Top and Bottom'
            ],
            'default' => 'bottom'
        ],
        [
            'name' => 'show_tax_meta',
            'label' => 'Show Categories & Tags in Meta',
            'type' => 'checkbox',
            'default' => true
        ],
        [
            'name' => 'featured_img_style',
            'label' => 'Featured Image Style',
            'type' => 'select',
            'options' => [
                'full' => 'Above Title (Full)',
                'thumb' => 'Side-by-side (Thumbnail)'
            ],
            'default' => 'full'
        ],
        [
            'name' => 'body_font',
            'label' => 'Body Font',
            'type' => 'font',
            'default' => 'Inter'
        ],
        [
            'name' => 'title_font',
            'label' => 'Title Font',
            'type' => 'font',
            'default' => 'Playfair Display'
        ],
        [
            'name' => 'primary_color',
            'label' => 'Primary Brand Color',
            'type' => 'color',
            'default' => '#000000'
        ]
    ]
];
