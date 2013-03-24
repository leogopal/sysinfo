<?php
/*
Plugin Name: SysInfo
Plugin URI: http://wordpress.org/extend/plugins/sysinfo/
Description: Useful system information about your WordPress install.
Version: 1.0.0
Author: Dave Donaldson
Author URI: http://arcware.net
License: http://www.gnu.org/licenses/gpl-2.0.html
*/

class SysInfo {
	function __construct() {
		// Global constants first
		define('SYSINFO_VERSION_KEY', 'sysinfo_version');
		define('SYSINFO_VERSION_NUM', '1.0.0');
		
		// Activation/deactivation hooks
		register_activation_hook(__FILE__, array($this, 'do_activation'));
		register_deactivation_hook(__FILE__, array($this, 'do_deactivation'));
		
		// Any other hooks we need
		add_action('init', array($this, 'load_textdomain'));
		add_action('admin_menu', array($this, 'add_tools_page'));
		add_filter('plugin_action_links', array($this, 'add_action_links'), 10, 2);
	}
	
	function activate() {
		update_option(SYSINFO_VERSION_KEY, SYSINFO_VERSION_NUM);
	}
	
	function deactivate() {
		delete_option(SYSINFO_VERSION_KEY);
	}
	
	function do_activation($network_wide) {
		if ($network_wide) {
			$this->call_function_for_each_site(array($this, 'activate'));
		}
		else {
			$this->activate();
		}
	}
	
	function do_deactivation($network_wide) {	
		if ($network_wide) {
			$this->call_function_for_each_site(array($this, 'deactivate'));
		}
		else {
			$this->deactivate();
		}
	}
	
	function call_function_for_each_site($function) {
		global $wpdb;
		
		// Hold this so we can switch back to it
		$current_blog = $wpdb->blogid;
		
		// Get all the blogs/sites in the network and invoke the function for each one
		$blog_ids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
		foreach ($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			call_user_func($function);
		}
		
		// Now switch back to the root blog
		switch_to_blog($current_blog);
	}
	
	function load_textdomain() {
		load_plugin_textdomain('sysinfo', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}
	
	function add_tools_page() {
		$admin_pages = array();
		
		$parent_slug = 'tools.php';
		$page_title = __('SysInfo', 'sysinfo');
		$sub_menu_title = __('SysInfo', 'sysinfo');
		$capability = 'manage_options';
		$menu_slug = 'sysinfo';
		$function = array($this, 'add_sysinfo_page');
		$admin_pages[] = add_submenu_page($parent_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);
		
		foreach ($admin_pages as $admin_page) {
			add_action("admin_print_styles-{$admin_page}", array($this, 'add_admin_styles'));
		}
	}
	
	function add_sysinfo_page() {
		require_once 'views/admin.php';
	}
	
	function add_admin_styles() {	
		wp_enqueue_style('sysinfo-css', plugins_url('sysinfo/css/sysinfo.css'));
	}

	function add_action_links($links, $file) {
		static $this_plugin;
		
		if (!$this_plugin) {
			$this_plugin = plugin_basename(__FILE__);
		}
		
		if ($file == $this_plugin) {
			$packs_link = '<a href="' . admin_url('tools.php?page=sysinfo') . '">' . __('View', 'sysinfo') . '</a>';
			array_unshift($links, $packs_link);
		}

		return $links;
	}
	
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
		}
		elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
			$platform = 'Mac';
		}
		elseif (preg_match('/windows|win32/i', $user_agent)) {
			$platform = 'Windows';
		}
		
		// Next get the name of the user agent yes seperately and for good reason
		if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
			$browser_name = 'Internet Explorer';
			$browser_name_short = "MSIE";
		}
		elseif (preg_match('/Firefox/i', $user_agent)) {
			$browser_name = 'Mozilla Firefox';
			$browser_name_short = "Firefox";
		}
		elseif (preg_match('/Chrome/i', $user_agent)) {
			$browser_name = 'Google Chrome';
			$browser_name_short = "Chrome";
		}
		elseif (preg_match('/Safari/i', $user_agent)) {
			$browser_name = 'Apple Safari';
			$browser_name_short = "Safari";
		}
		elseif (preg_match('/Opera/i', $user_agent)) {
			$browser_name = 'Opera';
			$browser_name_short = "Opera";
		}
		elseif (preg_match('/Netscape/i', $user_agent)) {
			$browser_name = 'Netscape';
			$browser_name_short = "Netscape";
		}
		
		// Finally get the correct version number
		$known = array('Version', $browser_name_short, 'other');
		$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $user_agent, $matches)) {
			// We have no matching number just continue
		}
		
		// See how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			// We will have two since we are not using 'other' argument yet
			// See if version is before or after the name
			if (strripos($user_agent, "Version") < strripos($user_agent, $browser_name_short)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}
		
		// Check if we have a number
		if ($version == null || $version == "") { $version = "?"; }
		
		return array(
			'user_agent' => $user_agent,
			'name' => $browser_name,
			'version' => $version,
			'platform' => $platform,
			'pattern' => $pattern
		);
	}
	
	function get_all_plugins() {
		return get_plugins();
	}
	
	function get_active_plugins() {
		return get_option('active_plugins', array());
	}
}

// Let's get this party started
$sysinfo = new SysInfo();
?>