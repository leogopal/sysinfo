<?php
/*
Plugin Name: SysInfo
Plugin URI: http://wordpress.org/extend/plugins/sysinfo/
Description: Useful system information about your WordPress install.
Version: 1.2.0
Author: Dave Donaldson
Author URI: http://arcware.net
License: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Use this plugin to view system information about your WordPress install. Things like the versions of WordPress, PHP, and MySQL,
 * installed/activated plugins, the current theme, memory limit, allowable upload size, operating system, browser details, etc. 
 * This information would be very useful to many users and for people that need to provide support for their plugins and themes.
 *
 * This plugin implements the singleton pattern so anytime you need access to an instance of the class, then call:
 * `$sysinfo = SysInfo::get_instance()`
 *
 * @version	1.2.0
 */
class SysInfo {

	/*--------------------------------------------------*
	 * Attributes
	 *--------------------------------------------------*/

	/** A reference to the instance of this class */
	private static $instance;
	
	/*--------------------------------------------------*
	 * Constructor
	 *--------------------------------------------------*/
	
	/**
	 * The function used to set and/or retrieve the current instance of this class.
	 *
	 * @return	object	$instance	A reference to an instance of this class.
	 */
	public static function get_instance() {
		
		if( null == self::$instance ) {
			self::$instance = new self;
		} // end if
		
		return self::$instance;
		
	} // end get_instance;

	/**
	 * The constructor for the class responsible for setting constant definitions, activation hooks,
	 * and filters.
	 */
	private function __construct() {
	
		// Global constants first
		define( 'SYSINFO_VERSION_KEY', 'sysinfo_version' );
		define( 'SYSINFO_VERSION_NUM', '1.2.0');
		define( 'SYSINFO_PLUGIN_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
		define( 'SYSINFO_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . SYSINFO_PLUGIN_NAME );
		define( 'SYSINFO_PLUGIN_URL', WP_PLUGIN_URL . '/' . SYSINFO_PLUGIN_NAME );
		
		// Activation/deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'do_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'do_deactivation' ) );
		
		// Any other hooks we need
		add_action( 'init', array($this, 'load_textdomain' ) );
		add_action( 'admin_menu', array($this, 'add_tools_page' ) );
		add_filter( 'plugin_action_links', array($this, 'add_action_links'), 10, 2 );
		
	} // end constructor
	
	/*--------------------------------------------------*
	 * Hooks
	 *--------------------------------------------------*/
	
	/**
	 * Defines the textdomain for this plugin for localization and translation.
	 */
	function load_textdomain() {
		load_plugin_textdomain('sysinfo', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	} // load_textdomain
	
	/**
	 * Updates the version key and version number of the plugin in the options table.
	 */
	function activate() {
		update_option( SYSINFO_VERSION_KEY, SYSINFO_VERSION_NUM );
	} // end activate
	
	/**
	 * Deletes the version information from the database
	 */
	function deactivate() {
		delete_option(SYSINFO_VERSION_KEY);
	} // end decativate
	
	/**
	 * Fired on plugin activation.
	 *
	 * @param	boolean $network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	function do_activation( $network_wide ) {
	
		// If this plugin as called network wide, then call the functionality for each site
		if( $network_wide ) {
		
			$this->call_function_for_each_site( array( $this, 'activate' ) );
			
		// Otherwise, simply call it for this single site
		} else {
		
			$this->activate();
			
		} // end if/else
		
	} // do_activation
	
	/**
	 * Fired on plugin deactivation.
	 *
	 * @param	boolean $network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	function do_deactivation( $network_wide ) {	
	
		// If this plugin as called network wide, then call the functionality for each site
		if( $network_wide ) {
		
			$this->call_function_for_each_site( array( $this, 'deactivate' ) );
			
		// Otherwise, simply call it for this single site	
		} else {
		
			$this->deactivate();
			
		} // end if/else
		
	} // end do_deactivation
	
	/**
	 * The callback function used if this plugin is being used in a multisite environment. It's responsible for iterating
	 * through each of the blogs, then gahtering the information for each blog.
	 *
	 * @param	function	$function	The callback function to fire for each blog.
	 */
	function call_function_for_each_site( $function ) {
	 
		global $wpdb;
		
		// Hold this so we can switch back to it
		$current_blog = $wpdb->blogid;
		
		// Get all the blogs/sites in the network and invoke the function for each one
		$blog_ids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs" ) );
		foreach( $blog_ids as $blog_id ) {
		
			switch_to_blog( $blog_id );
			call_user_func( $function );
			
		} // end foreach
		
		// Now switch back to the root blog
		switch_to_blog( $current_blog );
		
	} // end call_function_for_each_site
	
	/**
	 * Adds the 'SysInfo' page to the 'Tools' menu.
	 */
	function add_tools_page() {
	
		$admin_pages = array();
		
		// Build the options for the submenu page API call
		$parent_slug = 'tools.php';
		$page_title = __( 'SysInfo', 'sysinfo' );
		$sub_menu_title = __( 'SysInfo', 'sysinfo' );
		$capability = 'manage_options';
		$menu_slug = 'sysinfo';
		$function = array( $this, 'add_sysinfo_page' );
		
		// Actually add the page as a subemnu
		$admin_pages[] = add_submenu_page( $parent_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function );
		
		// Include the stylesheet for the given page
		foreach( $admin_pages as $admin_page) {
			add_action( "admin_print_styles-{$admin_page}", array( $this, 'add_admin_styles' ) );
		} // end foreach
		
	} // end add_tools_page
	
	/**
	 * Includes the admin view for the SysInfo page.
	 */
	function add_sysinfo_page() {
		require_once 'views/admin.php';
	} // end add_sysinfo_page
	
	/**
	 * Adds the stylesheet for the SysInfo admin page.
	 */
	function add_admin_styles() {	
		wp_enqueue_style( 'sysinfo-css', SYSINFO_PLUGIN_URL . '/css/sysinfo.css', SYSINFO_VERSION_NUM );
	} // end add_admin_styles

	/**
	 * Creates the action links for this plugin including the links and filename.
	 *
	 * @param	array	$links	Links to the edit and activate features of this plugin.
	 * @param	string	$file	The actual plugin file of this plugin.
	 * @return	array	$links	Includes links to Activate, Edit, and Delete
	 */
	function add_action_links( $links, $file ) {
	
		static $this_plugin;
		
		if ( ! $this_plugin ) {
			$this_plugin = plugin_basename(__FILE__);
		} // end if
		
		if( $file == $this_plugin ) {
		
			$packs_link = '<a href="' . admin_url( 'tools.php?page=sysinfo' ) . '">' . __( 'View', 'sysinfo' ) . '</a>';
			array_unshift( $links, $packs_link );
			
		} // end if

		return $links;
		
	} // end add_action_links
	
	/**
	 * Determines which browser is currently being used to view this installation of WordPress.
	 *
	 * @return	array	Includes information on user_agent, name, version, platform, and pattern.
	 */
	function get_browser() {
	
		// http://www.php.net/manual/en/function.get-browser.php#101125.
		// Cleaned up a bit, but overall it's the same.

		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$browser_name = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		// First get the platform
		if (preg_match('/linux/i', $user_agent)) {
			$platform = 'Linux';
		} elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
			$platform = 'Mac';
		} elseif (preg_match('/windows|win32/i', $user_agent)) {
			$platform = 'Windows';
		} // end if/else
		
		// Next get the name of the user agent yes seperately and for good reason
		if( preg_match( '/MSIE/i', $user_agent ) &&  ! preg_match( '/Opera/i', $user_agent ) ) {
		
			$browser_name = 'Internet Explorer';
			$browser_name_short = "MSIE";
			
		} elseif( preg_match( '/Firefox/i', $user_agent ) ) {
		
			$browser_name = 'Mozilla Firefox';
			$browser_name_short = "Firefox";
			
		} elseif( preg_match( '/Chrome/i', $user_agent ) ) {
		
			$browser_name = 'Google Chrome';
			$browser_name_short = "Chrome";
			
		} elseif( preg_match( '/Safari/i', $user_agent ) ) {
		
			$browser_name = 'Apple Safari';
			$browser_name_short = "Safari";
			
		} elseif( preg_match( '/Opera/i', $user_agent ) ) {
		
			$browser_name = 'Opera';
			$browser_name_short = "Opera";
			
		} elseif( preg_match('/Netscape/i', $user_agent ) ) {
		
			$browser_name = 'Netscape';
			$browser_name_short = "Netscape";
			
		} // end if/else
		
		// Finally get the correct version number
		$known = array( 'Version', $browser_name_short, 'other' );
		$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if( ! preg_match_all( $pattern, $user_agent, $matches ) ) {
			// We have no matching number just continue
		} // end if
		
		// See how many we have
		$i = count( $matches['browser'] );
		if( $i != 1 ) {
		
			// We will have two since we are not using 'other' argument yet
			// See if version is before or after the name
			if (strripos($user_agent, "Version") < strripos($user_agent, $browser_name_short)){
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			} // end if/else
			
		} else {
			$version= $matches['version'][0];
		} // end if/else
		
		// Check if we have a number
		if ($version == null || $version == "") { 
			$version = "?"; 
		} // end if/else
		
		return array(
			'user_agent'	=> $user_agent,
			'name' 			=> $browser_name,
			'version' 		=> $version,
			'platform' 		=> $platform,
			'pattern' 		=> $pattern
		);
		
	} // end get_browser
	
	/**
	 * From the Codex:
	 *
	 *	"Check the plugins directory and retrieve all plugin files with plugin data."
	 *
	 * @return	array	The array of plugins currently installed in the environment.
	 */
	function get_all_plugins() {
		return get_plugins();
	} // end get_all_plugins
	
	/**
	 * Retrieves all a list of the active plugins.
	 *
	 * @return	array	The list of active plugins.
	 */
	function get_active_plugins() {
		return get_option( 'active_plugins', array() );
	} // end get_active_plugins
	
	/**
	 * Retrieves the amount of memory being used by the installation along with the themes, plugins, etc.
	 *
	 * @return	float	The amount of memory being used by this installation.
	 */
	function get_memory_usage() {
		return round( memory_get_usage() / 1024 / 1024, 2 );
	} // end get_memory_usage

	/**
	 * From the Codex:
	 *
	 *	"Retrieve all autoload options or all options, if no autoloaded ones exist."
	 *
	 * @return	array	$options	All of the options that exist in the WordPress installation
	 */
	function get_all_options() {
	
		// Not to be confused with the core deprecated get_alloptions
		return wp_load_alloptions();
		
	} // end get_all_options

	/**
	 * Gathers a list of all of the transients set in the plugin.
	 * 
	 * @param	array	$options		The array of options managed by the installation of WordPress
	 * @return	array	$transients		The array of transients currently stored by WordPress
	 */
	function get_transients_in_options( $options ) {
	
		$transients = array();

		foreach( $options as $name => $value ) {
		
			if( stristr( $name, 'transient' ) ) {
				$transients[ $name ] = $value;
			} // end if
			
		} // end foreach
		
		return $transients;
		
	} // end get_transients_in_options
	
} // end class

// Let's get this party started
SysInfo::get_instance();