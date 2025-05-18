=== Plug Conflict Detector ===
Contributors: monzur
Tags: plugin conflict, troubleshoot, fatal error, plugin crash, plug conflict detector
Requires at least: 6.6
Author URI: https://profiles.wordpress.org/monzur/
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
Plugin URI: https://wordpress.org/plugins/plug-conflict-detector
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Detect plugin conflicts and automatically log or deactivate problematic plugins that cause site crashes or fatal errors.

== Description ==

Plug Conflict Detector is a lightweight utility plugin that helps WordPress administrators detect and log plugin-related conflicts, especially those that trigger fatal errors and take down a site. It's designed to help quickly identify the conflicting plugin and provide log visibility via the dashboard — even automatically reverting the conflict where possible.

== Features ==

* Detects fatal errors on plugin **activation or update**
* Logs conflicts in a custom database table
* Automatically **deactivates** a plugin that causes fatal errors
* Displays conflict logs in the **Conflict Logs** page
* Monitors `debug.log` for uncaught fatal errors
* Compatible with **WooCommerce** and **Elementor**
* Removes `.maintenance` file if left behind
* No setup needed
* Lightweight and does not consume high server resources

== How It Works ==

When you activate or update a plugin, Plug Conflict Detector:

1. Monitors the frontend for fatal output or crash
2. Scans `debug.log` for fatal errors
3. Logs any issues into the database
4. Optionally auto-deactivates the plugin to keep your site live

== Limitations ==

* Cannot detect **non-fatal conflicts** like JavaScript or CSS issues yet
* Does not currently support **multisite** installations
* Relies on `debug.log` being enabled for certain error tracking
* Requires plugins to be activated or updated to detect problems — cannot detect static/existing issues

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/plug-conflict-detector/`, or install the plugin through the WordPress plugin screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress
3. Go to ** Conflict Logs** to view logs

== Frequently Asked Questions ==

= Can this plugin detect all plugin conflicts? =

It detects plugin conflicts that trigger **fatal errors** or make your site crash. It cannot yet detect visual or JavaScript-related issues.

= Does it work on existing plugins? =

No. It monitors conflicts only when you activate or update plugins.

= Can I use it on a multisite? =

Not yet. Support for multisite may be added in the future.

== Screenshots ==

1. Conflict Logs admin page showing detected issues

== Changelog ==

= 1.0.1 =
* Initial release with fatal error detection
* WooCommerce & Elementor compatibility
* Conflict logging and auto-deactivation
* Maintenance file cleanup

== Upgrade Notice ==

= 1.0.1 =
First public release.
