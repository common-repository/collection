<?php

/**
 * @package Collection
 * version 0.5
 */

class slwsu_collection_admin_assets {

    /**
     * ...
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_style'));
    }

    public function enqueue_style() {
        // wp_register_style('collection_admin_css', plugins_url('/assets/admin.css', __FILE__), false);
        // wp_register_script( 'custom_jquery', plugins_url('/js/custom-jquery.js', __FILE__), array('jquery'), '2.5.1' );
        // wp_enqueue_style('collection_admin_css');
        // wp_enqueue_script('custom_jquery');
    }

}
