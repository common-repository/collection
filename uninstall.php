<?php

/**
 * @package Collection
 * version 0.5
 */

defined('WP_UNINSTALL_PLUGIN') or exit();

include_once plugin_dir_path(__FILE__) . 'plugin/options.php';
$aOptions = slwsu_collection_options::get_options();

foreach ($aOptions as $k => $v):
    delete_option($k);
endforeach;

unset($k, $v);

delete_transient('slwsu_collection_options');