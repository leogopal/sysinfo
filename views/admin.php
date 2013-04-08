<?php
global $sysinfo;

$theme = wp_get_theme();
$browser = $sysinfo->get_browser();
$plugins = $sysinfo->get_all_plugins();
$active_plugins = $sysinfo->get_active_plugins();
$memory_limit = ini_get('memory_limit');
$memory_usage = $sysinfo->get_memory_usage();
?>

<div id="sysinfo">
	<div class="wrap">
		<div class="icon32">
			<img src="<?php echo plugins_url('sysinfo/images/sysinfo.png') ?>" />
		</div>
		
		<h2 class="title"><?php _e('SysInfo', 'sysinfo') ?></h2>
		
		<div class="clear"></div>

		<div class="section">
			<div class="header">
				<?php _e('System Information', 'sysinfo') ?>
			</div>
			
			<div class="inside">
				<a class="button-primary" href="#" onclick="window.open('<?php echo plugins_url('sysinfo/views/phpinfo.php') ?>', 'PHPInfo', 'width=800, height=600, scrollbars=1'); return false;"><?php _e('PHP Info', 'sysinfo') ?></a>
				
				<textarea readonly="readonly" wrap="off">
WordPress Version:      <?php echo get_bloginfo('version') . "\n"; ?>
PHP Version:            <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:          <?php echo mysql_get_server_info() . "\n"; ?>
Web Server:             <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress URL:          <?php echo get_bloginfo('wpurl') . "\n"; ?>
Home URL:               <?php echo get_bloginfo('url') . "\n"; ?>

Content Directory:      <?php echo WP_CONTENT_DIR . "\n"; ?>
Content URL:            <?php echo WP_CONTENT_URL . "\n"; ?>
Plugin Directory:       <?php echo WP_PLUGIN_DIR . "\n"; ?>
Plugin URL:             <?php echo WP_PLUGIN_URL . "\n"; ?>
Uploads Directory:      <?php echo ( defined('UPLOADS') ? UPLOADS : WP_CONTENT_DIR . '/uploads' ) . "\n"; ?>

PHP cURL Support:       <?php echo (function_exists('curl_init')) ? _e('Yes', 'sysinfo') . "\n" : _e('No', 'sysinfo') . "\n"; ?>
PHP GD Support:         <?php echo (function_exists('gd_info')) ? _e('Yes', 'sysinfo') . "\n" : _e('No', 'sysinfo') . "\n"; ?>
PHP Memory Limit:       <?php echo $memory_limit . "\n"; ?>
PHP Memory Usage:       <?php echo $memory_usage . "M (" . round( $memory_usage / $memory_limit * 100, 0 ) . "%)\n"; ?>
PHP Post Max Size:      <?php echo ini_get('post_max_size') . "\n"; ?>
PHP Upload Max Size:    <?php echo ini_get('upload_max_filesize') . "\n"; ?>

WP_DEBUG:               <?php echo defined('WP_DEBUG') ? WP_DEBUG ? _e('Enabled', 'sysinfo') . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
SCRIPT_DEBUG:           <?php echo defined('SCRIPT_DEBUG') ? SCRIPT_DEBUG ? _e('Enabled', 'sysinfo') . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
SAVE_QUERIES:           <?php echo defined('SAVE_QUERIES') ? SAVE_QUERIES ? _e('Enabled', 'sysinfo') . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>

AUTOSAVE_INTERVAL:      <?php echo defined('AUTOSAVE_INTERVAL') ? AUTOSAVE_INTERVAL ? AUTOSAVE_INTERVAL . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
WP_POST_REVISIONS:      <?php echo defined('WP_POST_REVISIONS') ? WP_POST_REVISIONS ? WP_POST_REVISIONS . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>

Multi-Site Active:      <?php echo is_multisite() ? _e('Yes', 'sysinfo') . "\n" : _e('No', 'sysinfo') . "\n" ?>

Operating System:       <?php echo $browser['platform'] . "\n"; ?>
Browser:                <?php echo $browser['name'] . ' ' . $browser['version'] . "\n"; ?>
User Agent:             <?php echo $browser['user_agent'] . "\n"; ?>

Active Theme:
- <?php echo $theme->get('Name') ?> <?php echo $theme->get('Version') . "\n"; ?>
  <?php echo $theme->get('ThemeURI') . "\n"; ?>

Active Plugins:
<?php
foreach ($plugins as $plugin_path => $plugin) {
	// Only show active plugins
	if (in_array($plugin_path, $active_plugins)) {
		echo '- ' . $plugin['Name'] . ' ' . $plugin['Version'] . "\n";

		if (isset($plugin['PluginURI'])) {
			echo '  ' . $plugin['PluginURI'] . "\n";
		}

		echo "\n";
	}
}
?>
				</textarea>
			</div>
		</div>
	</div>
</div>
