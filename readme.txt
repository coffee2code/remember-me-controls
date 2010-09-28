=== Remember Me Controls ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: login, remember, remember me, cookie, session, coffee2code
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 1.0.1
Version: 1.0.1

Have "Remember Me" checked by default on logins, configure how long a login is remembered, or disable the "Remember Me" feature altogether.

== Description ==

Take control of the "Remember Me" feature for WordPress.  For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress.  If checked, WordPress will remember the login session for 14 days.  If unchecked, the login session will be remembered for only 2 days.  Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.

This plugin provides three primary controls over the behavior of the "Remember Me" feature:

* Automatically check "Remember Me" : Have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn't by default).
* Customize the duration of the "Remember Me" : Customize how long WordPress will remember a login session when "Remember Me" is checked.
* Disable "Remember Me" : Completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.


== Installation ==

1. Download the file http://coffee2code.com/wp-plugins/remember-me-controls.zip and unzip it into your /wp-content/plugins/ directory (or install via the built-in WordPress plugin installer).
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Settings" -> "Remember Me" and configure the settings


== Frequently Asked Questions ==

= How long does WordPress usually keep me logged in? =

By default, if you log in without "Remember Me" checked, WordPress keeps you logged in for up to 2 days. If you check "Remember Me", WordPress keeps you logged in for up to 14 days.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Changelog ==

= 1.0.1 =
* Fix bug where having "Remember Me" checked but having no remember me duration configured resulted in login error
* Fix bug where incorrect number of arguments were requested from the 'auth_cookie_expiration' action

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.0.1 =
Recommended bugfix release.

= 1.0 =
Initial public release!