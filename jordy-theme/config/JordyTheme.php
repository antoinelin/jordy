<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('JordyTheme')) {

    class JordyTheme {
        /**
         * Add WordPress actions after theme is well setup
         * @param function
         */
        private function _actionAfterSetup($function) {
            add_action('after_setup_theme', function() use ($function) {
                $function();
            });
        }

        /**
         * Require ACFs and CPTs
         */
        private function _autoloadFieldsAndCPTs() {
            foreach (glob(get_template_directory() . "/{models,custom-fields}/*.php", GLOB_BRACE) as $filename) {
                if(file_exists($filename) && is_readable($filename)) {
                    require_once $filename;
                }
            }
        }

        /**
         * Clean wp_head
         */
        private function _clean() {
            remove_action('wp_head', 'rsd_link'); // remove really simple discovery link
            remove_action('wp_head', 'wp_generator'); // remove wordpress version
            remove_action('wp_head', 'feed_links', 2); // remove rss feed links (make sure you add them in yourself if youre using feedblitz or an rss service)
            remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links
            remove_action('wp_head', 'index_rel_link'); // remove link to index page
            remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)
            remove_action('wp_head', 'start_post_rel_link', 10, 0); // remove random post link
            remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
            remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // remove the next and previous post links
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
            remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
        }

        public function __construct() {
            $this->_clean();

            // Autoload ACF fiels and custom post types
            $this->_autoloadFieldsAndCPTs();

            // Add WordPress theme support
            $this->addSupport('title-tag')
                ->addSupport('custom-logo')
                ->addSupport('custom-logo')
                ->addSupport('post-formats')
                ->addSupport('post-thumbnails')
                ->addSupport('menus')
                ->addSupport('customize-selective-refresh-widgets')
                ->addSupport('html5', [
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption'
                ]);

            // Enable the option show & edit in rest
            $this->addFilter('acf/rest_api/field_settings/show_in_rest', '__return_true');
            $this->addFilter('acf/rest_api/field_settings/edit_in_rest', '__return_true');

            // Allow ACF relations to be queryables recursively
             $post_types = get_post_types(array(
                'public'       => true,
                'show_in_rest' => true,
                '_builtin'     => false
             ), 'names', 'and');
             
             foreach ( $post_types  as $post_type ) {
                $this->addFilter("acf/rest_api/{$post_type}/get_fields", function($data) {
                    if (!empty($data)) {
                        array_walk_recursive($data, array($this, 'get_fields_recursive'));
                    }
    
                    return $data;
                });
             }
        }

        /**
         * Add feature to be supported by WordPress
         * @param feature
         * @param options
         * 
         * @return this
         */
        public function addSupport($feature, $options = null) {
            $this->_actionAfterSetup(function() use ($feature, $options) {
                if ($options) {
                    add_theme_support($feature, $options);
                } else {
                    add_theme_support($feature);
                }
            });

            return $this;
        }

        /**
         * Add filter to be added on WordPress
         * @param tag
         * @param function_to_add
         * @param priority
         * @param accepted_args
         * 
         * @return this
         */
        public function addFilter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
            $this->_actionAfterSetup(function() use ($tag, $function_to_add, $priority, $accepted_args) {
                add_filter($tag, $function_to_add, $priority, $accepted_args);
            });

            return $this;
        }

        /**
         * Recursively get ACF fields from custom relation fields
         * @param item
         * 
         * @return this
         */
        public function get_fields_recursive($item) {
            if (is_object($item)) {
                $item->acf = array();
                if ( $fields = get_fields($item)) {
                    $item->acf = $fields;
                    array_walk_recursive($item->acf, array($this, 'get_fields_recursive'));
                }
            }
        }
    }
}