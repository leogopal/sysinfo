## SysInfo

Contributors: arcware, tommcfarlin
Tags: system, system info, system information, php info
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.1.0

Useful system information about your WordPress install.

## Description 

Use this plugin to view system information about your WordPress install. Things like the versions of WordPress, PHP, and MySQL, installed/activated plugins, the current theme, memory limit, allowable upload size, operating system, browser details, etc. This information would be very useful to many users and for people that need to provide support for their plugins and themes.

## How To Use

Once installed and activated, simply go to Tools > SysInfo in your admin; that will display your system information. Also on that page you'll see a button near the top labeled "PHP Info". Clicking this will open a new browser window that displays all PHP-related information for your system.

## Installation 

For automatic installation:

1. Login to your website and go to the Plugins section of your admin panel.
1. Click the Add New button.
1. Under Install Plugins, click the Upload link.
1. Select the plugin zip file from your computer then click the Install Now button.
1. You should see a message stating that the plugin was installed successfully.
1. Click the Activate Plugin link.

For manual installation:

1. You should have access to the server where WordPress is installed. If you don't, contact your hosting provider.
1. Copy the plugin zip file up to your server and unzip it somewhere on the file system.
1. Copy the `sysinfo` folder into the `wp-content/plugins` directory of your WordPress installation.
1. Login to your website and go to the Plugins section of your admin panel.
1. Look for "SysInfo" and click Activate.

For installation via git clone:

1. ssh to `wp-content/plugins` folder of your website
1. `git clone git://github.com/davedonaldson/WordPress-SysInfo.git sysinfo`
1. Log in to WordPress and activate the plugin.
