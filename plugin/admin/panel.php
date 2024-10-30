<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

include_once plugin_dir_path(__FILE__) . 'form.php';

class slwsu_collection_admin_panel {

    public function __construct() {
        $this->_init();
    }

    private function _init() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'admin_settings'));
        add_action('admin_head', array($this, 'admin_css'));
        add_action('admin_head', array($this, 'admin_js'));
    }

    public function admin_menu() {
        global $GROUPER_COLLECTION;
        $nomCommercial = get_option('slwsu_collection_plugin_name', 'Collection');
        if (is_object($GROUPER_COLLECTION)):
            // Grouper
            $GROUPER_COLLECTION->add_admin_menu();
            add_submenu_page($GROUPER_COLLECTION->grp_id, $nomCommercial, $nomCommercial, 'manage_options', 'collection', array($this, 'admin_page'));
        else:
            add_menu_page($nomCommercial, $nomCommercial, 'activate_plugins', 'collection', array($this, 'admin_page'));
        endif;
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <div id="maj" class="notice notice-warning is-dismissible" style="display:none;">
                <p><?php echo __('Beware, values have changed: do not forget to save your changes.', 'coon'); ?></p>
            </div>
            <?php
            slwsu_collection_admin_form::action();
            echo '<h1>' . get_option('slwsu_collection_plugin_name', 'Collection') . '</h1>';
            slwsu_collection_admin_form::validation();
            slwsu_collection_admin_form::message($_POST);
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'collection';
            ?>
            <h2 class = "nav-tab-wrapper">
                <a href="?page=collection&tab=collection" class="nav-tab<?php echo ('collection' === $active_tab) ? ' nav-tab-active' : ''; ?>"><?php echo __('Collections', 'coon'); ?></a>
                <a href="?page=collection&tab=metabox" class="nav-tab<?php echo ('metabox' === $active_tab) ? ' nav-tab-active' : ''; ?>"><?php echo __('Metabox', 'coon'); ?></a>
                <a href="?page=collection&tab=styles" class="nav-tab<?php echo ('styles' === $active_tab) ? ' nav-tab-active' : ''; ?>"><?php echo __('Styles', 'coon'); ?></a>
                <a href="?page=collection&tab=reglages" class="nav-tab<?php echo ('reglages' === $active_tab) ? ' nav-tab-active' : ''; ?>"><?php echo __('Settings', 'coon'); ?></a>
                <a href="?page=collection&tab=grouper" class="nav-tab<?php echo ('grouper' === $active_tab) ? ' nav-tab-active' : ''; ?>">Grouper</a>
            </h2>

            <form method="post" action="options.php">
                <?php
                if ($active_tab == 'collection'):
                    do_settings_sections('slwsu_collection_plugin');
                    settings_fields('slwsu_collection_plugin');
                elseif ($active_tab == 'metabox'):
                    do_settings_sections('slwsu_collection_metabox');
                    settings_fields('slwsu_collection_metabox');
                elseif ($active_tab == 'styles'):
                    do_settings_sections('slwsu_collection_styles');
                    settings_fields('slwsu_collection_styles');
                elseif ($active_tab == 'reglages') :
                    do_settings_sections('slwsu_collection_reglages');
                    settings_fields('slwsu_collection_reglages');
                elseif ($active_tab == 'grouper') :
                    do_settings_sections('slwsu_collection_grouper');
                    settings_fields('slwsu_collection_grouper');
                else:
                    echo '<br /> Erreur !';
                endif;

                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     *
     */
    public function admin_settings() {
        /**
         * Section Collection
         */
        add_settings_section(
                'slwsu_collection_section_plugin', __('Collections', 'coon'), array($this, 'section_plugin'), 'slwsu_collection_plugin'
        );

        // ...
        add_settings_field(
                'slwsu_collection_post_types', __('Collections', 'coon'), array($this, 'post_types'), 'slwsu_collection_plugin', 'slwsu_collection_section_plugin'
        );
        register_setting(
                'slwsu_collection_plugin', 'slwsu_collection_post_types'
        );

        /**
         * Section metabox
         */
        add_settings_section(
                'slwsu_collection_section_metabox', __('Metabox', 'coon'), array($this, 'section_metabox'), 'slwsu_collection_metabox'
        );

        // ...
        add_settings_field(
                'slwsu_collection_metaboxs', __('Metabox', 'coon'), array($this, 'metabox'), 'slwsu_collection_metabox', 'slwsu_collection_section_metabox'
        );
        register_setting(
                'slwsu_collection_metabox', 'slwsu_collection_metaboxs'
        );

        /**
         * Section Css
         */
        add_settings_section(
                'slwsu_collection_section_styles', __('Styles', 'coon'), array($this, 'section_styles'), 'slwsu_collection_styles'
        );

        // ...
        add_settings_field(
                'slwsu_collection_add_body_class', __('Body Class', 'coon'), array($this, 'add_body_class'), 'slwsu_collection_styles', 'slwsu_collection_section_styles'
        );
        register_setting(
                'slwsu_collection_styles', 'slwsu_collection_add_body_class'
        );

        // ...
        add_settings_field(
                'slwsu_collection_inline_styles', __('Code css', 'coon'), array($this, 'inline_styles'), 'slwsu_collection_styles', 'slwsu_collection_section_styles'
        );
        register_setting(
                'slwsu_collection_styles', 'slwsu_collection_inline_styles'
        );

        /**
         * Section réglages
         */
        add_settings_section(
                'slwsu_collection_section_reglages', __('Settings', 'coon'), array($this, 'section_reglages'), 'slwsu_collection_reglages'
        );

        // ...
        add_settings_field(
                'slwsu_collection_plugin_name', __('Plugin name', 'coon'), array($this, 'plugin_name'), 'slwsu_collection_reglages', 'slwsu_collection_section_reglages'
        );
        register_setting(
                'slwsu_collection_reglages', 'slwsu_collection_plugin_name'
        );

        // ...
        add_settings_field(
                'slwsu_collection_capability_type', __('Capacity', 'coon'), array($this, 'capability_type'), 'slwsu_collection_reglages', 'slwsu_collection_section_reglages'
        );
        register_setting(
                'slwsu_collection_reglages', 'slwsu_collection_capability_type'
        );

        // ...
        add_settings_field(
                'slwsu_collection_post_type_supports', __('Supports', 'coon'), array($this, 'post_type_supports'), 'slwsu_collection_reglages', 'slwsu_collection_section_reglages'
        );
        register_setting(
                'slwsu_collection_reglages', 'slwsu_collection_post_type_supports'
        );

        // ...
        add_settings_field(
                'slwsu_collection_post_type_deregister', __('Deregister post types', 'coon'), array($this, 'post_type_deregister'), 'slwsu_collection_reglages', 'slwsu_collection_section_reglages'
        );
        register_setting(
                'slwsu_collection_reglages', 'slwsu_collection_post_type_deregister'
        );

        // ...
        add_settings_field(
                'slwsu_collection_delete_options', __('Delete options', 'coon'), array($this, 'delete_options'), 'slwsu_collection_reglages', 'slwsu_collection_section_reglages'
        );
        register_setting(
                'slwsu_collection_reglages', 'slwsu_collection_delete_options'
        );

        /**
         * Support GRP
         */
        if ('true' === get_option('slwsu_is_active_grouper', 'false')):
            /**
             * Section grouper
             */
            add_settings_section(
                    'slwsu_collection_section_grouper', __('Group', 'coon'), array($this, 'section_grouper'), 'slwsu_collection_grouper'
            );
            // ...
            add_settings_field(
                    'slwsu_collection_grouper', __('Plugin Group', 'coon'), array($this, 'grouper_nom'), 'slwsu_collection_grouper', 'slwsu_collection_section_grouper'
            );
            register_setting(
                    'slwsu_collection_grouper', 'slwsu_collection_grouper'
            );
        else:
            // Section NO grouper
            add_settings_section(
                    'slwsu_collection_section_grouper', __('Grouper', 'coon'), array($this, 'section_grouper_no'), 'slwsu_collection_grouper'
            );
        endif;
    }

    /**
     *
     */
    public function admin_js() {
        ?>
        <script>
            function add_metas(cpt) {
                document.getElementById('maj').style.display = 'block';

                var target = document.getElementById(cpt);
                var elm = document.createElement("div");
                elm.innerHTML = '<input name="slwsu_collection_metaboxs[' + cpt + '][metaboxs][]" type="text" placeholder="<?php echo __('Add metabox', 'coon'); ?>" required="required" />';

                target.appendChild(elm);
            }
            var count = 0;
            function add_fields(target, cpt, i, metabox, champ) {
                document.getElementById('maj').style.display = 'block';

                var objTo = document.getElementById(target);
                var elm = document.createElement("div");
                var nbChamp = (count + champ);
                elm.setAttribute('id', 'add_' + target);
                elm.innerHTML = '<input name="slwsu_collection_metaboxs[' + cpt + '][metaboxs][' + i + '][' + metabox + '][' + nbChamp + '][name]" type="text" placeholder="<?php echo __('Add field', 'coon'); ?>" required="required" />\n\
                                <select name="slwsu_collection_metaboxs[' + cpt + '][metaboxs][' + i + '][' + metabox + '][' + nbChamp + '][type]" >\n\
                                    <option value="text"><?php echo __('Short text', 'coon'); ?></option>\n\
                                    <option value="textarea"><?php echo __('Long text', 'coon'); ?></option>\n\
                                    <option value="number"><?php echo __('Number', 'coon'); ?></option>\n\
                                    <option value="tel"><?php echo __('Phone', 'coon'); ?></option>\n\
                                    <option value="color"><?php echo __('Color', 'coon'); ?></option>\n\
                                    <option value="email"><?php echo __('E-mail', 'coon'); ?></option>\n\
                                    <option value="url"><?php echo __('Url', 'coon'); ?></option>\n\
                                    <option value="date"><?php echo __('Date', 'coon'); ?></option>\n\
                                    <option value="month"><?php echo __('Month', 'coon'); ?></option>\n\
                                    <option value="week"><?php echo __('Week', 'coon'); ?></option>\n\
                                    <option value="time"><?php echo __('Hour', 'coon'); ?></option>\n\
                                </select>\n\
                                <br />';

                objTo.appendChild(elm);
                count++;
            }

            function delete_fields(id, type, index) {
                document.getElementById('maj').style.display = 'block';

                var elm = document.getElementById(id);
                elm.parentNode.removeChild(elm);

                var inputs = document.querySelectorAll(".js-meta-update");

                if ('field' === type) {
                    for (i = 0; i < inputs.length; ++i) {
                        const str = inputs[i].name;

                        const regName = /\[([0-9]+)\]\[name\]/g;
                        let nam;
                        while ((nam = regName.exec(strr)) !== null) {
                            if (nam.index === regName.lastIndex) {
                                regName.lastIndex++;
                            }
                            console.log(inputs[i].name);
                            nam.forEach(() => {
                                if (index < nam[1]) {
                                    var nam_i = nam[1] - 1;
                                    var remplacement = str.replace('[' + nam[1] + '][name]', '[' + nam_i + '][name]');
                                    inputs[i].name = remplacement;
                                }
                                console.log(inputs[i].name);
                            });
                        }
                    }

                    for (i = 0; i < inputs.length; ++i) {
                        const str = inputs[i].name;

                        const regType = /\[([0-9]+)\]\[type\]/g;
                        let typ;
                        while ((typ = regType.exec(str)) !== null) {
                            if (typ.index === regType.lastIndex) {
                                regType.lastIndex++;
                            }
                            console.log(inputs[i].name);
                            typ.forEach(() => {
                                if (index < typ[1]) {
                                    var typ_i = typ[1] - 1;
                                    var remplacement = str.replace('[' + typ[1] + '][name]', '[' + typ_i + '][name]');
                                    inputs[i].name = remplacement;
                                }
                                console.log(inputs[i].name);
                            });
                        }
                    }
                }

                if ('metabox' === type) {
                    var btnAdd = document.getElementById('add_' + id);
                    var btnDel = document.getElementById('del_' + id);
                    btnAdd.parentNode.removeChild(btnAdd);
                    btnDel.parentNode.removeChild(btnDel);

                    for (i = 0; i < inputs.length; ++i) {
                        const str = inputs[i].name;

                        const regEmplacement = /\[emplacement\]\[([0-9]+)\]/g;
                        let emp;
                        while ((emp = regEmplacement.exec(str)) !== null) {
                            if (emp.index === regEmplacement.lastIndex) {
                                regEmplacement.lastIndex++;
                            }
                            emp.forEach(() => {
                                if (index < emp[1]) {
                                    var emp_i = emp[1] - 1;
                                    var remplacement = str.replace('[emplacement][' + emp[1] + ']', '[emplacement][' + emp_i + ']');
                                    inputs[i].name = remplacement;
                                }
                            });
                        }

                        const regPriorite = /\[priorite\]\[([0-9]+)\]/g;
                        let pri;
                        while ((pri = regPriorite.exec(str)) !== null) {
                            if (pri.index === regPriorite.lastIndex) {
                                regPriorite.lastIndex++;
                            }
                            pri.forEach(() => {
                                if (index < pri[1]) {
                                    var pri_i = pri[1] - 1;
                                    var remplacement = str.replace('[priorite][' + pri[1] + ']', '[priorite][' + pri_i + ']');
                                    inputs[i].name = remplacement;
                                }
                            });
                        }

                        const regMetaboxs = /\[metaboxs\]\[([0-9]+)\]/g;
                        let met;
                        while ((met = regMetaboxs.exec(str)) !== null) {
                            if (met.index === regMetaboxs.lastIndex) {
                                regMetaboxs.lastIndex++;
                            }
                            met.forEach(() => {
                                if (index < met[1]) {
                                    var met_i = met[1] - 1;
                                    var remplacement = str.replace('[metaboxs][' + met[1] + ']', '[metaboxs][' + met_i + ']');
                                    inputs[i].name = remplacement;
                                }
                            });
                        }
                    }
                }
            }

            function HauteurAuto(e) {
                console.log(e);
                var text = document.getElementById((e));
                if (!text) {
                    return;
                }
                text.style.height = "";
                var adjustedHeight = text.clientHeight;
                adjustedHeight = Math.max(text.scrollHeight, adjustedHeight);
                if (adjustedHeight > text.clientHeight) {
                    text.style.height = adjustedHeight + "px";
                }
            }
        </script>
        <?php
    }

    /**
     * Metabox
     */
    public function section_metabox() {
        echo __('This section concerns the configuration of the metabox.', 'coon');
    }

    public function metabox() {
        $post_types = get_option('slwsu_collection_post_types', 'false');
        $collectionMetaboxs = get_option('slwsu_collection_metaboxs', 'false');

        $md5Str = 'false';

        if (is_array($post_types)):
            $md5Str = '';
            foreach ($post_types as $cpt):
                $md5Str .= $cpt['name'];
            endforeach;
        endif;

        $flush = get_option('slwsu_collection_flush_rewrite', 'false');
        if ($md5Str !== $flush):
            update_option('slwsu_collection_flush_rewrite', md5($md5Str));
        endif;
        ?>
        <div class="collection">
            <?php
            if (is_array($post_types)):

                //...
                $cpt_i = 0;
                foreach ($post_types as $post_type):
                    $cptName = $post_type['name'];
                    // var_dump($collectionMetaboxs[$cptName]);
                    // var_dump($collectionMetaboxs[$cptName]['emplacement']);
                    ?>
                    <div <?php echo ((count($post_types) + 1) > $cpt_i + 1) ? 'style="margin-bottom:35px;"' : ''; ?>>
                        <div id="<?php echo $cptName; ?>">
                            <div class="label" style="background-color:#e5e5e5; color:#262d39; padding:10px; margin-bottom:10px;">
                                <span class="dashicons dashicons-admin-post"></span> <?php echo $post_type['singular']; ?>
                                <input name="slwsu_collection_metaboxs[<?php echo $cptName; ?>]" type="hidden" value="<?php echo $cptName; ?>" />
                            </div>
                            <?php
                            if (isset($collectionMetaboxs[$cptName]['metaboxs'])):
                                $i = 0;
                                $j = 0;
                                foreach ($collectionMetaboxs[$cptName]['metaboxs'] as $metabox):
                                    if (is_array($metabox)):
                                        foreach ($metabox as $k => $v):
                                            $metaId = $k;
                                            $metaboxId = slwsu_collection_admin_panel::str_to_id($k, '_');
                                            ?>
                                            <div id="<?php echo $cptName; ?>_<?php echo $metaboxId; ?>" style="margin-bottom:10px">
                                                <div id="<?php echo $cptName; ?>_metas_<?php echo $metaboxId; ?>">
                                                    <span class="dashicons dashicons-list-view"></span> <?php echo $k; ?>

                                                    <?php
                                                    $v_i = count($v);
                                                    $metaFields = '';
                                                    $iii = 0;
                                                    foreach ($v as $key => $champ):
                                                        $metaFields .= $champ['name'];
                                                        if (($iii + 1) < $v_i):
                                                            $metaFields .= ', ';
                                                        endif;
                                                        $iii++;
                                                    endforeach;
                                                    ?>
                                                    - <span style="color:#0073aa">[collection_table box="<?php echo $k; ?>" fields="<?php echo $metaFields; ?>"]</span>
                                                    <input name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][metaboxs][]" type="hidden" value="<?php echo $k; ?>" /><br />
                                                    <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][emplacement][<?php echo $i; ?>]" type="hidden" value="<?php echo $collectionMetaboxs[$cptName]['emplacement'][$i]; ?>" />
                                                    <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][priorite][<?php echo $i; ?>]" type="hidden" value="<?php echo $collectionMetaboxs[$cptName]['priorite'][$i]; ?>" />
                                                    <br />
                                                    <div style="margin-bottom:15px;" id="<?php echo $cptName; ?>_champs_<?php echo $metaboxId; ?>">
                                                        <?php
                                                        foreach ($v as $key => $champ):
                                                            $champId = slwsu_collection_admin_panel::str_to_id($champ['name'], '_');
                                                            ?>
                                                            <div style="border-left: 3px solid #0073aa; margin-left:9px; padding-left:25px;" id="<?php echo $cptName; ?>_<?php echo $metaboxId; ?>_<?php echo $champId; ?>">
                                                                <?php echo __('Field', 'coon'); ?> <?php echo $j + 1; ?> <button style="background-color:#dc3232;" onclick="delete_fields('<?php echo $cptName; ?>_<?php echo $metaboxId; ?>_<?php echo $champId; ?>', 'field', <?php echo $j; ?>)">X</button>
                                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][metaboxs][<?php echo $i; ?>][<?php echo $k; ?>][<?php echo $j; ?>][name]" type="text" value="<?php echo $champ['name']; ?>" required="required"/>
                                                                <select class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][metaboxs][<?php echo $i; ?>][<?php echo $k; ?>][<?php echo $j; ?>][type]" value="<?php echo $champ['type']; ?>">
                                                                    <option value="text" <?php echo ('text' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Short text', 'coon'); ?></option>
                                                                    <option value="textarea" <?php echo ('textarea' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Long text', 'coon'); ?></option>
                                                                    <option value="number" <?php echo ('number' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Number', 'coon'); ?></option>
                                                                    <option value="tel" <?php echo ('tel' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Phone', 'coon'); ?></option>
                                                                    <option value="color" <?php echo ('color' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Color', 'coon'); ?></option>
                                                                    <option value="email" <?php echo ('mail' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('E-mail', 'coon'); ?></option>
                                                                    <option value="url" <?php echo ('url' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Url', 'coon'); ?></option>
                                                                    <option value="date" <?php echo ('date' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Date', 'coon'); ?></option>
                                                                    <option value="month" <?php echo ('month' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Month', 'coon'); ?></option>
                                                                    <option value="week" <?php echo ('week' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Week', 'coon'); ?></option>
                                                                    <option value="time" <?php echo ('time' === $champ['type']) ? 'selected="selected"' : ''; ?>><?php echo __('Hour', 'coon'); ?></option>
                                                                </select>
                                                                - <span style="color:#0073aa">[collection_field box="<?php echo $k; ?>" field="<?php echo $champ['name']; ?>"]</span>
                                                            </div>
                                                            <?php
                                                            $j++;
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        endforeach;
                                    else:
                                        $metaId = $metabox;
                                        $metaboxId = slwsu_collection_admin_panel::str_to_id($metabox, '_');
                                        ?>
                                        <div id="<?php echo $cptName; ?>_<?php echo $metaboxId; ?>">
                                            <div id="<?php echo $cptName; ?>_metas_<?php echo $metaboxId; ?>">
                                                <span style="margin-bottom:10px;" class="dashicons dashicons-admin-generic"></span> <?php echo $metabox; ?>
                                                <input name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][metaboxs][]" type="hidden" value="<?php echo $metabox; ?>" />

                                                <?php
                                                if (!isset($collectionMetaboxs[$cptName]['emplacement'][$i])):
                                                    $selected = 'normal';
                                                else:
                                                    $selected = $collectionMetaboxs[$cptName]['emplacement'][$i];
                                                endif;
                                                ?>
                                                <br />
                                                <strong><?php echo __('Location', 'coon'); ?></strong>
                                                <br />
                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][emplacement][<?php echo $i; ?>]" type="radio" value="normal" <?php echo ('normal' === $selected) ? 'checked="checked"' : ''; ?>/><?php echo __('Normal', 'coon'); ?> -
                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][emplacement][<?php echo $i; ?>]" type="radio" value="side" <?php echo ('side' === $selected) ? 'checked="checked"' : ''; ?>/><?php echo __('Side', 'coon'); ?> -
                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][emplacement][<?php echo $i; ?>]" type="radio" value="advanced" <?php echo ('advanced' === $selected) ? 'checked="checked"' : ''; ?>/><?php echo __('Advanced', 'coon'); ?>

                                                <br />
                                                <br />

                                                <?php
                                                if (!isset($collectionMetaboxs[$cptName]['priorite'][$i])):
                                                    $selected = 'default';
                                                else:
                                                    $selected = $collectionMetaboxs[$cptName]['priorite'][$i];
                                                endif;
                                                ?>
                                                <strong><?php echo __('Priority', 'coon'); ?></strong>
                                                <br />
                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][priorite][<?php echo $i; ?>]" type="radio" value="default" <?php echo ('default' === $selected) ? 'checked="checked"' : ''; ?>/> <?php echo __('Default', 'coon'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][priorite][<?php echo $i; ?>]" type="radio" value="low" <?php echo ('low' === $selected) ? 'checked="checked"' : ''; ?>/> <?php echo __('Low', 'coon'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][priorite][<?php echo $i; ?>]" type="radio" value="high" <?php echo ('high' === $selected) ? 'checked="checked"' : ''; ?>/> <?php echo __('High', 'coon'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                                <input class="js-meta-update" name="slwsu_collection_metaboxs[<?php echo $cptName; ?>][priorite][<?php echo $i; ?>]" type="radio" value="core" <?php echo ('core' === $selected) ? 'checked="checked"' : ''; ?>/> <?php echo __('Core', 'coon'); ?>

                                                <div style="margin:10px 0;" id="<?php echo $cptName; ?>_champs_<?php echo $metaboxId; ?>">
                                                    <p><span class="dashicons dashicons-warning"></span> <?php echo __('This metabox contains no field.', 'coon'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    endif;
                                    ?>
                                    <button style="margin-bottom:15px;" type="button" id="add_<?php echo $cptName; ?>_<?php echo $metaboxId; ?>" onclick="add_fields('<?php echo $cptName; ?>_champs_<?php echo $metaboxId; ?>', '<?php echo $cptName; ?>', '<?php echo $i; ?>', '<?php echo $metaId; ?>', <?php echo $j; ?>);
                                                                    return false;" ><?php echo __('Add field', 'coon'); ?></button>
                                    <button style="margin-bottom:15px; background-color:#dc3232;" type="button" id="del_<?php echo $cptName; ?>_<?php echo $metaboxId; ?>" onclick="delete_fields('<?php echo $cptName; ?>_<?php echo $metaboxId; ?>', 'metabox', '<?php echo $i; ?>');
                                                                    return false;" ><?php echo __('Remove metabox', 'coon'); ?></button>
                                    <hr style="margin-bottom:15px;"/>
                                    <?php
                                    $i++;
                                endforeach;
                            else:
                                echo '<p style="margin-bottom:10px;"><span class="dashicons dashicons-warning"></span> ' . __('This collection contains no metabox.', 'coon') . '</p>';
                            endif;
                            ?>
                        </div>
                        <button type="button" onclick="add_metas('<?php echo $cptName; ?>');
                                                return false;" ><?php echo __('Add metabox', 'coon'); ?></button>
                    </div>
                    <?php
                    $cpt_i++;
                endforeach;
            else:
                echo __('You have not created any collection yet.', 'coon');
            endif;
            ?>
        </div>
        <?php
    }

    /**
     * Styles
     */
    public function section_styles() {
        echo __('This section concerns the configuration of the css styles.', 'coon') . '<br />';
        echo __('Collection adds to the body tag the', 'coon') . ' : <strong>body class="collection archive-{name} single-{name}"</strong>. <br />';
        echo __('If you use the shortcode field by field, be aware that they are embedded in', 'coon') . ' : <strong>span class="collestion-field {collection name} {field name}"</strong>.<br />';
        echo __('If you use the shortcode table to display multiple fields, you will need to use', 'coon') . ' : <strong>table class = "collection-table {collection name} {metabox name}"</strong>.<br />';
    }

    public function add_body_class() {
        $input = get_option('slwsu_collection_add_body_class');
        ?>
        <input name="slwsu_collection_add_body_class" type="radio" value="true" <?php if ('true' == $input) echo 'checked="checked"'; ?> />
        <span class="description">On</span>
        &nbsp;
        <input name="slwsu_collection_add_body_class" type="radio" value="false" <?php if ('false' == $input) echo 'checked="checked"'; ?> />
        <span class="description">Off</span>
        &nbsp;-&nbsp;
        <span class="description"><?php echo __('Add collection class to body.', 'coon'); ?> </span>
        <?php
    }

    public function inline_styles() {
        $input = get_option('slwsu_collection_inline_styles');
        echo '<div class="collection">';
        echo '<textarea onkeyup="HauteurAuto(\'slwsu_collection_inline_styles\');"  id="slwsu_collection_inline_styles" name="slwsu_collection_inline_styles" >' . $input . '</textarea>';
        echo '<p class="description">' . __('Add css for your collection here.', 'coon') . '</p>';
        echo '</div>';
    }

    /**
     * Plugin
     */
    public function section_plugin() {
        echo __('TThis part concerns the configuration of your collections.', 'coon');
        $nbCpt = count(get_option('slwsu_collection_post_types'));
        ?>
        <script>
            var cpt = <?php echo $nbCpt; ?>;
            function add_fields() {
                cpt++;
                document.getElementById('maj').style.display = 'block';
                var objTo = document.getElementById('filds');
                var divtest = document.createElement("div");
                divtest.innerHTML = '\n\
                <div class="collection">\n\
                    <div class="label"><h2><?php echo __('Add collection', 'coon'); ?></h2></div>\n\
                    <div class="content">\n\
                        <input type="text" name="slwsu_collection_post_types[' + cpt + '][name]" value="" type="text" placeholder="<?php echo __('name', 'coon'); ?>" required="required" />\n\
                        <input name="slwsu_collection_post_types[' + cpt + '][singular]" value="" type="text" placeholder="<?php echo __('singular', 'coon'); ?>" required="required" />\n\
                        <input name="slwsu_collection_post_types[' + cpt + '][plural]" value="" type="text" placeholder="<?php echo __('plural', 'coon'); ?>" required="required" />\n\
                        <input name="slwsu_collection_post_types[' + cpt + '][slug]" value="" type="text" placeholder="<?php echo __('slug', 'coon'); ?>" />\n\
                        <input name="slwsu_collection_post_types[' + cpt + '][args]" value="" type="hidden" />\n\
                        <input name="slwsu_collection_post_types[' + cpt + '][category]" value="" type="hidden" />\n\
                        <textarea name="slwsu_collection_post_types[' + cpt + '][description]" value="" placeholder="<?php echo __('description', 'coon'); ?>" ></textarea>\n\
                        <p class="description"><strong><?php echo __('Indicate here the information about this new collection.', 'coon'); ?></strong></p>\n\
                        <hr />\n\
                    </div>\n\
                </div>';

                objTo.appendChild(divtest);
            }

            function delete_fields(id) {
                document.getElementById('maj').style.display = 'block';
                var element = document.getElementById(id);
                element.parentNode.removeChild(element);
            }
            function HauteurAuto(e) {
                console.log(e);
                var text = document.getElementById((e));
                if (!text) {
                    return;
                }
                text.style.height = "";
                var adjustedHeight = text.clientHeight;
                adjustedHeight = Math.max(text.scrollHeight, adjustedHeight);
                if (adjustedHeight > text.clientHeight) {
                    text.style.height = adjustedHeight + "px";
                }
            }
        </script>
        <?php
    }

    public function post_types() {
        $cpts = get_option('slwsu_collection_post_types', 'false');
        $txt_1 = __('Categories', 'coon');
        $txt_2 = __('Enable categories support.', 'coon');
        $txt_3 = __('Tags', 'coon');
        $txt_4 = __('Enable tags support.', 'coon');
        
        $sCpt = <<<CODE_HTML
            <div class="collection" id="cpt_%s">
                <div class="label" style="background-color:#e5e5e5; color:#262d39; padding:10px; margin-bottom:3px;">
                    <span class="dashicons dashicons-admin-post"></span> %s (%s)
                </div>
                <div class="content">
                    <input name="slwsu_collection_post_types[%s][name]" value="%s" type="hidden" class="regular-text" placeholder="name" />
                    <input name="slwsu_collection_post_types[%s][singular]" value="%s" type="text" placeholder="singular" />
                    <input name="slwsu_collection_post_types[%s][plural]" value="%s" type="text" placeholder="plural" />
                    <input name="slwsu_collection_post_types[%s][slug]" value="%s" type="text" placeholder="slug" />
                    <button style="background-color:#dc3232;" type="button" onclick="delete_fields('cpt_%s'); return false;">X</button>
                    <textarea name="slwsu_collection_post_types[%s][description]" value="%s" onkeyup="HauteurAuto(this);" placeholder="description" >%s</textarea>
                    
                    <input name="slwsu_collection_post_types[%s][category]" type="checkbox" value="1" %s /> 
                    <span class="description"><strong>{$txt_1}</strong> : {$txt_2}</span>
                    &nbsp;&nbsp;
                    <input name="slwsu_collection_post_types[%s][tags]" type="checkbox" value="1" %s /> 
                    <span class="description"><strong>{$txt_3}</strong> : {$txt_4}</span>
                
                    <input name="slwsu_collection_post_types[%s][styles]" type="hidden" value=""/>
                </div>
                <br />
            </div>
CODE_HTML;

        echo'<div id="filds">';
        if ('' !== $cpts && is_array($cpts)):
            $i = 1;
            foreach ($cpts as $cpt):
                $tags = (isset($cpt['tags']) && '1' === $cpt['tags']) ? 'checked="checked"' : '';
                $category = (isset($cpt['category']) && '1' === $cpt['category']) ? 'checked="checked"' : '';
                if ('' !== $cpt['name']):
                    echo sprintf($sCpt, $cpt['name'], // id
                            $cpt['plural'], $cpt['name'], $i, $cpt['name'], $i, $cpt['singular'], $i, $cpt['plural'], $i, $cpt['slug'], $cpt['name'], $i, $cpt['description'], $cpt['description'], $i, $category, $i, $tags, $i);
                    $i++;
                endif;
            endforeach;
        else:
            ?>
            <div class="collection" id="add_cpt">
                <div class="label"><?php echo __('Add a collection', 'coon'); ?></div>
                <div class="content">
                    <input name="slwsu_collection_post_types[1][name]" type="text" placeholder="<?php echo __('name', 'coon'); ?>" required="required" />
                    <input name="slwsu_collection_post_types[1][singular]" type="text" placeholder="<?php echo __('singular', 'coon'); ?>" required="required" />
                    <input name="slwsu_collection_post_types[1][plural]" type="text" placeholder="<?php echo __('plural', 'coon'); ?>" required="required" />
                    <input name="slwsu_collection_post_types[1][slug]" type="text" placeholder="<?php echo __('slug', 'coon'); ?>" required="required" />
                    <br />
                    <textarea onkeyup="HauteurAuto('collection-1');" id="collection-1" name="slwsu_collection_post_types[1][description]" type="text" placeholder="<?php echo __('description', 'coon'); ?>" ></textarea>
                    <input name="slwsu_collection_post_types[1][styles]" type="hidden" value=""/>
                </div>
                <hr />
            </div>
        <?php
        endif;

        echo'</div>';

        echo '<span class="collection"><button type="button" onclick="add_fields(); return false;" >' . __('Add collection', 'coon') . '</button></span>';
    }

    /**
     * Réglages
     */
    public function section_reglages() {
        echo __('This part deals with the general configuration of your collections.', 'coon');
    }

    public function plugin_name() {
        $input = get_option('slwsu_collection_plugin_name');
        echo '<input id="slwsu_collection_plugin_name" name="slwsu_collection_plugin_name" value="' . $input . '" type="text" class="regular-text" />';
        echo '<p class="description">' . __('Add a name to classify the collections here.', 'coon') . '</p>';
    }

    public function capability_type() {
        $input = get_option('slwsu_collection_capability_type');
        echo '<input id="slwsu_collection_capability_type" name="slwsu_collection_capability_type" value="' . $input . '" type="text" class="regular-text" />';
        echo '<p class="description">' . __('Add editing capabilities here. (beta)', 'coon') . '</p>';
    }

    public function post_type_supports() {
        $input = get_option('slwsu_collection_post_type_supports');
        echo '<input id="slwsu_collection_post_type_supports" name="slwsu_collection_post_type_supports" value="' . $input . '" type="text" class="regular-text" />';
        echo '<p class="description">' . __('Add here the different supports for your articles by separating them with a comma ( , ).', 'coon') . '</p>';
    }

    public function post_type_deregister() {
        $input = get_option('slwsu_collection_post_type_deregister');
        echo '<input id="slwsu_collection_post_type_deregister" name="slwsu_collection_post_type_deregister" value="' . $input . '" type="text" class="regular-text" />';
        echo '<p class="description">' . __('Add here the different post types to deregister by separating them with a comma ( , ).', 'coon') . '</p>';
    }

    public function delete_options() {
        $input = get_option('slwsu_collection_delete_options');
        ?>
        <input name="slwsu_collection_delete_options" type="radio" value="true" <?php if ('true' == $input) echo 'checked="checked"'; ?> />
        <span class="description">On</span>
        &nbsp;
        <input name="slwsu_collection_delete_options" type="radio" value="false" <?php if ('false' == $input) echo 'checked="checked"'; ?> />
        <span class="description">Off</span>
        &nbsp;-&nbsp;
        <span class="description"><?php echo __('Delete plugin options when disabling.', 'coon'); ?> </span>
        <?php
    }

    /**
     * Support GRP
     */
    public function section_grouper() {
        echo __('This part concerns the configuration of the plugin group.', 'coon');
    }

    public function grouper_nom() {
        $input = get_option('slwsu_collection_grouper', 'Grouper');
        echo '<input id="slwsu_collection_grouper" name="slwsu_collection_grouper" value="' . $input . '" type="text" class="regular-text" />';
        echo '<p class="description">' . __('Specify here the group to attach', 'coon') . '&nbsp;<strong><i>' . __('Collection', 'coon') . '</i></strong>.</p>';
        echo '<p>' . __('WARNING :: changing the value of this field amounts to modifying the name of the parent link in the WordPress admin menu !', 'coon') . '</p>';
        echo '<p>' . __('You can use this option to isolate this plugin or to add this plugin to an existing Grouper group.', 'coon') . '</p>';
    }

    public function section_grouper_no() {
        echo '<strong><i>' . __('Collection', 'coon') . '</i></strong> ' . __('is compatible with Grouper', 'coon');
        if (file_exists(WP_PLUGIN_DIR . '/grouper')):
            echo '.<br />Grouper ' . __('is installed but does not appear to be enabled', 'coon') . ' : ';
            echo '<a href="plugins.php">' . __('you can activate', 'coon') . ' Grouper</a>';
        else:
            echo ' : <a href="https://web-startup.fr/grouper/" target="_blank">' . __('more information here', 'coon') . '</a>.';
        endif;
    }

    /**
     *
     */
    public function admin_css() {
        echo '<style>
            // Form : https://www.sanwebe.com/2014/08/css-html-forms-designs#form-1-html
            .collection label{
                margin:0 0 3px 0;
                padding:0px;
                display:block;
                font-weight: bold;
            }
            textarea,
            .collection input[type=text],
            .collection input[type=date],
            .collection input[type=datetime],
            .collection input[type=number],
            .collection input[type=search],
            .collection input[type=time],
            .collection input[type=url],
            .collection input[type=email],
            .collection textarea,
            .collection select {
                box-sizing: border-box;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                border:1px solid #BEBEBE;
                padding: 5px;
                margin: 3px 0 0 0;
                -webkit-transition: all 0.30s ease-in-out;
                -moz-transition: all 0.30s ease-in-out;
                -ms-transition: all 0.30s ease-in-out;
                -o-transition: all 0.30s ease-in-out;
                outline: none;
            }
            .collection input[type=text]:focus,
            .collection input[type=date]:focus,
            .collection input[type=datetime]:focus,
            .collection input[type=number]:focus,
            .collection input[type=search]:focus,
            .collection input[type=time]:focus,
            .collection input[type=url]:focus,
            .collection input[type=email]:focus,
            .collection textarea:focus,
            .collection select:focus{
                -moz-box-shadow: 0 0 8px #88D5E9;
                -webkit-box-shadow: 0 0 8px #88D5E9;
                box-shadow: 0 0 8px #88D5E9;
                border: 1px solid #88D5E9;
            }

            .collection textarea{
                width: 100%;
                margin-bottom: 0;
            }
            .collection select{
                margin-top: -4px;
                height: 31px;
            }
            .collection input[type=submit], .collection button, .collection input[type=button]{
                color: #fff;
                border: none;
                padding: 5px 11px 5px 10px;
                cursor: pointer;
                margin: 3px 0 0 0;
                background: #0073aa;
            }
            .collection button:hover, .collection input[type=submit]:hover, .collection input[type=button]:hover{
                background: #008ec2;
            }
            .collection .required{
                color:red;
            }






            .collection-modal-link {
                position: relative;
                float: right;
            }

            .collection-modal {
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                opacity: 0;
                z-index: 99999;
                position: fixed;
                pointer-events: none;
                background: rgba(0,0,0,0.8);
                font-family: Arial, Helvetica, sans-serif;
                -webkit-transition: opacity 250ms ease-in;
                -moz-transition: opacity 250ms ease-in;
                transition: opacity 250ms ease-in;
            }

            .collection-modal:target {
                opacity: 1;
                pointer-events: auto;
            }

            .collection-modal > div {
                width: 400px;
                background: #fff;
                margin: 7% auto;
                position: relative;
                border-radius: 10px;
                padding: 5px 20px 13px 20px;
                background: -o-linear-gradient(bottom, rgb(245,245,245) 25%, rgb(232,232,232) 63%);
                background: -moz-linear-gradient(bottom, rgb(245,245,245) 25%, rgb(232,232,232) 63%);
                background: -webkit-linear-gradient(bottom, rgb(245,245,245) 25%, rgb(232,232,232) 63%);
            }

            .collection-modal-close {
                top: 10px;
                right: 10px;
                font-weight: bold;
                position: absolute;
                text-align: center;
                text-decoration: none;
            }

            .collection-modal-close:hover { color: #333; }

            #collection-contact input[type="text"],
            #collection-contact input[type="email"],
            #collection-contact input[type="url"],
            #collection-contact textarea,
            #collection-contact button[type="submit"] {
                font:400 12px/16px "Open Sans", Helvetica, Arial, sans-serif;
            }

            fieldset {
                border: medium none !important;
                margin: 0 0 6px;
                min-width: 100%;
                padding: 0;
                width: 100%;
            }

            #collection-contact input[type="text"],
            #collection-contact input[type="email"],
            #collection-contact input[type="tel"],
            #collection-contact input[type="url"],
            #collection-contact textarea {
                width:100%;
                border:1px solid #CCC;
                background:#FFF;
                margin:0 0 5px;
                padding:10px;
            }

            #collection-contact input[type="text"]:hover,
            #collection-contact input[type="email"]:hover,
            #collection-contact input[type="tel"]:hover,
            #collection-contact input[type="url"]:hover,
            #collection-contact textarea:hover {
                -webkit-transition:border-color 0.3s ease-in-out;
                -moz-transition:border-color 0.3s ease-in-out;
                transition:border-color 0.3s ease-in-out;
                border:1px solid #AAA;
            }

            #collection-contact textarea {
                height:100px;
                max-width:100%;
                resize:none;
                margin-bottom: 0px;
            }

            #collection-contact input:focus,
            #collection-contact textarea:focus {
                outline:0;
                border:1px solid #999;
            }

            ::-webkit-input-placeholder { color:#888; }
            :-moz-placeholder { color:#888; }
            ::-moz-placeholder { color:#888; }
            :-ms-input-placeholder { color:#888; }


            .collection-contact-valide, .collection-contact-error{
                padding: 8px;
                background-color: white;
            }
            .collection-contact-valide{
                border-left: 4px solid #46b450;
            }
            .collection-contact-error{
                border-left: 4px solid #dc3232;
            }

        </style>';
    }

    /**
     *
     * @param (string) $str - Chaine a traiter
     * @param (string) $sep - Séparateur
     * @param (string $charset - Charset
     * @return type
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
