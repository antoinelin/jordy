<?php

require_once get_template_directory() . '/config/JordyTheme.php';

// Timezone
date_default_timezone_set("Europe/Paris");

// Theme instance
$theme = new JordyTheme;

// Hide admin bar
$theme->addFilter('show_admin_bar', '__return_false');

// Allow SVG uploads
$theme->addFilter('upload_mimes', function($mime_types) {
    $mime_types['svg'] = 'image/svg+xml';

    return $mime_types;
}, 1, 1);
