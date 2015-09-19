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


function fancybox_wp_setup(){
    wp_enqueue_style("jquery.customautocomplete", WP_PLUGIN_URL."/css/aristo/jquery-ui-aristo.min.css", false, "");
    wp_enqueue_script("jquery");
    wp_enqueue_script("jquery.ui", WP_PLUGIN_URL."/js/jquery-ui-1.9.2.custom.min.js", array("jquery"), "", 1);
    wp_enqueue_script("jquery.customautocomplete", WP_PLUGIN_URL."/js/jquery.customautocomplete.js", array("jquery", "jquery.ui"), "", 1);
}