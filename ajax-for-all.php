<?php
/*
Copyright 2010 Nicolas Kuttler (email : wp@nicolaskuttler.de )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

Plugin Name: Ajax For All
Plugin URI: http://www.nkuttler.de/2010/07/22/automatic-ajax-for-wordpress-plugin/
Author: Nicolas Kuttler
Author URI: http://www.nkuttler.de/
Description: This plugin will enable a fancy ajax functionality on most themes.
Version: 0.5.2
Text Domain: ajax-for-all

*/

/**
 * @since 0.2
 * @package ajax-for-all
 * @subpackage pluginwrapper
 */
class AjaxForAll {

	/**
	 * Array containing the options
	 *
	 * @since 0.2
	 * @var string
	 */
	var $options;

	/**
	 * The plugin file
	 *
	 * @since 0.3
	 * @var string
	 */
	var $plugin_file;

	/**
	 * Load options
	 *
	 * @return none
	 * @since 0.2
	 */
	function __construct () {
		$this->plugin_file = __FILE__;
		$this->options = get_option ( 'ajax-for-all' );
	} 

	/**
	 * Return a specific option value
	 *
	 * @param string $option name of option to return
	 * @return mixed 
	 * @since 0.2
	 */
	function get_option( $option ) {
		if ( isset ( $this->options[$option] ) )
			return $this->options[$option];
		else
			return false;
	}

	/**
	 * return plugin URL
	 *
	 * @return string
	 * @since 0.2
	 */
	function plugin_url () {
		return plugins_url ( plugin_basename ( dirname ( __FILE__ ) ) );
	}

}

/**
 * Instantiate the AjaxForAllFrontend or AjaxForAllAdmin Class
 */
if ( is_admin () ) {
	require_once ( dirname ( __FILE__ ) . '/inc/admin.php' );
	$AjaxForAllAdmin = new AjaxForAllAdmin ();
}
else {
	require_once ( dirname ( __FILE__ ) . '/inc/frontend.php' );
	global $AjaxForAllFrontend;
	$AjaxForAllFrontend = new AjaxForAllFrontend ();
}
