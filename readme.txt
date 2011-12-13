=== Remember Me Controls ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: login, remember, remember me, cookie, session, coffee2code
Requires at least: 3.1
Tested up to: 3.3
Stable tag: 1.2
Version: 1.2

Have "Remember Me" checked by default on logins, configure how long a login is remembered, or disable the "Remember Me" feature altogether.


== Description ==

Take control of the "Remember Me" feature for WordPress.  For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress.  If checked, WordPress will remember the login session for 14 days.  If unchecked, the login session will be remembered for only 2 days.  Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.

This plugin provides three primary controls over the behavior of the "Remember Me" feature:

* Automatically check "Remember Me" : Have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn't checked by default).
* Customize the duration of the "Remember Me" : Customize how long WordPress will remember a login session when "Remember Me" is checked.
* Disable "Remember Me" : Completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.

NOTE: WordPress remembers who you are based on cookies stored in your web browser. If you use a different web browser, clear your cookies, use a browser on a different machine, or uninstall/reinstall your browser then you will have to log in again since WordPress will not be able to locate the cookies needed to identify you.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/remember-me-controls/) | [Plugin Directory Page](http://wordpress.org/extend/plugins/remember-me-controls/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Unzip `remember-me-controls.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Settings" -> "Remember Me" and configure the settings


== Frequently Asked Questions ==

= How long does WordPress usually keep me logged in? =

By default, if you log in without "Remember Me" checked, WordPress keeps you logged in for up to 2 days. If you check "Remember Me", WordPress keeps you logged in for up to 14 days.

= How can I set the session duration to less than an hour? =

You can't (and probably shouldn't). With a session length of less than an hour you risk timing out users too quickly.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.
2. A screenshot of the login form with "Remember Me" checked by default
3. A screenshot of the login form with "Remember Me" removed


== Changelog ==

= 1.2 =
* Add setting 'remember_me_forever' to allow user to forego having to make up a large number
* Set a max expiration of 100 years in the future to prevent error if user supplies a high enough number to exceed the year 9999
* Use pure JS instead of jQuery for checking checkbox
* Hook into 'login_footer' action to output JS
* Change hooking of 'login_head' to output CSS rather than calling login_head()
* Remove login_head()
* Allow setting minimum duration of 1 hour (as was documented)
* Remove support for global $c2c_remember_me_controls variable
* Update plugin framework to 031
* Note compatibility through WP 3.3+
* Drop compatibility with versions of WP older than 3.1
* Create 'lang' subdirectory and move .pot file into it
* Regenerate .pot
* Update screenshot
* Add screenshots 2 and 3
* Add more description, FAQ question
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

= 1.1 =
* Fix bug with missing remember_me_duration setting conversion from hours to seconds
* Update plugin framework to version v023
* Save a static version of itself in class variable $instance
* Deprecate use of global variable $c2c_remember_me_controls to store instance
* Fix to properly register activation and uninstall hooks
* Add __construct(), activation(), uninstall()
* Explicitly declare all class functions public
* Note compatibility through WP 3.2+
* Drop compatibility with versions of WP older than 3.0
* Minor code formatting changes (spacing)
* Minor readme.txt formatting changes
* Fix plugin homepage and author links in description in readme.txt
* Update copyright date (2011)

= 1.0.1 =
* Fix bug where having "Remember Me" checked but having no remember me duration configured resulted in login error
* Fix bug where incorrect number of arguments were requested from the 'auth_cookie_expiration' action

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.2 =
Recommended update. Highlights: added new setting to remember logins forever; misc improvements and minor bug fixes; updated plugin framework; compatibility is now for WP 3.1 - 3.3+.

= 1.1 =
Recommended upgrade! Fixed bug relating to value conversion from hours to seconds; fix for proper activation; noted compatibility through WP 3.2; dropped compatibility with versions of WP 3.0; deprecated use of global updated plugin framework; and more.

= 1.0.1 =
Recommended bugfix release.

= 1.0 =
Initial public release!