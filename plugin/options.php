<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_collection_options {
    
    /**
     * ...
     */
    public static function options() {
        $return = [
            'plugin_name' => 'Collection',
            'add_body_class' => 'false',
            'inline_styles' => '',
            'capability_type' => 'post',
            'flush_rewrite' => 'false',
            'post_types' => 'false',
            'post_type_deregister' => '',
            'metaboxs' => 'false',
            'post_type_supports' => 'title, editor, author, thumbnail, excerpt, comments',
            'delete_options' => 'false',
            'grouper' => 'Grouper'
        ];
        return $return;
    }
    
    /**
     * ...
     */
    public static function get_options() {
        $return = [];
        foreach (self::options() as $k => $v):
            $return['slwsu_collection_' . $k] = get_option('slwsu_collection_' . $k, $v);
        endforeach;
        unset($k, $v);

        return $return;
    }
    
    /**
     * ...
     */
    public static function get_transient() {
        $return = get_transient('slwsu_collection_options');
        return $return;
    }
    
    /**
     * ...
     */
    public static function set_transient($aOptions) {
        set_transient('slwsu_collection_options', $aOptions, '');
    }

}
