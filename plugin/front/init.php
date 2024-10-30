<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_collection_front_init {

    public $post_types;

    /**
     * ...
     */
    public function __construct() {
        $this->post_types = get_option('slwsu_collection_post_types', 'false');
        $this->_init();
    }

    /**
     * ...
     */
    public function _init() {
        
        add_action('wp_head', array($this, 'add_inline_styles'));
        
        if ('true' === get_option('slwsu_collection_add_body_class', 'false')):
            add_filter('body_class', array($this, 'add_body_class'));
        endif;

        $this->shortcode();
    }

    /**
     * ...
     */
    public function shortcode() {
        include_once plugin_dir_path(__FILE__) . 'shortcode.php';
        new slwsu_collection_shortcode();
    }

    /**
     * ...
     */
    public function add_body_class($classes) {
        global $post;
        $collectionClasses = array('collection');

        if (is_archive()):
            $collectionClasses[] = 'archive-' . $post->post_type;
        endif;

        if (is_single()):
            $collectionClasses[] = 'single-' . $post->post_type;
        endif;

        foreach ($this->post_types as $cpt) {
            if ($post->post_type === $cpt['name']):
                $classes = array_merge($collectionClasses, $classes);
            endif;
        }

        return $classes;
    }

    /**
     * ...
     */
    public static function add_inline_styles() {
        $ccollection_css = get_option('slwsu_collection_inline_styles', '');
        echo "<style>\n\t" . $ccollection_css . "\n</style>";
    }

}
