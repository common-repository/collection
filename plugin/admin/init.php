<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_collection_admin_init {

    /**
     * ...
     */
    public function __construct() {
        $this->add_metabox();
        $this->admin_page();
    }

    /**
     * ...
     */
    public function add_metabox() {
        include_once plugin_dir_path(__FILE__) . 'metabox.php';

        $collectionMetaboxs = get_option('slwsu_collection_metaboxs', 'false');

        if (is_array($collectionMetaboxs)):
            foreach ($collectionMetaboxs as $cpt => $v):
                if (is_array($v)):
                    foreach ($v as $k => $val):
                        foreach ($val as $k => $v):
                            if (is_array($v)):
                                foreach ($v as $key => $val):
                                    ${$cpt} = new slwsu_metabox($cpt, $key, $val);
                                endforeach;
                            endif;
                        endforeach;
                    endforeach;
                endif;
            endforeach;
        endif;
    }

    /**
     * ...
     */
    public function admin_page() {
        include_once plugin_dir_path(__FILE__) . 'panel.php';
        new slwsu_collection_admin_panel();

        include_once plugin_dir_path(__FILE__) . 'assets.php';
        new slwsu_collection_admin_assets();
    }

}