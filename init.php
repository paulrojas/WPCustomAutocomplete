<?php
/*
Plugin Name: WP Custom Autocomplete
Plugin URI: https://github.com/paulrojas/WPCustomAutocomplete
Description: PHP test for WalletHub
Version: 1.0
Author: Paul Rojas
Author URI: http://www.paulrojas.me
License: Creative Commons Attribution-ShareAlike


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/


class WPCustomAutocomplete {
    protected static $options_field = 'wpca_settings';
    protected static $options_field_ver = 'wpca_settings_ver';
    protected static $options_field_current_ver = '1.0';
    protected static $slug = 'wp-custom-autocomplete';
    protected static $options_default = array(
        'autocomplete_url' => '',
        'autocomplete_minimum' => 3,
        'autocomplete_theme' => '/aristo/jquery-ui-aristo.min.css',
        'autocomplete_custom_theme' => '',
    );
    protected static $options_init = array(
        'autocomplete_url' => '',
        'autocomplete_minimum' => 3,
        'autocomplete_theme' => '/aristo/jquery-ui-aristo.min.css',
        'autocomplete_custom_theme' => '',
    );

    var $pluginUrl,
        $defaults,
        $script_mode = 'min',
        $options;

    public function __construct() {

        $this->initVariables();
        add_action('wp_enqueue_scripts', array($this, 'initScripts'));
        add_action('admin_enqueue_scripts', array($this, 'initAdminScripts'));
        //$this->initAjax();

        // init admin settings page
        add_action('admin_menu', array($this, 'adminSettingsMenu'));
        add_action('admin_init', array($this, 'adminSettingsInit')); // Add admin init functions
    }

    public function initVariables() {
        $this->pluginUrl = plugin_dir_url(__FILE__);

        $options = get_option(self::$options_field);
        $this->options = ($options !== false) ? wp_parse_args($options, self::$options_default) : self::$options_default;

        $this->script_mode = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '.min';
    }

    public function initScripts() {
        $localVars = array(
            'autocompleteUrl' => $this->options['autocomplete_url'],
            'minLength' => $this->options['autocomplete_minimum']
        );
        if ($this->options['autocomplete_theme'] !== '--None--') {
            wp_enqueue_style('WPCustomAutocomplete-theme', plugins_url('css' . $this->options['autocomplete_theme'], __FILE__), array(), '1.9.2');
        }
        if (wp_script_is('jquery-ui-autocomplete', 'registered')) {
            wp_enqueue_script('WPCustomAutocomplete', plugins_url('js/jquery.customautocomplete.js', __FILE__), array('jquery-ui-autocomplete'), '1.0.0', true);
        } else {
            wp_register_script('jquery-ui-autocomplete', plugins_url('js/jquery-ui-1.9.2.custom.min.js', __FILE__), array('jquery-ui'), '1.9.2', true);
            wp_enqueue_script('WPCustomAutocomplete', plugins_url('js/jquery.customautocomplete.js', __FILE__), array('jquery-ui-autocomplete'), '1.0.0', true);
        }

        $localVars = apply_filters('wp_custom_autocomplete_settings', $localVars);

        wp_localize_script('WPCustomAutocomplete', 'WPCustomAutocomplete', $localVars);
    }

    public function initAdminScripts() {
        $localAdminVars = array(
            'defaults' => self::$options_default
        );
        wp_enqueue_script('WPCustomAutocompleteAdmin', plugins_url('js/admin-scripts.js', __FILE__), array('jquery-ui-sortable'), '1.0.0', true);
        wp_localize_script('WPCustomAutocompleteAdmin', 'WPCustomAutocompleteAdmin', $localAdminVars);
    }

    /*public function initAjax() {
        add_action('wp_ajax_autocompleteCallback', array($this, 'acCallback'));
        add_action('wp_ajax_nopriv_autocompleteCallback', array($this, 'acCallback'));
    }*/

    /*
     * Admin Settings
     *
     */
    public function adminSettingsMenu() {
        $page = add_options_page(
            __( 'WP Custom Autocomplete', 'wp-custom-autocomplete' ),
            __( 'WP Custom Autocomplete', 'wp-custom-autocomplete' ),
            'manage_options',
            'wp-custom-autocomplete', array(
                $this,
                'settingsPage'
            ) );
    }

    public function settingsPage() {
        ?>
        <div class="wrap wpcustomautocomplete-settings">
            <h2><?php _e( 'WP Custom Autocomplete', 'wp-custom-autocomplete' ); ?></h2>
            <form action="options.php" method="post">
                <?php wp_nonce_field(); ?>
                <?php
                settings_fields( 'wpca_settings' );
                do_settings_sections( 'wp-custom-autocomplete' );
                ?>
                <input class="button-primary" name="Submit" type="submit" value="<?php _e( 'Save settings', 'wp-custom-autocomplete' ); ?>">
                <input class="button revert" name="revert" type="button" value="<?php _e( 'Revert to Defaults', 'wp-custom-autocomplete' ); ?>">
            </form>
        </div>
    <?php
    }

    /**
     *
     */
    public function adminSettingsInit() {
        register_setting(
            self::$options_field,
            self::$options_field,
            array( $this, 'wpca_settings_validate' )
        );
        add_settings_section(
            'wpca_settings_main',
            __( 'Settings', 'wp-custom-autocomplete' ),
            array( $this, 'wpca_settings_main_text' ),
            'wp-custom-autocomplete'
        );
        add_settings_field(
            'autocomplete_url',
            __( 'Service URL', 'wp-custom-autocomplete' ),
            array( $this, 'wpca_settings_field_url' ),
            'wp-custom-autocomplete',
            'wpca_settings_main'
        );
        add_settings_field(
            'autocomplete_minimum',
            __( 'Autocomplete Trigger', 'wp-custom-autocomplete' ),
            array( $this, 'wpca_settings_field_minimum' ),
            'wp-custom-autocomplete',
            'wpca_settings_main'
        );
        add_settings_field(
            'autocomplete_theme',
            __( 'Theme Stylesheet', 'wp-custom-autocomplete' ),
            array( $this, 'wpca_settings_field_themes' ),
            'wp-custom-autocomplete',
            'wpca_settings_main'
        );
    }

    public function wpca_settings_main_text() {
    }

    public function wpca_settings_field_url() {
        ?>
        <input id="autocomplete_url" class="regular-text" name="<?php echo self::$options_field; ?>[autocomplete_url]" value="<?php echo htmlspecialchars( $this->options['autocomplete_url'] ); ?>">
        <p class="description">
            <?php _e( 'Enter the URL of the service', 'wp-custom-autocomplete' ); ?><br>
        </p>
    <?php
    }

    public function wpca_settings_field_minimum() {
        ?>
        <input id="autocomplete_minimum" class="regular-text" name="<?php echo self::$options_field; ?>[autocomplete_minimum]" value="<?php echo $this->options['autocomplete_minimum']; ?>">
        <p class="description"><?php _e( 'The minimum number of characters before the autocomplete triggers.', 'wp-custom-autocomplete' ); ?>
        <br>
    <?php
    }

    public function wpca_settings_field_themes() {
        $globFilter = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.css';

        if ( $themeOptions = glob( $globFilter, GLOB_ERR ) ) {
            array_unshift( $themeOptions, __( '--None--', 'wp-custom-autocomplete' ) );
        } else {

        }
        ?>
        <select name="<?php echo self::$options_field; ?>[autocomplete_theme]" id="autocomplete_theme">
            <?php
            foreach ( $themeOptions as $stylesheet ) {
                $newSheet = str_replace( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'css', '', $stylesheet );
                printf( '<option value="%s"%s>%s</option>', $newSheet, ( $newSheet == $this->options['autocomplete_theme'] ) ? ' selected="selected"' : '', $newSheet );
            }
            ?>
        </select>
        <p class="description"><?php _e( 'If you would like to use your own styles outside of the plugin, select "--None--" and no stylesheet will be loaded by the plugin.', 'wp-custom-autocomplete' ); ?></p>
    <?php
    }

    public function wpca_settings_validate( $input ) {
        $valid = wp_parse_args( $input, self::$options_default );

        return $valid;
    }

    public function activate( $network_wide ) {
        if ( get_option( 'wpca_settings' ) === false ) {
            update_option( 'wpca_settings', self::$options_init );
        } else {
            $options = get_option( 'wpca_settings' );
            update_option( 'wpca_settings', $options );
        }
    }
}

register_activation_hook( __FILE__, array( 'WPCustomAutocomplete', 'activate' ) );
$WPCustomAutocomplete = new WPCustomAutocomplete();

function create_customautocomplete_func() {
    echo '<input type="text" name="location" id="location" value="">';
}

add_shortcode('create_customautocomplete', 'create_customautocomplete_func');
