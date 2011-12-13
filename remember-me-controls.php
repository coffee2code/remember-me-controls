<?php
/**
 * @package Remember_Me_Controls
 * @author Scott Reilly
 * @version 1.2
 */
/*
Plugin Name: Remember Me Controls
Version: 1.2
Plugin URI: http://coffee2code.com/wp-plugins/remember-me-controls/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: remember-me-controls
Domain Path: /lang/
Description: Have "Remember Me" checked by default on logins, configure how long a login is remembered, or disable the "Remember Me" feature altogether.

Compatible with WordPress 3.1+, 3.2+, 3.3+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/remember-me-controls/
*/

/*
Copyright (c) 2009-2012 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


if ( ! version_compare( $wp_version, '3.0.999', '>' ) ) :

	add_action( 'admin_notices', create_function( '', "echo '<div class=\"error\"><p>" .
		__( 'The plugin Remember Me Controls requires at least WordPress 3.1 to function. Please upgrade your WordPress or deactivate the plugin.', 'remember-me-controls' ) ."</p></div>';" )
	);

elseif ( ! class_exists( 'c2c_RememberMeControls' ) ) :

require_once( 'c2c-plugin.php' );

class c2c_RememberMeControls extends C2C_Plugin_031 {

	public static $instance;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->c2c_RememberMeControls();
	}

	public function c2c_RememberMeControls() {
		// Be a singleton
		if ( ! is_null( self::$instance ) )
			return;

		parent::__construct( '1.2', 'remember-me-controls', 'c2c', __FILE__, array() );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
		self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * This can be overridden.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function uninstall() {
		delete_option( 'c2c_remember_me_controls' );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * @return void
	 */
	public function load_config() {
		$this->name      = __( 'Remember Me Controls', $this->textdomain );
		$this->menu_name = __( 'Remember Me', $this->textdomain );

		$this->config = array(
			'auto_remember_me' => array(
					'input' => 'checkbox', 'default' => false,
					'label' => __( 'Have the "Remember Me" checkbox automatically checked?', $this->textdomain ),
					'help' => __( 'If checked, then the "Remember Me" checkbox will automatically be checked when visiting the login form.', $this->textdomain ) ),
			'remember_me_forever' => array(
					'input' => 'checkbox', 'default' => false,
					'label' => __( 'Remember forever*?', $this->textdomain ),
					'help' => __( 'Should user be remembered forever if "Remember Me" is checked? If so, then the "Remember Me duration" value below is ignored.<br /><small style="font-style:italic;">(*Not quite forever; actually it\'s 100 years.)</small>', $this->textdomain ) ),
			'remember_me_duration' => array(
					'input' => 'shorttext', 'default' => '', 'datatype' => 'int',
					'label' => __( 'Remember Me duration', $this->textdomain ),
					'help' => __( 'The number of <strong>hours</strong> a login with "Remember Me" checked will last.  If not provided, then the WordPress default of 336 (i.e. two weeks) will be used. Do not include any commas.<br />NOTE: This value is ignored if "Remember forever?" is checked above.', $this->textdomain ) ),
			'disable_remember_me' => array(
					'input' => 'checkbox', 'default' => false,
					'label' => __( 'Disable the "Remember Me" feature?', $this->textdomain ),
					'help' => __( 'If checked, then the "Remember Me" checkbox will not appear on login and the login session will last no longer than 24 hours.', $this->textdomain ) )
		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions
	 * and filters.
	 *
	 * @return void
	 */
	public function register_filters() {
		add_action( 'auth_cookie_expiration', array( &$this, 'auth_cookie_expiration' ), 10, 3 );
		add_action( 'login_head',             array( &$this, 'add_css' ) );
		add_filter( 'login_footer',           array( &$this, 'add_js' ) );
		add_action( $this->get_hook( 'post_display_option' ), array( &$this, 'maybe_add_hr' ) );
	}

	/**
	 * Outputs the text above the setting form
	 *
	 * @return void (Text will be echoed.)
	 */
	public function options_page_description() {
		parent::options_page_description( __( 'Remember Me Controls Settings', $this->textdomain ) );
		echo '<p>' . __( 'Take control of the "Remember Me" feature for WordPress.  For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress.  If checked, WordPress will remember the login session for 14 days.  If unchecked, the login session will be remembered for only 2 days.  Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.', $this->textdomain ) . '</p>';
		echo '<p>' . __( 'This plugin provides three primary controls over the behavior of the "Remember Me" feature:', $this->textdomain ) . '</p>';
		echo '<ul class="c2c-plugin-list">';
		echo '<li>' . __( 'Automatically check "Remember Me" : Have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn\'t checked by default).', $this->textdomain ) . '</li>';
		echo '<li>' . __( 'Customize the duration of the "Remember Me" : Customize how long WordPress will remember a login session when "Remember Me" is checked.', $this->textdomain ) . '</li>';
		echo '<li>' . __( 'Disable "Remember Me" : Completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.', $this->textdomain ) . '</li>';
		echo '</ul>';
	}

	/**
	 * Outputs CSS within style tags
	 *
	 * @return void
	 */
	public function add_css() {
		$options = $this->get_options();
		if ( $options['disable_remember_me'] )
			echo '<style type="text/css">.forgetmenot { display:none; }</style>' . "\n";
	}

	/**
	 * Outputs JavaScript within script tags
	 *
	 * @return void
	 */
	public function add_js() {
		$options = $this->get_options();
		if ( $options['auto_remember_me'] && ! $options['disable_remember_me'] ) {
			echo <<<JS
		<script type="text/javascript">
			var checkbox = document.getElementById('rememberme');
			if ( null != checkbox )
				checkbox.checked = true;
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
	 * @param int $expiration The time interval, in seconds, before auth_cookie expiration
	 * @param int $user_id User ID
	 * @param bool $remember If the remember_me_duration should be used instead of the default
	 * @return void
	 */
	public function auth_cookie_expiration( $expiration, $user_id, $remember ) {
		$options = $this->get_options();
		$max_expiration = 100 * 365 * 24 * 60 * 60; // 100 years
		if ( $options['disable_remember_me'] ) // Regardless of checkbutton state, if 'remember me' is disabled, use the non-remember-me duration
			$expiration = 172800; // 48 hours
		elseif ( $remember && $options['remember_me_forever'] )
			$expiration = $max_expiration;
		elseif ( $remember && ( (int) $options['remember_me_duration'] >= 1 ) )
			$expiration = (int) $options['remember_me_duration'] * 60 * 60;

		// In reality, we just need to prevent the user from specifying an expiration that would
		// exceed the year 9999. But a fixed max expiration is simpler and quite reasonable.
		if ( $expiration > $max_expiration )
			$expiration = $max_expiration;

		return $expiration;
	}

	/**
	 * Output a hr (or rather, the equivalent of such) after a particular option
	 *
	 * @param string $opt The option name
	 * @return void (Text may possibly be echoed.)
	 */
	public function maybe_add_hr( $opt ) {
		if ( 'remember_me_duration' == $opt )
			echo "</tr><tr><td colspan='2'><div class='hr'>&nbsp;</div></td>\n";
	}
} // end class

// To access the object instance, use: c2c_RememberMeControls::$instance
new c2c_RememberMeControls();

endif; // end if !class_exists()

?>