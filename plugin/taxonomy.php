<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_taxonomy {

    public $post_type;
    public $hierarchical;
    public $name;
    public $slug;

    public function __construct($cpt, $hierarchical) {
        $this->post_type = $cpt;
        $this->hierarchical = $hierarchical;

        if (true === $this->hierarchical):
            $this->name = $this->post_type . '_category';
            $this->slug = $this->post_type . '-category';
        else:
            $this->name = $this->post_type . '_tag';
            $this->slug = $this->post_type . '-tag';
        endif;

        $this->_init();
    }

    public function _init() {
        add_action('init', array($this, 'register_taxonomy'));
    }

    public function register_taxonomy() {
        register_taxonomy($this->name, array($this->post_type), $this->args());
    }

    private function labels() {
        if (true === $this->hierarchical):
            $labels = array(
                'name' => _x('Categories', 'taxonomy general name', 'coon'),
                'singular_name' => _x('Category', 'taxonomy singular name', 'coon'),
                'search_items' => __('Search Categories', 'coon'),
                'all_items' => __('All Categories', 'coon'),
                'parent_item' => __('Parent Category', 'coon'),
                'parent_item_colon' => __('Parent Category:', 'coon'),
                'edit_item' => __('Edit Category', 'coon'),
                'update_item' => __('Update Category', 'coon'),
                'add_new_item' => __('Add New Category', 'coon'),
                'new_item_name' => __('New Category Name', 'coon'),
                'menu_name' => __('Categories', 'coon'),
            );
        else:
            $labels = array(
                'name' => _x('Tags', 'taxonomy general name', 'coon'),
                'singular_name' => _x('Tag', 'taxonomy singular name', 'coon'),
                'search_items' => __('Search Tags', 'coon'),
                'all_items' => __('All Tags', 'coon'),
                'parent_item' => __('Parent Tag', 'coon'),
                'parent_item_colon' => __('Parent Tag:', 'coon'),
                'edit_item' => __('Edit Tag', 'coon'),
                'update_item' => __('Update Tag', 'coon'),
                'add_new_item' => __('Add New Tag', 'coon'),
                'new_item_name' => __('New Tag Name', 'coon'),
                'menu_name' => __('Tags', 'coon'),
            );
        endif;

        return $labels;
    }

    private function args() {
        $args = array(
            'hierarchical' => $this->hierarchical,
            'labels' => $this->labels(),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $this->slug),
        );

        return $args;
    }

}
