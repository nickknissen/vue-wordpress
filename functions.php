<?php
/**
 * Based on https://github.com/roots/sage/blob/fb19145d423668b3ce5d17ca27a5e15c84ab8f34/functions.php
 */

/**
 * Require Composer autoloader if installed on it's own
 */
if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    require_once $composer;
}

/**
 * Here's what's happening with these hooks:
 * 1. WordPress detects theme in themes/vue-wordpress
 * 2. When we activate, we tell WordPress that the theme is actually in themes/vue-wordpress/templates
 * 3. When we call get_template_directory() or get_template_directory_uri(), we point it back to themes/vue-wordpress
 *
 * We do this so that the Template Hierarchy will look in themes/vue-wordpress/templates for core WordPress themes
 * But functions.php, style.css, and index.php are all still located in themes/vue-wordpress
 *
 * get_template_directory()   -> /srv/www/example.com/current/web/app/themes/vue-wordpress
 * get_stylesheet_directory() -> /srv/www/example.com/current/web/app/themes/vue-wordpress
 * locate_template()
 * ├── STYLESHEETPATH         -> /srv/www/example.com/current/web/app/themes/vue-wordpress
 * └── TEMPLATEPATH           -> /srv/www/example.com/current/web/app/themes/vue-wordpress/templates
 */
add_filter('template', function ($stylesheet) {
    return dirname($stylesheet);
});
add_action('after_switch_theme', function () {
    $stylesheet = get_option('template');
    if (basename($stylesheet) !== 'server') {
      // TODO: Check if its possible to use nested level folder so that we can use (src/server/templates)
      // if not do we even need this folder when it's a SPA? or how do we seperate different kind of pages?
      // maybe by using post_metadata
        update_option('template', $stylesheet . '/templates');
    }
});

/**
 * Includes
 *
 * The $includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 */
$includes = [
    'src/server/setup.php',
];
array_walk($includes, function ($file) {
    if (!locate_template($file, true, true)) {
        trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
    }
});
