<?php

/*
Plugin Name: Advanced Custom Fields: Pages by template
Plugin URI: https://github.com/jonathan-dejong/acf-pages-by-template
description: Adds a field to select a page, filtered on pages of a specific template
Version: 2.0.0
Author: Jonathan de Jong, Xavier Priour
Author URI: tigerton.se
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf_pages_by_template', false, dirname( plugin_basename(__FILE__) ) . '/lang/' ); 


// 2. Include field type for ACF5
function include_field_types_acf_pages_by_template( $version ) {
  include_once('acf_pages_by_template_v5.php');
}
add_action('acf/include_field_types', 'include_field_types_acf_pages_by_template'); 


// 3. Include field type for ACF4
function register_fields_acf_pages_by_template() {
  include_once('acf_pages_by_template_v4.php');
}
add_action('acf/register_fields', 'register_fields_acf_pages_by_template'); 

?>