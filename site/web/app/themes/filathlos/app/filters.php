<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

add_filter('wc_get_template_part', function ($template, $slug, $name) {

    $bladeTemplate = false;

    // Look in yourtheme/slug-name.blade.php and yourtheme/woocommerce/slug-name.blade.php
    if ( $name && ! WC_TEMPLATE_DEBUG_MODE ) {
        $bladeTemplate = locate_template( array( "{$slug}-{$name}.blade.php", WC()->template_path() . "{$slug}-{$name}.blade.php" ) );
    }

    // If template file doesn't exist, look in yourtheme/slug.blade.php and yourtheme/woocommerce/slug.blade.php
    if ( ! $template && ! WC_TEMPLATE_DEBUG_MODE ) {
        $bladeTemplate = locate_template( array( "{$slug}.blade.php", WC()->template_path() . "{$slug}.blade.php" ) );
    }

    if ($bladeTemplate) {
        echo template($bladeTemplate);

        // Return a blank file to make WooCommerce happy
        return get_theme_file_path('index.php');
    }

    return $template;
}, PHP_INT_MAX, 3);


add_filter('wc_get_template', function($located, $template_name, $args, $template_path, $default_path) {
  
    $bladeTemplateName = str_replace('.php', '.blade.php', $template_name);
    $bladeTemplate = locate_template( array($bladeTemplateName, WC()->template_path() . $bladeTemplateName ) );

    if ($bladeTemplate) {
        return template_path($bladeTemplate, $args);
    }

    return $located;
}, PHP_INT_MAX, 5);

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", function ($templates) {
        return collect($templates)->flatMap(function ($template) {
            $transforms = [
                '%^/?(resources[\\/]views)?[\\/]?%' => '',
                '%(\.blade)?(\.php)?$%' => ''
            ];
            $normalizedTemplate = preg_replace(array_keys($transforms), array_values($transforms), $template);
            return ["{$normalizedTemplate}.blade.php", "{$normalizedTemplate}.php"];
        })->toArray();
    });
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    echo template($template, $data);
    // Return a blank file to make WordPress happy
    return get_theme_file_path('index.php');
}, PHP_INT_MAX);

/**
 * Tell WordPress how to find the compiled path of comments.blade.php
 */
add_filter('comments_template', 'App\\template_path');
