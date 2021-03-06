=== WordPress Starter ===

Contributors: comprock,saurabhd,subharanjan
Donate link: https://axelerant.com/about-axelerant/donate/
Tags: t, b, d
Requires at least: 3.9.2
Tested up to: 4.3.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

TBD


== Description ==

WordPress Starter does TBD.

[youtube https://www.youtube.com/watch?v=TBD]

**[Video introduction](https://www.youtube.com/watch?v=TBD)**

The WordPress KEYWORD plugin, WordPress Starter, TBD.

= Primary Features =

* Ajax based processing screen
* API
* Settings export/import
* Settings screen

= WordPress Starter Premium =

[Buy WordPress Starter Premium](https://store.axelerant.com/downloads/wordpress-starter-premium-wordpress-plugin/) plugin for WordPress.

= Primary Premium Features =

* TBD

[Buy WordPress Starter Premium](https://store.axelerant.com/downloads/wordpress-starter-premium-wordpress-plugin/) plugin for WordPress.

= Shortcodes =

* TBD

= Theme Functions =

* TBD

= Additional Features =

* TBD

= Shortcode Examples =

* TBD

= Shortcode and Widget Options =

**General**

* Enable Paging? - For `[wps_widget_list]`
	* Disable – Don't display paging
	* Enable – display paging before and after post entries
	* Before – display paging only before post entries
	* After – display paging only after post entries

**Testing**

* Debug Mode - Bypass Ajax controller to handle posts_to_import directly for testing purposes.
* Posts to Import - A CSV list of post ids to import, like "1,2,3".
* Skip Importing Posts - A CSV list of post ids to not import, like "1,2,3".
* Import Limit - Useful for testing import on a limited amount of posts. 0 or blank means unlimited.

**Compatibility & Reset**

* Export Settings – These are your current settings in a serialized format. Copy the contents to make a backup of your settings.
* Import Settings – Paste new serialized settings here to overwrite your current configuration.
* Remove Plugin Data on Deletion? - Delete all WordPress Starter data and options from database on plugin deletion
* Reset to Defaults? – Check this box to reset options to their defaults


== Installation ==

= Requirements =

* PHP 5.3+ [Read notice](https://nodedesk.zendesk.com/hc/en-us/articles/202331041) – Since 2.16.0
* WordPress 3.9.2+
* [jQuery 1.10+](https://nodedesk.zendesk.com/hc/en-us/articles/202244022)

= Install Methods =

* Through WordPress Admin > Plugins > Add New, Search for "WordPress Starter"
	* Find "WordPress Starter"
	* Click "Install Now" of "WordPress Starter"
* Download [`wordpress-starter.zip`](http://downloads.wordpress.org/plugin/wordpress-starter.zip) locally
	* Through WordPress Admin > Plugins > Add New
	* Click Upload
	* "Choose File" `wordpress-starter.zip`
	* Click "Install Now"
* Download and unzip [`wordpress-starter.zip`](http://downloads.wordpress.org/plugin/wordpress-starter.zip) locally
	* Using FTP, upload directory `wordpress-starter` to your website's `/wp-content/plugins/` directory

= Activation Options =

* Activate the "WordPress Starter" plugin after uploading through WordPress Admin > Plugins

= Usage =

1. Edit options through WordPress Admin > Settings > WordPress Starter 
1. Process posts via WordPress Admin > Tools > WordPress Starter

= Upgrading =

* Through WordPress
	* Via WordPress Admin > Dashboard > Updates, click "Check Again"
	* Select plugins for update, click "Update Plugins"
* Using FTP
	* Download and unzip [`wordpress-starter.zip`](http://downloads.wordpress.org/plugin/wordpress-starter.zip) locally
	* Upload directory `wordpress-starter` to your website's `/wp-content/plugins/` directory
	* Be sure to overwrite your existing `wordpress-starter` folder contents


== Frequently Asked Questions ==

= Most Common Issues =

* Got `Parse error: syntax error, unexpected T_STATIC…`? See [Most Axelerant Plugins Require PHP 5.3+](https://nodedesk.zendesk.com/hc/en-us/articles/202331041)
* [Change styling or debug CSS](https://nodedesk.zendesk.com/hc/en-us/articles/202243372)
* [Debug theme and plugin conflicts](https://nodedesk.zendesk.com/hc/en-us/articles/202330781)

= Still Stuck or Want Something Done? Get Support! =

1. [Knowledge Base](https://nodedesk.zendesk.com/hc/en-us/sections/200861112) - review and submit bug reports and enhancement requests
1. [Support on WordPress](http://wordpress.org/support/plugin/wordpress-starter) - ask questions and review responses
1. [Contribute Code](https://github.com/michael-cannon/wordpress-starter/blob/master/CONTRIBUTING.md)
1. [Beta Testers Needed](http://store.axelerant.com/become-beta-tester/) - provide feedback and direction to plugin development
1. [Old Plugin Versions](http://wordpress.org/plugins/wordpress-starter/developers/)


== Screenshots ==

1. TBD

[gallery]


== Changelog ==

See [CHANGELOG](https://github.com/michael-cannon/wordpress-starter/blob/master/CHANGELOG.md)


== Upgrade Notice ==

= 1.0.0 =

* Initial release


== Notes ==

* When plugin is uninstalled, all data and settings are deleted if "Remove Plugin Data on Deletion" is checked in Settings


== API ==

* Read the [WordPress Starter API](https://github.com/michael-cannon/wordpress-starter/blob/master/API.md).


== Localization ==

You can translate this plugin into your own language if it's not done so already. The localization file `wordpress-starter.pot` can be found in the `languages` folder of this plugin. After translation, please [send the localized file](https://axelerant.com/contact-axelerant/) for plugin inclusion.

**[How do I localize?](https://nodedesk.zendesk.com/hc/en-us/articles/202294892)**


== Thank You ==

Current development by [Axelerant](https://axelerant.com/about-axelerant/).
