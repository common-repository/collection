<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_post_types {

    public $name;
    public $plural;
    public $singular;
    public $slug;
    public $description;
    public $capability;
    public $supports = [];
    public $flush_rewrite = false;

    /**
     * 
     */
    public function __construct($aCpts) {

        $this->name = $aCpts['name'];
        $this->plural = $aCpts['plural'];
        $this->singular = $aCpts['singular'];
        $this->slug = $aCpts['slug'];
        $this->description = $aCpts['description'];
        $this->capability = get_option('slwsu_collection_capability_type', 'post');
        $this->supports = array_map('trim', explode(',', get_option('slwsu_collection_post_type_supports')));

        $post_types = get_option('slwsu_collection_post_types', 'false');
        $flush = get_option('slwsu_collection_flush_rewrite', 'false');
        
        $md5Str = 'false';

        if (is_array($post_types)):
            $md5Str = '';
            foreach ($post_types as $cpt):
                $md5Str .= $cpt['name'];
            endforeach;
        endif;

        if (md5($md5Str) !== $flush):
            $this->flush_rewrite = true;
        endif;

        // Init
        $this->_init();
    }

    /**
     * 
     */
    public function _init() {
        add_action('init', array($this, 'register_post_types'), 25);

        if (true === $this->flush_rewrite):
            add_action('init', array($this, 'flush_rewrite_rules'), 30);
        endif;
    }

    /**
     * ...
     */
    public function register_post_types() {
        register_post_type($this->name, $this->args());
    }

    /**
     * ...
     */
    private function args() {

        $args = array(
            'labels' => $this->labels(),
            'description' => $this->description,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $this->slug, 'with_front' => true),
            'capability_type' => $this->capability,
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 4,
            'supports' => $this->supports,
            // Rest api : http://www.geekpress.fr/tuto-wp-api-rest-comment-ajouter-vos-custom-post-types-a-lapi-rest-wp/
            'show_in_rest' => true,
            'rest_base' => $this->slug,
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        );

        return $args;
    }

    /**
     * ...
     */
    private function labels() {

        $labels = array(
            'name' => $this->plural,
            'singular_name' => $this->singular,
            'menu_name' => $this->plural,
            'name_admin_bar' => $this->singular,
        );

        return $labels;
    }

    /**
     * ...
     */
    public function flush_rewrite_rules() {
        flush_rewrite_rules();
    }


}
