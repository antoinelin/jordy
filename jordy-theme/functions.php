<?php

require_once get_template_directory() . '/config/JordyTheme.php';

// Timezone
date_default_timezone_set("Europe/Paris");

// Theme instance
$theme = new JordyTheme;

// Hide admin bar
$theme->addFilter('show_admin_bar', '__return_false');

// Enable the option show & edit in rest
$theme->addFilter('acf/rest_api/field_settings/show_in_rest', '__return_true');
$theme->addFilter('acf/rest_api/field_settings/edit_in_rest', '__return_true');

// Allow ACF relations to be queryables recursively
$theme->addFilter('acf/rest_api/event/get_fields', function($data) {
    if ( ! empty($data) ) {
        array_walk_recursive($data, array($theme, 'get_fields_recursive'));
    }

    return $data;
});

// Allow SVG uploads
$theme->addFilter('upload_mimes', function($mime_types) {
    $mime_types['svg'] = 'image/svg+xml';

    return $mime_types;
}, 1, 1);
