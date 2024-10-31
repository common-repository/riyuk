<?php
/*
Plugin Name: RIYUK
Plugin URI: http://www.riyuk.de/wp
Description: Shortens every Post with Bitly. Youtube scroller integration like Facebook.
Version: 0.1.6
Author: Gerrit 'riyuk' Boettcher
Author URI: http://www.riyuk.de
License: GPL2
*/

if( !defined( 'R_PLUGIN_DIR' ) ) define( 'R_PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
if( !defined( 'R_PHP' ) ) define( 'R_PHP', true );
if( !defined( 'R_NEED_PHP' ) ) define( 'R_NEED_PHP', '5.3.0' );

// we use call statics
if( version_compare( PHP_VERSION, R_NEED_PHP, '<' ) === true ) {
	add_action( 'admin_notices', 'rytLowPHP' );
	function rytLowPHP() {
	        echo '<div class="error">';
	        echo '<p>';
	        echo '<strong>' . __('Warning:', 'riyuk') . '</strong> ';
	        __( sprintf( "You're using PHP Version %s - you need at least PHP Version %s to run the Plugin 'RIYUK'.", PHP_VERSION, R_NEED_PHP ), 'riuyk' );
	        echo '</p></div>';
	}
	
	add_filter( 'plugin_action_links_riyuk/riyuk.php', 'rytFilter' );
	function rytFilter( $links ) {
		$links['error'] = __( 'Wrong PHP Version! Functions not active!', 'riyuk' );
		return $links;
	}
	define( 'R_PHP', false );
}

if( R_PHP ) {
	
	// load class
	require_once( R_PLUGIN_DIR . 'rytube.php' );
	
	// frontend plugin laden
	if( !is_admin() ) {
		
		if( is_string( get_option('ryt-youtube') ) && get_option('ryt-youtube') == '1' ) {
			// hook plugin into wp
			add_action( 'init', array( 'rYtube', 'actionInit' ) ); // zuständig dafür jquery in wp zu laden
			add_filter( 'the_content', array( 'rYtube', 'filterContent' ) );
		}
		
		$bitlyAccount = get_option( 'ryt-bitly-username' );
		$bitlyApi = get_option( 'ryt-bitly-api' );
		if( strlen( $bitlyAccount ) && strlen( $bitlyApi ) ) {
			// bit.ly
			add_action( 'the_post', array( 'rYtube', 'actionPost' ) );
		}
		
	}
	
	// admin interface hook
	add_action( 'admin_menu', array( 'rYtube', 'actionAdmin' ) );

}
