<?php

/**
 * @package Collection
 * version 0.5
 */

defined('ABSPATH') or exit();

class slwsu_collection_admin_form {

    /**
     * ...
     */
    public static function validation() {
        if (isset($_GET['settings-updated'])) {
            delete_transient('slwsu_collection_options');
            ?>
            <div id="message" class="updated">
                <p><strong><?php echo __('Settings saved', 'coon') ?></strong></p>
            </div>
            <?php
        }
    }

    /**
     * ...
     */
    public static function action() {
        ?>
        <a class="collection-modal-link" style="text-decoration:none; font-weight:bold;" href="#openModal"><?php echo __('About', 'coon'); ?> <span class="dashicons dashicons-info"></span></a>
        <?php
    }

    /**
     * ...
     */
    public static function message($post) {
        ?>
        <div id="openModal" class="collection-modal">
            <div>
                <a href="#collection-modal-close" title="Close" class="collection-modal-close"><span class="dashicons dashicons-dismiss"></span></a>
                <h2><?php echo __('About', 'coon'); ?></h2>
                <p><span class="dashicons dashicons-admin-users"></span> <?php echo __('By', 'coon'); ?> <?php echo 'Steeve Lefebvre - slWsu'; ?></p>
                <p><span class="dashicons dashicons-admin-site"></span> <?php echo __('More information', 'coon'); ?> : <a href="<?php echo 'https://web-startup.fr/collection/'; ?>" target="_blank"><?php _e('plugin page', 'coon'); ?></a></p>
                <p><span class="dashicons dashicons-admin-tools"></span> <?php echo __('Development for the web', 'coon'); ?> : HTML, PHP, JS, WordPress</p>
                <h2><?php echo __('Support', 'coon'); ?></h2>
                <p><span class="dashicons dashicons-email-alt"></span> <?php echo __('Ask your question', 'coon'); ?></p>
                <?php
                if (isset($post['submit'])) {
                    global $current_user; $to = 'steeve.lfbvr@gmail.com'; $subject = "Support Grouper !!!";
                    $roles = implode(", ", $current_user->roles);
                    $message = "From: " . get_bloginfo('name') . " - " . get_bloginfo('home') . " - " . get_bloginfo('admin_email') . "\n";
                    $message .= "By : " . strip_tags($post['nom']) . " - " . $post['email'] . " - " . $roles . "\n";
                    $message .= strip_tags($post['message']) . "\n";
                    if (wp_mail($to, $subject, $message)):
                        echo '<p class="collection-contact-valide"><strong>' . __('Your message has been sent !', 'coon') . '</strong></p>';
                    else:
                        echo '<p class="collection-contact-error">' . __('Something went wrong, go back and try again !', 'coon') . '</p>';
                    endif;
                }
                ?>
                <form id="collection-contact" action="" method="post">
                    <fieldset>
                        <input id="nom" name="nom" type="text" placeholder="<?php echo __('Your name', 'coon'); ?>" required="required">
                    </fieldset>
                    <fieldset>
                        <input id="email" name="email" type="email" placeholder="<?php echo __('Your Email Address', 'coon'); ?>" required="required">
                    </fieldset>
                    <fieldset>
                        <textarea id="message" name="message" placeholder="<?php echo __('Formulate your support request or feature proposal here...', 'coon'); ?>" required="required"></textarea>
                    </fieldset>
                    <fieldset>
                        <input id="submit" name="submit" type="submit" value="<?php echo __('Send', 'coon'); ?>" class="button button-primary" type="submit" id="collection-contact-submit" data-submit="...Sending" />
                    </fieldset>
                </form>
            </div>
        </div>
        <?php
    }

}
