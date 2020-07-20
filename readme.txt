=== Remember Me Controls ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: login, remember, remember me, cookie, session, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.4
Stable tag: 1.8.1

Have "Remember Me" checked by default on the login page and configure how long a login is remembered. Or disable the feature altogether.


== Description ==

Take control of the "Remember Me" feature for WordPress by having it enabled by default, customize how long users are remember, or disable this built-in feature by default.

For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress. If checked, WordPress will remember the login session for 14 days. If unchecked, the login session will be remembered for only 2 days. Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.

This plugin provides three primary controls over the behavior of the "Remember Me" feature:

* Automatically check "Remember Me" : The ability to have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn't checked by default).
* Customize the duration of the "Remember Me" : The ability to customize how long WordPress will remember a login session when "Remember Me" is checked.
* Disable "Remember Me" : The ability to completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.

NOTE: WordPress remembers who you are based on cookies stored in your web browser. If you use a different web browser, clear your cookies, use a browser on a different machine, or uninstall/reinstall your browser then you will have to log in again since WordPress will not be able to locate the cookies needed to identify you.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/remember-me-controls/) | [Plugin Directory Page](https://wordpress.org/plugins/remember-me-controls/) | [GitHub](https://github.com/coffee2code/remember-me-controls/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or download and unzip `remember-me-controls.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Settings" -> "Remember Me" and configure the settings


== Frequently Asked Questions ==

= How long does WordPress usually keep me logged in? =

By default, if you log in without "Remember Me" checked, WordPress keeps you logged in for up to 2 days. If you check "Remember Me", WordPress keeps you logged in for up to 14 days.

= How can I set the session duration to less than an hour? =

You can't (and probably shouldn't). With a session length of less than an hour you risk timing out users too quickly.

= Is this plugin GDPR-compliant? =

Yes. This plugin does not collect, store, or disseminate any information from any users or site visitors.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.
2. A screenshot of the login form with "Remember Me" checked by default
3. A screenshot of the login form with "Remember Me" removed


== Changelog ==

= 1.8.1 (2020-01-01) =
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)
* Change: Tweak changelog formatting for v1.8 release

= 1.8 (2019-06-28) =
Highlights:

* This release is a minor update that verifies compatibility through WordPress 5.2+ and makes minor behind-the-scenes improvements.

Details:

* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Update plugin framework to 049
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
    * 048:
    * When resetting options, delete the option rather than setting it with default values
    * Prevent double "Settings reset" admin notice upon settings reset
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* Unit tests:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Ensure settings get reset before assigning newly set values
    * Fix: Fix broken unit test
* Change: Note compatibility through WP 5.2+
* Change: Add link to plugin's page in Plugin Directory to README.md
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

= 1.7 (2018-04-19) =
* New: Add support for BuddyPress Login widget
* New: Add support for Sidebar Login plugin (https://wordpress.org/plugins/sidebar-login/)
* New: Add support for Login Widget With Shortcode plugin (https://wordpress.org/plugins/login-sidebar-widget/)
* New: Change login form defaults according to plugin settings
* Change: Update plugin framework to 047
    * 047:
    * Don't save default setting values to database on install
    * Change "Cheatin', huh?" error messages to "Something went wrong.", consistent with WP core
    * Note compatibility through WP 4.9+
    * Drop compatibility with version of WP older than 4.7
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`
    * Note compatibility through WP 4.7+
    * Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database
    * 044:
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes
    * Add  `add_option()` to add a new option to the plugin's configuration
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names
    * Change: Refactor `get_option_names()`
    * 043:
    * Disregard invalid lines supplied as part of hash option value
    * 042:
    * Update `disable_update_check()` to check for HTTP and HTTPS for plugin update check API URL
    * Translate "Donate" in footer message
* Change: Store setting name in class constant
* New: Add README.md
* New: Add FAQ indicating that the plugin is GDPR-compliant
* Change: Unit tests:
    * Add and improve unit tests
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Drop compatibility with versions of WP older than 4.7
* Change: Update copyright date (2018)
* Change: Update installation instruction to prefer built-in installer over .zip file

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/remember-me-controls/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.8.1 =
Trivial update: noted compatibility through WP 5.3+ and updated copyright date (2020)

= 1.8 =
Minor update: tweaked plugin initialization, updated plugin framework to version 049, noted compatibility through WP 5.2+, created CHANGELOG.md to store historical changelog outside of readme.txt, and updated copyright date (2019)

= 1.7 =
Recommended update: added support for BuddyPress Login widget, Sidebar Login plugin, and Login Widget With Shortcode plugin; updated plugin framework to version 047; compatibility is now with WP 4.7-4.9+; updated copyright date (2018).

= 1.6 =
Minor update: improved support for localization; verified compatibility through WP 4.4; removed compatibility with WP earlier than 4.1; updated copyright date (2016)

= 1.5 =
Minor update: add unit tests; updated plugin framework to 039; noted compatibility through WP 4.1+; updated copyright date (2015); added plugin icon

= 1.4 =
Recommended update: updated plugin framework; compatibility now WP 3.6-3.8+

= 1.3 =
Minor update. Highlights: updated plugin framework; noted compatibility through WP 3.5+; and more.

= 1.2 =
Recommended update. Highlights: added new setting to remember logins forever; misc improvements and minor bug fixes; updated plugin framework; compatibility is now for WP 3.1 - 3.3+.

= 1.1 =
Recommended upgrade! Fixed bug relating to value conversion from hours to seconds; fix for proper activation; noted compatibility through WP 3.2; dropped compatibility with versions of WP 3.0; deprecated use of global updated plugin framework; and more.

= 1.0.1 =
Recommended bugfix release.

= 1.0 =
Initial public release!
