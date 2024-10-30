<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_collection_plugin_init {

    public $post_types;
    public $del_post_types;

    /**
     * ...
     */
    public function __construct() {
        $this->post_types = get_option('slwsu_collection_post_types', 'false');
        $this->del_post_types = get_option('slwsu_collection_post_type_deregister', '');
        $this->_init();
    }

    /**
     * ...
     */
    private function _init() {
        if ('' !== $this->del_post_types):
            add_action('init', array($this, 'del_post_types'), 20);
        endif;
        
        $this->add_post_types();

        if (is_admin()):
            $this->admin_init();
        else:
            $this->front_init();
        endif;
    }

    /**
     * ...
     */
    private function add_post_types() {
        include_once plugin_dir_path(__FILE__) . 'post_types.php';
        include_once plugin_dir_path(__FILE__) . 'taxonomy.php';

        if ('false' !== $this->post_types && '' !== $this->post_types):
            foreach ($this->post_types as $cpt):
            
                ${$cpt['name']} = new slwsu_post_types($cpt);
                
                if (isset($cpt['category'])):
                    ${$cpt['name'] . '_category'} = new slwsu_taxonomy($cpt['name'], true);
                endif;
                
                if (isset($cpt['tags'])):
                    ${$cpt['name'] . '_tag'} = new slwsu_taxonomy($cpt['name'], false);
                endif;

            endforeach;
        endif;
    }

    /**
     * ...
     */
    public function del_post_types() {
        global $wp_post_types;
        $aCpt = explode(',', $this->del_post_types);
        if (is_array($aCpt)):
            foreach ($aCpt as $post_type) {
                $post_type = trim($post_type);
                if (isset($wp_post_types[$post_type]) && '' !== $post_type) {
                    unset($wp_post_types[$post_type]);
                }
            }
        else:
            $post_type = trim($this->del_post_types);
            unset($wp_post_types[$post_type]);
        endif;
    }

    /**
     * ...
     */
    private function admin_init() {
        include_once plugin_dir_path(__FILE__) . 'admin/init.php';
        new slwsu_collection_admin_init();
    }

    /**
     * ...
     */
    private function front_init() {
        include_once plugin_dir_path(__FILE__) . 'front/init.php';
        new slwsu_collection_front_init();
    }

}
