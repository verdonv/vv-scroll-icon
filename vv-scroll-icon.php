<?php
/* 
Plugin Name: Verdon's Scroll Icon
Description: A shortcode to put an animated scroll icon on a page [vvscroll]
Version: 1.1.0
Author: Verdon Vaillancourt
Author URI: http://verdon.ca/
Update URL: https://github.com/verdonv/vv-scroll-icon/
License: GPLv2 or later
Text Domain: vv-scroll-icon
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/* setup a few constants */
define( 'VVSI_VERSION', '1.1.0' );
define( 'VVSI__MINIMUM_WP_VERSION', '4.0' );
define( 'VVSI__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VVSI__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/* check for the installed version */
if (get_option( 'vvsi_version' )) {
	$installed_ver = get_option( 'vvsi_version' );
} else {
	$installed_ver = '1.0.0';
}

/* get the required pages and classes */
require_once( VVSI__PLUGIN_DIR . 'vvsi-settings.php' );
require_once( VVSI__PLUGIN_DIR . 'vvsi-display.php' );

/* activation and deactivation hooks */
register_activation_hook( __FILE__, array( 'VVSI_Settings', 'vvsi_activate') );
register_deactivation_hook( __FILE__, array( 'VVSI_Settings', 'vvsi_deactivate') );


/* add a settings link to the row in the plugin page */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'vvsi_plugin_action_links', 10, 2 );
function vvsi_plugin_action_links($links, $file) {
	static $this_plugin;
	if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
	}
	if ($file == $this_plugin) {
		$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/themes.php?page=vvsi_options">Settings</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

?>