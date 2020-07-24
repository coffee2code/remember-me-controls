<?php
/**
 * Plugin Name: Remember Me Controls
 * Version:     1.8.1
 * Plugin URI:  https://coffee2code.com/wp-plugins/remember-me-controls/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: remember-me-controls
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Have "Remember Me" checked by default on the login page and configure how long a login is remembered. Or disable the feature altogether.
 *
 * Compatible with WordPress 4.9+ through 5.4+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/remember-me-controls/
 *
 * @package Remember_Me_Controls
 * @author  Scott Reilly
 * @version 1.8.1
 */

/*
	Copyright (c) 2009-2020 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_RememberMeControls' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

final class c2c_RememberMeControls extends c2c_RememberMeControls_Plugin_050 {

	/**
	 * Name of plugin's setting.
	 *
	 * @var string
	 */
	const SETTING_NAME = 'c2c_remember_me_controls';

	/**
	 *  The one true instance.
	 *
	 * @var c2c_RememberMeControls
	 * @access private
	 */
	private static $instance;

	/**
	 * Get singleton instance.
	 *
	 * @since 1.4
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct( '1.8.1', 'remember-me-controls', 'c2c', __FILE__, array() );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 1.1
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * @since 1.1
	 */
	public static function uninstall() {
		delete_option( self::SETTING_NAME );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	public function load_config() {
		$this->name      = __( 'Remember Me Controls', 'remember-me-controls' );
		$this->menu_name = __( 'Remember Me', 'remember-me-controls' );

		$this->config = array(
			'auto_remember_me' => array(
				'input'    => 'checkbox',
				'default'  => false,
				'label'    => __( 'Have the "Remember Me" checkbox automatically checked?', 'remember-me-controls' ),
				'help'     => __( 'If checked, then the "Remember Me" checkbox will automatically be checked when visiting the login form.', 'remember-me-controls' ),
			),
			'remember_me_forever' => array(
				'input'    => 'checkbox',
				'default'  => false,
				'label'    => sprintf(
					/* translators: %s: Markup for a character indicating a footnote. */
					__( 'Remember forever%s?', 'remember-me-controls' ),
					'<sup style="color:red;">*</sup>'
				),
				'help'     => __( 'Should user be remembered forever if "Remember Me" is checked? If so, then the "Remember Me duration" value below is ignored.', 'remember-me-controls' )
					. sprintf( '<br><em><sup style="color:red;">*</sup>%s</em>', __( 'Not quite forever; actually it\'s 100 years.', 'remember-me-controls' ) )
					. sprintf( '<br><em>%s</em>', __( 'NOTE: A change of this value only takes effect on subsequent logins.', 'remember-me-controls' ) ),
			),
			'remember_me_duration' => array(
				'input'    => 'shorttext',
				'default'  => '',
				'datatype' => 'int',
				'label'    => __( 'Remember Me duration', 'remember-me-controls' ),
				'help'     => __( 'The number of <strong>hours</strong> a login with "Remember Me" checked will last. If not provided, then the WordPress default of 336 (i.e. two weeks) will be used. Do not include any commas. This value is ignored if "Remember forever?" is checked above.', 'remember-me-controls' )
					. sprintf( '<br><em>%s</em>', __( 'NOTE: A change of this value only takes effect on subsequent logins.', 'remember-me-controls' ) ),
			),
			'disable_remember_me' => array(
				'input'    => 'checkbox',
				'default'  => false,
				'label'    => __( 'Disable the "Remember Me" feature?', 'remember-me-controls' ),
				'help'     => __( 'If checked, then the "Remember Me" checkbox will not appear on login and the login session will last no longer than 24 hours.', 'remember-me-controls' ),
			),
		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions
	 * and filters.
	 */
	public function register_filters() {
		add_action( 'auth_cookie_expiration',                 array( $this, 'auth_cookie_expiration' ), 10, 3 );
		add_action( 'login_head',                             array( $this, 'add_css' ) );
		add_filter( 'login_footer',                           array( $this, 'add_js' ) );
		add_action( $this->get_hook( 'post_display_option' ), array( $this, 'maybe_add_hr' ) );
		add_filter( 'login_form_defaults',                    array( $this, 'login_form_defaults' ) );

		// Compat for BuddyPress Login Widget.
		add_action( 'bp_before_login_widget_loggedout',       array( $this, 'add_css' ) );
		add_action( 'bp_after_login_widget_loggedout',        array( $this, 'add_js' ) );

		// Compat for Login Widget With Shortcode plugin.
		add_filter( 'pre_option_login_afo_rem',               '__return_empty_string' );

		// Compat for Sidebar Login plugin.
		add_filter( 'sidebar_login_widget_form_args',         array( $this, 'compat_for_sidebar_login' ) );
		add_action( 'wp_ajax_sidebar_login_process',          array( $this, 'compat_for_sidebar_login_ajax_handler' ), 1 );
		add_action( 'wp_ajax_nopriv_sidebar_login_process',   array( $this, 'compat_for_sidebar_login_ajax_handler' ), 1 );
	}

	/**
	 * Outputs the text above the setting form.
	 *
	 * @param string $localized_heading_text Optional. Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		parent::options_page_description( __( 'Remember Me Controls Settings', 'remember-me-controls' ) );

		echo '<p>' . __( 'Take control of the "Remember Me" login feature for WordPress by customizing its behavior or disabling it altogether.', 'remember-me-controls' ) . '</p>';
		echo '<p>' . __( 'For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress. If checked, by default WordPress will remember the login session for 14 days. If unchecked, the login session will be remembered for only 2 days. Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.' ) . '</p>';
		echo '<p>' . __( 'NOTE: WordPress remembers who you are based on cookies stored in your web browser. If you use a different web browser, clear your cookies, use a browser on a different machine, the site owner invalidates all existing login sessions, or you uninstall/reinstall (and possibly even just restart) your browser then you will have to log in again since WordPress will not be able to locate the cookies needed to identify you.', 'remember-me-controls' ) . '</p>';
	}

	/**
	 * Configures help tabs content.
	 *
	 * @since 1.4
	 */
	public function help_tabs_content( $screen ) {
		$screen->add_help_tab( array(
			'id'      => $this->id_base . '-' . 'about',
			'title'   => __( 'About', 'remember-me-controls' ),
			'content' =>
				'<p>' . __( 'Take control of the "Remember Me" login feature for WordPress by customizing its behavior or disabling it altogether.', 'remember-me-controls' ) . '</p>' .
				'<p>' . __( 'For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress. If checked, by default WordPress will remember the login session for 14 days. If unchecked, the login session will be remembered for only 2 days. Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.', 'remember-me-controls' ) . '</p>' .
				'<p>' . __( 'This plugin provides three primary controls over the behavior of the "Remember Me" feature:', 'remember-me-controls' ) . '</p>' .
				'<ul class="c2c-plugin-list">' .
				'<li>' . __( 'Automatically check "Remember Me" : Have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn\'t checked by default).', 'remember-me-controls' ) . '</li>' .
				'<li>' . __( 'Customize the duration of the "Remember Me" : Customize how long WordPress will remember a login session when "Remember Me" is checked, either forever or a customizable number of hours.', 'remember-me-controls' ) . '</li>' .
				'<li>' . __( 'Disable "Remember Me" : Completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.', 'remember-me-controls' ) . '</li>' .
				'</ul>',
		) );

		parent::help_tabs_content( $screen );
	}

	/**
	 * Outputs CSS within style tags.
	 */
	public function add_css() {
		$options = $this->get_options();
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

		if ( $options['disable_remember_me'] ) {
			echo '<style' . $type_attr . '>.forgetmenot { display:none; }</style>' . "\n";
		}
	}

	/**
	 * Outputs JavaScript within script tags.
	 */
	public function add_js() {
		$options = $this->get_options();
		$type_attr = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';

		if ( $options['auto_remember_me'] && ! $options['disable_remember_me'] ) {
			echo <<<JS
		<script{$type_attr}>
			const rememberme_checkbox = document.getElementById('rememberme');
			if ( null !== rememberme_checkbox ) {
				rememberme_checkbox.checked = true;
			}
		</script>

JS;
		}
	}

	/**
	 * Possibly modifies the authorization cookie expiration duration based on
	 * plugin configuration.
	 *
	 * Minimum number of hours for the remember_me_duration is 2.
	 *
	 * @param int  $expiration The time interval, in seconds, before auth_cookie expiration.
	 * @param int  $user_id    User ID.
	 * @param bool $remember   If the remember_me_duration should be used instead of the default.
	 *
	 * @return int
	 */
	public function auth_cookie_expiration( $expiration, $user_id, $remember ) {
		$options = $this->get_options();
		$max_expiration = 100 * YEAR_IN_SECONDS; // 100 years

		if ( $options['disable_remember_me'] ) { // Regardless of checkbutton state, if 'remember me' is disabled, use the non-remember-me duration
			$expiration = 2 * DAY_IN_SECONDS;
		} elseif ( $remember && $options['remember_me_forever'] ) {
			$expiration = $max_expiration;
		} elseif ( $remember && ( (int) $options['remember_me_duration'] >= 1 ) ) {
			$expiration = (int) $options['remember_me_duration'] * HOUR_IN_SECONDS;
		}

		// In reality, we just need to prevent the user from specifying an expiration that would
		// exceed the year 9999. But a fixed max expiration is simpler and quite reasonable.
		$expiration = min( $expiration, $max_expiration );

		return $expiration;
	}

	/**
	 * Outputs a horizontal rule (or rather, the equivalent of such) after a particular option.
	 *
	 * @param string $opt The option name.
	 */
	public function maybe_add_hr( $opt ) {
		if ( 'remember_me_duration' === $opt ) {
			echo "</tr><tr><td colspan='2'><div class='hr'>&nbsp;</div></td>\n";
		}
	}

	/**
	 * Changes default login form default configuration.
	 *
	 * WordPress doesn't currently allow for the final config options to be
	 * overridden, so this may not have much practical applicability for
	 * guaranteeing conformance to plugin's settings by third-party login forms.
	 *
	 * @since 1.7
	 *
	 * @param array $defaults Default configuration options.
	 * @return array
	 */
	public function login_form_defaults( $defaults ) {
		$options = $this->get_options();

		if ( $options['auto_remember_me'] ) {
			$defaults['value_remember'] = true;
		}

		if ( $options['disable_remember_me'] ) {
			$defaults['remember']       = false;
			$defaults['value_remember'] = false;
		}

		return $defaults;
	}

	/**
	 * Modifies setting for widget provided by Sidebar Login plugin.
	 *
	 * @since 1.7
	 *
	 * @param array $args Form arguments for Sidebar Login widget.
	 * @return array
	 */
	public function compat_for_sidebar_login( $args ) {
		return $this->login_form_defaults( $args );
	}

	/**
	 * Overrides AJAX handling for Sidebar Login plugin to prevent the remember me
	 * value from being saved if the feature is disabled by this plugin.
	 *
	 * @since 1.7
	 */
	public function compat_for_sidebar_login_ajax_handler() {
		$options = $this->get_options();

		if ( $options['disable_remember_me'] ) {
			unset( $_POST['remember'] );
		}
	}

} // end class

add_action( 'plugins_loaded', array( 'c2c_RememberMeControls', 'get_instance' ) );

endif; // end if !class_exists()
