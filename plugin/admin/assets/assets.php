<?php

/**
 * @package Collection
 * version 0.5
 */
defined('ABSPATH') or exit();

class slwsu_collection_front_asset {

    public function __construct() {
        $this->_init();
    }

    public function _init() {
        add_action('wp_enqueue_scripts', array($this, 'register_css'));
        add_action('wp_enqueue_scripts', array($this, 'register_js'));
    }

    public function register_css() {
        wp_register_style('custom-style', plugins_url('/assets/front.css', __FILE__));
        wp_enqueue_style('custom-style');
    }

    public function register_js() {
        wp_register_script('custom-script', plugins_url('/assets/front.js', __FILE__));
        wp_enqueue_script('custom-script');
    }

}
