<?php

/**
 * @package Collection
 * version 0.5
 */
defined('ABSPATH') or exit();

class slwsu_metabox {

    public $post_type;
    public $metabox_name;
    public $metabox_id;
    public $inputs;

    /**
     * ...
     */
    public function __construct($cpt, $metabox, $inputs) {
        $this->post_type = $cpt;
        $this->metabox_name = $metabox;
        $this->metabox_id = $this->str_to_id('_', $metabox);
        // $this->metabox_id = $this->get_id($metabox);
        $this->inputs = $inputs;
        // var_dump($this->post_type, $this->inputs);
        $this->_init();
    }

    /**
     * ...
     */
    public function _init() {
        add_action('add_meta_boxes', array($this, 'init_metabox'), 10, 2);
        add_action('save_post', array($this, 'save_metabox'));
    }

    /**
     * ...
     */
    public function init_metabox() {
        $id = 'collection_' . $this->post_type . '_' . $this->metabox_id;
        add_meta_box($id, $this->metabox_name, array($this, 'get_inputs'), $this->post_type, 'normal', 'default');
    }

    /**
     * ...
     */
    public function get_inputs() {
        global $post;

        foreach ($this->inputs as $k => $input):
            $metaName = '_' . $this->post_type . '_' . $this->metabox_id . '_' . $this->str_to_id('_', $input['name']);
            $metaValue = get_post_meta($post->ID, $metaName, true);
            /* ... */
            $aInputSimple = ['text', 'color', 'email', 'url', 'date', 'month', 'week', 'time', 'number'];
            if (in_array($input['type'], $aInputSimple)):
                $this->input_text($input['name'], $input, $metaValue);
            endif;
            /* ... */
            $aInputPattern = ['tel'];
            if (in_array($input['type'], $aInputPattern)):
                $this->input_pattern($input['name'], $input, $metaValue);
            endif;
            /* ... */
            $aInputTextArea = ['textarea'];
            if (in_array($input['type'], $aInputTextArea)):
                $this->input_textarea($input['name'], $input, $metaValue);
            endif;


        endforeach;
    }

    /**
     * ...
     */
    public function input_text($optionName, $input, $metaValue) {
        $inputId = $this->str_to_id('_', $this->post_type . '_' . $this->metabox_id . '_' . $optionName);
        echo '<label for="' . $inputId . '">' . $optionName . '</label><br />';
        echo '<input style="width:100%;" id="' . $inputId . '" type="' . $input['type'] . '" name="' . $inputId . '" value="' . $metaValue . '" /> <br />';
    }

    /**
     * ...
     */
    public function input_textarea($optionName, $input, $metaValue) {
        $inputId = $this->str_to_id('_', $this->post_type . '_' . $this->metabox_id . '_' . $optionName);
        echo '<label for="' . $inputId . '">' . $optionName . '</label><br />';
        echo '<textarea style="width:100%;" id="' . $inputId . '" style="width: 280px;" name="' . $inputId . '">' . $metaValue . '</textarea>';
    }

    /**
     * ...
     */
    public function input_pattern($optionName, $input, $metaValue) {
        $inputId = $this->str_to_id('_', $this->post_type . '_' . $this->metabox_id . '_' . $optionName);

        $pattern = [
            'tel' => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
        ];

        echo '<label for="' . $inputId . '">' . $optionName . '</label><br />';
        echo '<input style="width:100%;" id="' . $inputId . '" type="' . $input['type'] . '" pattern="' . $pattern[$input['type']] . '" name="' . $inputId . '" value="' . $metaValue . '" /> <br />';
    }

    /**
     * ...
     */
    public static function save_metabox($post_id) {
        global $post;

        $collectionMetaboxs = get_option('slwsu_collection_metaboxs', 'false');
        foreach ($collectionMetaboxs as $cpt => $v):
            foreach ($v as $k => $val):
                foreach ($val as $k => $v):
                    if (is_array($v)):
                        foreach ($v as $key => $val):
                            foreach ($val as $k => $v):
                                $champ = slwsu_metabox::str_to_id('_', $cpt . '_' . $key . '_' . $v['name']);
                                if (isset($_POST[$champ])):
                                    switch ($v[1]) {
                                        case 'textarea':
                                            $input = esc_textarea($_POST[$champ]);
                                            break;
                                        case 'tel':
                                        case 'color':
                                            $input = esc_html($_POST[$champ]);
                                            break;
                                        case 'email':
                                            $input = is_email($_POST[$champ]);
                                            break;
                                        case 'url':
                                            $input = esc_url($_POST[$champ]);
                                            break;
                                        case 'text':
                                        default:
                                            $input = sanitize_text_field($_POST[$champ]);
                                    }
                                    update_post_meta($post_id, '_' . $champ, $input);
                                endif;
                            endforeach;
                        endforeach;
                    endif;
                endforeach;
            endforeach;
        endforeach;
    }

    /**
     * ...
     */
    public static function str_to_id($sep, $str, $charset = 'utf-8') {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        $str = preg_replace('# #', $sep, $str);

        $str = strtolower($str);

        return $str;
    }

}
