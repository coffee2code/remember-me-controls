=== Remember Me Controls ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: login, remember, remember me, cookie, session, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 6.2
Stable tag: 1.9.1

Have "Remember Me" checked by default on the login page and configure how long a login is remembered. Or disable the feature altogether.


== Description ==

Take control of the "Remember Me" login feature for WordPress by having it enabled by default, customize how long users are remembered, or disable this built-in feature by default.

For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress. If checked, WordPress will remember the login session for 14 days. If unchecked, the login session will be remembered for only 2 days. Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.

This plugin provides three primary controls over the behavior of the "Remember Me" feature:

* Automatically check "Remember Me" : The ability to have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn't checked by default).
* Customize the duration of the "Remember Me" : The ability to customize how long WordPress will remember a login session when "Remember Me" is checked, either forever or a customizable number of hours.
* Disable "Remember Me" : The ability to completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.

NOTE: WordPress remembers who you are based on cookies stored in your web browser. If you use a different web browser, clear your cookies, use a browser on a different machine, or uninstall/reinstall (and possibly even just restarting) your browser then you will have to log in again since WordPress will not be able to locate the cookies needed to identify you.

= Compatibility =

Other than the plugins listed below, compatibility has not been tested or attempted for any other third-party plugins that provide their own login widgets or login handling.

Special handling has been added to provide compatibility with the following plugins:

* [BuddyPress](https://wordpress.org/plugins/buddypress/) (in particular, its "Log in" widget)
* [Sidebar Login](https://wordpress.org/plugins/sidebar-login/)
* [Login Widget With Shortcode](https://wordpress.org/plugins/login-sidebar-widget/)

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/remember-me-controls/) | [Plugin Directory Page](https://wordpress.org/plugins/remember-me-controls/) | [GitHub](https://github.com/coffee2code/remember-me-controls/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or install the plugin code inside the plugins directory for your site (typically `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Settings" -> "Remember Me" and configure the settings


== Frequently Asked Questions ==

= How long does WordPress usually keep me logged in? =

By default, if you log in without "Remember Me" checked, WordPress keeps you logged in for up to 2 days. If you check "Remember Me" (without this plugin active), WordPress keeps you logged in for up to 14 days.

= Why am I being asked to log in again even though I've configured the plugin to remember me forever (or an otherwise long enough duration that hasn't been met yet)? =

WordPress remembers who you are based on cookies stored in your web browser. If you use a different web browser, clear your cookies, use a browser on a different machine, the site owner invalidates all existing login sessions, or you uninstall/reinstall (and possibly even just restart) your browser then you will have to log in again since WordPress will not be able to locate the cookies needed to identify you.

Also, if you changed the remember me duration but hadn't logged out after having done so, that particular login session would still be affected by the default (or previously configured) duration.

= How can I set the session duration to less than an hour? =

You can't (and probably shouldn't). With a session length of less than an hour you risk timing out users too quickly.

= Do changes to the remember me duration take effect for all current login sessions? =

No. The duration for which a login cookie is valid is defined within the cookie when it gets created (which is when you log in). Changing the setting for the remember me duration will only affect cookies created thereafter. You can log out and then log back in if you want the newly configured remember me duration to apply to your session. More precisely, the changes take effect for all *new* logins, which can happen after a preexisting login session expires, the user logs out, or the user's cookies are cleared in their browser (manually or automatically).

= How can I make the plugin configuration changes I've made take effect immediately? =

As explained in the previous FAQ entry, changes to the plugin's settings only take effect the next time a user logs in. Existing login sessions will abide by the remember me duration configured at the time they logged into their current session.

The login cookies for a user session can become invalidated by the visitor by logging out or clearing their cookies.

Here are some options to force all active login sessions to abide by the current login session duration:
* [Manually refresh](https://developer.wordpress.org/reference/functions/wp_salt/) your site's authentication keys and salts.
* Use WP-CLI to [regenerate salts](https://developer.wordpress.org/cli/commands/config/shuffle-salts/).
* Use the plugin [WPForce Logout](https://wordpress.org/plugins/wp-force-logout/) (to force session logouts) or [Salt Shaker](https://wordpress.org/plugins/salt-shaker/) (to regenerate salts). _Note: Plugins are merely suggestions and not necessarily recommendations._

= What plugins are this plugin compatible with? =

Special handling has been added to provide compatibility with the following plugins:

* [BuddyPress](https://wordpress.org/plugins/buddypress/) (in particular, its "Log in" widget)
* [Sidebar Login](https://wordpress.org/plugins/sidebar-login/)
* [Login Widget With Shortcode](https://wordpress.org/plugins/login-sidebar-widget/)

= Is this plugin GDPR-compliant? =

Yes. This plugin does not collect, store, or disseminate any information from any users or site visitors.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.
2. A screenshot of the login form with "Remember Me" checked by default
3. A screenshot of the login form with "Remember Me" removed


== Changelog ==

= 1.9.1 (2021-02-13) =
* Fix: Add missing textdomain. Props @kittmedia.
* Change: Enhance a FAQ answer to make clear that an existing login session will not be affected by an update to the remember me duration (must log in again)
* Change: Note compatibility through WP 5.6+
* Change: Update copyright date (2021)

= 1.9 (2020-07-20) =

Highlights:

* This minor release adds support for using commas when setting the remember me duration, adds HTML5 compliance when supported by the theme, improves settings help text and other documentation, updates its plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, updates compatibility to be WP 4.9 through 5.4+, and other minor behind-the-scenes tweaks.

Details:

* New: Add HTML5 compliance by omitting `type` attribute to 'script' and 'style' tags when the theme supports 'html5'
* New: Add help text to settings whose value change won't take effect until subsequent logins regarding as much
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add items to it)
* Change: Allow use of commas in user-submitted value for `remember_me_duration` setting
* Change: Update JavaScript coding syntax
* Change; Add help text to the top of the settings page
* Change: Use a superscript for footnote asterisk and extract markup from translatable string
* Change: Update plugin framework to 051
    * 051:
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
    * 050:
    * Allow a hash entry to literally have '0' as a value without being entirely omitted when saved
    * Output donation markup using `printf()` rather than using string concatenation
    * Update copyright date (2020)
    * Note compatibility through WP 5.4+
    * Drop compatibility with version of WP older than 4.9
* Change: Tweak text on help tab
* Change: Add a few new FAQ entries and amend another
* Change: Include another example scenario in which login cookies could be invalidated
* Change: Tweak verbiage of various documentation
* Change: Note compatibility through WP 5.4+
* Change: Drop compatibility with versions of WP older than 4.9
* Change: Update links to coffee2code.com to be HTTPS
* Unit tests:
    * New: Add `get_default_hooks()` as a helper method for getting the default hooks
    * New: Add tests for `add_css()`, `add_js()`, `help_tabs_content()`, `maybe_add_hr()`, `options_page_description()`
    * New: Add test for setting name
    * New: Add test for hook registering
    * Change: Store plugin instance in test object to simplify referencing it
    * Change: Remove unnecessary unregistering of hooks in `tearDown()`
    * Change: Remove duplicative `reset_options()` call
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests
* Change: Updated screenshot

= 1.8.1 (2020-01-01) =
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)
* Change: Tweak changelog formatting for v1.8 release

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/remember-me-controls/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.9.1 =
Trivial update: added missing translation textdomain, noted compatibility through WP 5.6+, and updated copyright date (2021)

= 1.9 =
Minor update: allowed commas in numerical input, improved documentation, added HTML5 compliance when supported by the theme, updated plugin framework, added TODO.md file, updated a few URLs to be HTTPS, expanded unit testing, updated compatibility to be WP 4.9 through 5.4+, and minor behind-the-scenes tweaks.

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
