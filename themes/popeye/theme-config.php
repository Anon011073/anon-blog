<?php
/**
 * Popeye Theme Config
 */
return [
    'name' => 'Popeye',
    'description' => 'A clean, single-column focused theme for minimalists.',
    'author' => 'AnonBlog Team',
    'version' => '1.0.0',
    'options' => [
        'site_width' => [
            'label' => 'Site Content Width (px)',
            'type' => 'number',
            'default' => 650
        ],
        'header_blur' => [
            'label' => 'Enable Glassmorphism Header',
            'type' => 'checkbox',
            'default' => true
        ],
        'widget_pos' => [
            'label' => 'Widget Area Placement',
            'type' => 'select',
            'options' => [
                'top' => 'Above Content',
                'bottom' => 'Below Content',
                'both' => 'Top and Bottom'
            ],
            'default' => 'bottom'
        ],
        'show_tax_meta' => [
            'label' => 'Show Categories & Tags in Meta',
            'type' => 'checkbox',
            'default' => true
        ],
        'featured_img_style' => [
            'label' => 'Featured Image Style',
            'type' => 'select',
            'options' => [
                'full' => 'Above Title (Full)',
                'thumb' => 'Side-by-side (Thumbnail)'
            ],
            'default' => 'full'
        ],
        'body_font' => [
            'label' => 'Body Font',
            'type' => 'font',
            'default' => 'Inter'
        ],
        'title_font' => [
            'label' => 'Title Font',
            'type' => 'font',
            'default' => 'Playfair Display'
        ],
        'primary_color' => [
            'label' => 'Primary Brand Color',
            'type' => 'color',
            'default' => '#000000'
        ]
    ]
];
