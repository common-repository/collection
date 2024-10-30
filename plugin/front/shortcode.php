<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_collection_shortcode {

    /**
     * ...
     */
    public function __construct() {
        add_shortcode('collection_field', array($this, 'get_field'));
        add_shortcode('collection_table', array($this, 'get_table'));
    }

    /**
     * ...
     */
    public function get_field($atts) {
        global $post;
        extract(shortcode_atts(array('box' => 'none', 'field' => 'none',), $atts));
        if (!$box || !$field):
            return;
        endif;

        $boxId = slwsu_collection_shortcode::str_to_id($box, '_');
        $fieldId = slwsu_collection_shortcode::str_to_id($field, '_');

        $class = preg_replace('#_#', '-', $fieldId);
        $metaName = '_' . $post->post_type . '_' . $boxId . '_' . $fieldId;
        $data = get_post_meta($post->ID, $metaName, true);

        if ($data) {
            return '<span class="collection-field ' . $post->post_type . ' ' . $class . '" >' . $data . '</span>';
        }
    }

    /**
     * ...
     */
    public function get_table($atts) {
        global $post;
        extract(shortcode_atts(array('box' => 'none', 'fields' => 'none',), $atts));
        if (!$box || !$fields):
            return;
        endif;

        $metaboxId = slwsu_collection_shortcode::str_to_id($box, '_');
        $aDatas = array_map('trim', explode(',', $fields));
        $class = preg_replace('#_#', '-', $metaboxId);
        $html = '';

        $html .= '<table class="collection-table ' . $post->post_type . ' ' . $class . '"><thead><tr><th colspan="2">' . $box . '</th></tr></thead><tbody>';

        foreach ($aDatas as $champ) {
            $champId = slwsu_collection_shortcode::str_to_id($champ, '_');
            $metaName = '_' . $post->post_type . '_' . $metaboxId . '_' . $champId;
            $value = get_post_meta($post->ID, $metaName, true);

            $html .= '<tr><td>' . $champ . '</td><td>' . $value . '</td></tr>';
        }

        $html .= '<tbody></table>';

        return $html;
    }

    /**
     * ...
     */
    public static function str_to_id($str, $sep = null, $charset = 'utf-8') {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        if (null !== $sep):
            $str = preg_replace('# #', $sep, $str); // On remplace les espaces
        endif;


        $str = strtolower($str);

        return $str;
    }

}
