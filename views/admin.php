<?php

// Get a reference to the SysInfo instance
$sysinfo = SysInfo::get_instance();

// Now get information from the environment
$theme = wp_get_theme();
$browser = $sysinfo->get_browser();
$plugins = $sysinfo->get_all_plugins();
$active_plugins = $sysinfo->get_active_plugins();
$memory_limit = ini_get( 'memory_limit' );
$memory_usage = $sysinfo->get_memory_usage();
$all_options = $sysinfo->get_all_options();
$all_options_serialized = serialize( $all_options );
$all_options_bytes = round( mb_strlen( $all_options_serialized, '8bit' ) / 1024, 2 );
$all_options_transients = $sysinfo->get_transients_in_options( $all_options );
?>

<div id="sysinfo">
	<div class="wrap">
		<div class="icon32">
			<img src="<?php echo SYSINFO_PLUGIN_URL ?>/images/sysinfo.png" />
		</div><!-- /.icon32 -->
		
		<h2 class="title"><?php _e(' SysInfo', 'sysinfo' ); ?></h2><!-- /.title -->
		
		<div class="clear"></div><!-- /.clear -->

		<div class="section">
			<div class="header">
				<?php _e( 'System Information', 'sysinfo' ); ?>
			</div><!-- /.header -->
			
			<div class="inside">
				<a class="button-primary" href="#" onclick="window.open('<?php echo SYSINFO_PLUGIN_URL ?>/views/phpinfo.php', 'PHPInfo', 'width=800, height=600, scrollbars=1'); return false;"><?php _e('PHP Info', 'sysinfo') ?></a>
				
				<textarea readonly="readonly" wrap="off">
<?php _e( 'WordPress Version:', 'sysinfo' ); ?>      <?php echo get_bloginfo('version') . "\n"; ?>
<?php _e( 'PHP Version:', 'sysinfo' ); ?>            <?php echo PHP_VERSION . "\n"; ?>
<?php _e( 'MySQL Version:', 'sysinfo' ); ?>          <?php echo mysql_get_server_info() . "\n"; ?>
<?php _e( 'Web Server:', 'sysinfo' ); ?>             <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

<?php _e( 'WordPress URL:', 'sysinfo' ); ?>          <?php echo get_bloginfo('wpurl') . "\n"; ?>
<?php _e( 'Home URL: ', 'sysinfo' ); ?>              <?php echo get_bloginfo('url') . "\n"; ?>

<?php _e( 'Content Directory:', 'sysinfo' ); ?>      <?php echo WP_CONTENT_DIR . "\n"; ?>
<?php _e( 'Content URL:', 'sysinfo' ); ?>            <?php echo WP_CONTENT_URL . "\n"; ?>
<?php _e( 'Plugins Directory:', 'sysinfo' ); ?>      <?php echo WP_PLUGIN_DIR . "\n"; ?>
<?php _e( 'Plugins URL:', 'sysinfo' ); ?>            <?php echo WP_PLUGIN_URL . "\n"; ?>
<?php _e( 'Uploads Directory:', 'sysinfo' ); ?>      <?php echo (defined('UPLOADS') ? UPLOADS : WP_CONTENT_DIR . '/uploads') . "\n"; ?>

<?php _e( 'Cookie Domain:', 'sysinfo' ); ?>          <?php echo defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN ? COOKIE_DOMAIN . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
<?php _e( 'Multi-Site Active:', 'sysinfo' ); ?>      <?php echo is_multisite() ? _e('Yes', 'sysinfo') . "\n" : _e('No', 'sysinfo') . "\n" ?>

<?php _e( 'PHP cURL Support:', 'sysinfo' ); ?>       <?php echo (function_exists('curl_init')) ? _e('Yes', 'sysinfo') . "\n" : _e('No', 'sysinfo') . "\n"; ?>
<?php _e( 'PHP GD Support:', 'sysinfo' ); ?>         <?php echo (function_exists('gd_info')) ? _e('Yes', 'sysinfo') . "\n" : _e('No', 'sysinfo') . "\n"; ?>
<?php _e( 'PHP Memory Limit:', 'sysinfo' ); ?>       <?php echo $memory_limit . "\n"; ?>
<?php _e( 'PHP Memory Usage:', 'sysinfo' ); ?>       <?php echo $memory_usage . "M (" . round($memory_usage / $memory_limit * 100, 0) . "%)\n"; ?>
<?php _e( 'PHP Post Max Size:', 'sysinfo' ); ?>      <?php echo ini_get('post_max_size') . "\n"; ?>
<?php _e( 'PHP Upload Max Size:', 'sysinfo' ); ?>    <?php echo ini_get('upload_max_filesize') . "\n"; ?>

<?php _e( 'WP Options Count:', 'sysinfo' ); ?>       <?php echo count($all_options) . "\n"; ?>
<?php _e( 'WP Options Size:', 'sysinfo' ); ?>        <?php echo $all_options_bytes . "kb\n" ?>
<?php _e( 'WP Options Transients:', 'sysinfo' ); ?>  <?php echo count($all_options_transients) . "\n"; ?>

WP_DEBUG:             	<?php echo defined('WP_DEBUG') ? WP_DEBUG ? _e('Enabled', 'sysinfo') . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
SCRIPT_DEBUG:           <?php echo defined('SCRIPT_DEBUG') ? SCRIPT_DEBUG ? _e('Enabled', 'sysinfo') . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
SAVEQUERIES:            <?php echo defined('SAVEQUERIES') ? SAVEQUERIES ? _e('Enabled', 'sysinfo') . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
AUTOSAVE_INTERVAL:      <?php echo defined('AUTOSAVE_INTERVAL') ? AUTOSAVE_INTERVAL ? AUTOSAVE_INTERVAL . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>
WP_POST_REVISIONS:      <?php echo defined('WP_POST_REVISIONS') ? WP_POST_REVISIONS ? WP_POST_REVISIONS . "\n" : _e('Disabled', 'sysinfo') . "\n" : _e('Not set', 'sysinfo') . "\n" ?>

<?php _e( 'Operating System:', 'sysinfo' ); ?>      <?php echo $browser['platform'] . "\n"; ?>
<?php _e( 'Browser:', 'sysinfo' ); ?>                 <?php echo $browser['name'] . ' ' . $browser['version'] . "\n"; ?>
<?php _e( 'User Agent:', 'sysinfo' ); ?>              <?php echo $browser['user_agent'] . "\n"; ?>

<?php _e( 'Active Theme:', 'sysinfo' ); ?>
<?php echo "\n\r"; ?>
- <?php echo $theme->get('Name') ?> <?php echo $theme->get('Version') . "\n"; ?>
  <?php echo $theme->get('ThemeURI') . "\n"; ?>

<?php _e( 'Active Plugins:', 'sysinfo' ); ?>
<?php echo "\n\r"; ?>
<?php
foreach( $plugins as $plugin_path => $plugin ) {

	// Only show active plugins
	if( in_array( $plugin_path, $active_plugins ) ) {
	
		echo '- ' . $plugin['Name'] . ' ' . $plugin['Version'] . "\n";

		if( isset( $plugin['PluginURI'] ) ) {
			echo '  ' . $plugin['PluginURI'] . "\n";
		} // end if

		echo "\n";
	} // end if
} // end foreach
?>
				</textarea>
			</div><!-- /.inside -->
		</div><!-- /.section -->
	</div><!-- /.wrap -->
</div><!-- /#sysinfo -->