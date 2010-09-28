<?php
/**
 * @package Remember_Me_Controls
 * @author Scott Reilly
 * @version 1.0.1
 */
/*
Plugin Name: Remember Me Controls
Version: 1.0.1
Plugin URI: http://coffee2code.com/wp-plugins/remember-me-controls/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: remember-me-controls
Description: Have "Remember Me" checked by default on logins, configure how long a login is remembered, or disable the "Remember Me" feature altogether.

Compatible with WordPress 2.8+, 2.9+, 3.0+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/remember-me-controls/

*/

/*
Copyright (c) 2009-2010 by Scott Reilly (aka coffee2code)

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


if ( !class_exists( 'c2c_RememberMeControls' ) ) :

require_once( 'c2c-plugin.php' );

class c2c_RememberMeControls extends C2C_Plugin_016 {

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function c2c_RememberMeControls() {
		$this->C2C_Plugin_016( '1.0.1', 'remember-me-controls', 'c2c', __FILE__, array() );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * @return void
	 */
	function load_config() {
		$this->name = __( 'Remember Me Controls', $this->textdomain );
		$this->menu_name = __( 'Remember Me', $this->textdomain );

		$this->config = array(
			'auto_remember_me' => array( 'input' => 'checkbox', 'default' => false,
					'label' => __( 'Have the "Remember Me" checkbox automatically checked?', $this->textdomain ),
					'help' => __( 'If checked, then the "Remember Me" checkbox will automatically be checked when visiting the login form.', $this->textdomain ) ),
			'remember_me_duration' => array( 'input' => 'shorttext', 'default' => '', 'datatype' => 'int',
					'label' => __( 'Remember Me duration', $this->textdomain ),
					'help' => __( 'The number of <strong>hours</strong> a login with "Remember Me" checked will last.  If not provided, then the WordPress default of 336 (i.e. two weeks) will be used.', $this->textdomain ) ),
			'disable_remember_me' => array( 'input' => 'checkbox', 'default' => false,
					'label' => __( 'Disable the "Remember Me" feature?', $this->textdomain ),
					'help' => __( 'If checked, then the "Remember Me" checkbox will not appear on login and the login session will last no longer than 24 hours.', $this->textdomain ) ),

		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions and filters.
	 *
	 * @return void
	 */
	function register_filters() {
		add_action( 'auth_cookie_expiration', array( &$this, 'auth_cookie_expiration' ), 10, 3 );
		add_action( 'login_head', array( &$this, 'login_head' ) );
		add_action( $this->get_hook( 'post_display_option' ), array( &$this, 'maybe_add_hr' ) );
	}

	/**
	 * Outputs the text above the setting form
	 *
	 * @return void (Text will be echoed.)
	 */
	function options_page_description() {
		parent::options_page_description( __( 'Remember Me Controls Settings', $this->textdomain ) );
		echo '<p>' . __( 'Take control of the "Remember Me" feature for WordPress.  For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress.  If checked, WordPress will remember the login session for 14 days.  If unchecked, the login session will be remembered for only 2 days.  Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.', $this->textdomain ) . '</p>';
		echo '<p>' . __( 'This plugin provides three primary controls over the behavior of the "Remember Me" feature:', $this->textdomain ) . '</p>';
		echo '<ul class="c2c-plugin-list">';
		echo '<li>' . __( 'Automatically check "Remember Me" : Have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn\'t by default).', $this->textdomain ) . '</li>';
		echo '<li>' . __( 'Customize the duration of the "Remember Me" : Customize how long WordPress will remember a login session when "Remember Me" is checked.', $this->textdomain ) . '</li>';
		echo '<li>' . __( 'Disable "Remember Me" : Completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.', $this->textdomain ) . '</li>';
		echo '</ul>';
	}

	/**
	 * Outputs CSS within style tags
	 *
	 * @return void
	 */
	function add_css() {
		$options = $this->get_options();
		if ( $options['disable_remember_me'] )
			echo '<style type="text/css">.forgetmenot { display:none; }</style>' . "\n";
	}

	/**
	 * Outputs JavaScript within script tags
	 *
	 * @return void
	 */
	function add_js() {
		$options = $this->get_options();
		if ( $options['auto_remember_me'] && !$options['disable_remember_me'] ) {
			// This kinda sucks, but the login page doesn't facilitate use of some core code (i.e. wp_enqueue_script()).
			// Bringing in jQuery for this tiny thing seems like such an overhead.  The direct javascript method is much lighter, but brittle.
			$jquery_path = '/' . WPINC . '/js/jqueryx/jquery.js';
			$use_jquery = file_exists( ABSPATH . $jquery_path );
			if ( $use_jquery ) :
				$jquery_js = esc_attr( site_url( $jquery_path ) );
				echo <<<JS
		<script type="text/javascript" src="$jquery_js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#rememberme').attr('checked',true);
			});
		</script>

JS;
			else :
				// This approach will clobber (or be clobbered by) any other onload attached handler.
				// Alternatively, a setTimeout() could be used, as here:
				//		setTimeout( function(){ try{document.getElementById('rememberme').checked = true;}catch(e){} } );
				echo <<<JS
		<script type="text/javascript">
			window.onload=function(){
				try{document.getElementById('rememberme').checked = true;}catch(e){}
			}
		</script>

JS;
			endif;
		}
	}

	/**
	 * Invokes the CSS and JS output functions within the head of the login page.
	 *
	 * @return void
	 */
	function login_head() {
		$this->add_css();
		$this->add_js(); // Would rather do this in the footer, but no such hook exists.
	}

	/**
	 * Possibly modifies the authorization cookie expiration duration based on plugin configuration.
	 *
	 * @param int $expiration The time interval, in seconds, before auth_cookie expiration
	 * @param int $user_id User ID
	 * @param bool $remember If the remember_me_duration should be used instead of the default
	 * @return void
	 */
	function auth_cookie_expiration( $expiration, $user_id, $remember ) {
		$options = $this->get_options();
		if ( $options['disable_remember_me'] ) // Regardless of checkbutton state, if 'remember me' is disabled, use the non-remember-me duration
			$expiration = 172800;
		elseif ( $remember && ( (int) $options['remember_me_duration'] > 2 ) )
			$expiration = (int) $options['remember_me_duration'];
		return $expiration;
	}

	/**
	 * Output a hr (or rather, the equivalent of such) after a particular option
	 *
	 * @param string $opt The option name
	 * @return void (Text may possibly be echoed.)
	 */
	function maybe_add_hr( $opt ) {
		if ( 'remember_me_duration' == $opt )
			echo "</tr><tr><td colspan='2'><div class='hr'>&nbsp;</div></td>\n";
	}
} // end class

$GLOBALS['c2c_remember_me_controls'] = new c2c_RememberMeControls();

endif; // end if !class_exists()

?>