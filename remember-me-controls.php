<?php
/**
 * @package Remember_Me_Controls
 * @author Scott Reilly
 * @version 0.9
 */
/*
Plugin Name: Remember Me Controls
Version: 0.9
Plugin URI: http://coffee2code.com/wp-plugins/remember-me-controls
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: remember-me-controls
Description: Have "Remember Me" checked by default on logins, configure how long a login is remembered, or disable the "Remember Me" feature.

Take control of the "Remember Me" feature for WordPress.  For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress.
If checked, WordPress will remember the login session for 14 days.  If unchecked, the login session will be remembered for only 2 days.  Once a
login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.

This plugin provides three primary controls over the behavior of the "Remember Me" feature:

* Automatically check "Remember Me" : Have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn't by default).
* Customize the duration of the "Remember Me" : Customize how long WordPress will remember a login session when "Remember Me" is checked.
* Disable "Remember Me" : Completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.

Compatible with WordPress 2.8+, 2.9+, 3.0+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://coffee2code.com/wp-plugins/remember-me-controls.zip and unzip it into your
/wp-content/plugins/ directory (or install via the built-in WordPress plugin installer).
2. Activate the plugin through the 'Plugins' admin menu in WordPress

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

if ( !class_exists('RememberMeControls') ) :

class RememberMeControls {
	var $admin_options_name = 'c2c_remember_me_controls';
	var $nonce_field = 'update-remember_me_controls';
	var $textdomain = 'remember-me-controls';
	var $show_admin = true;	// Change this to false if you don't want the plugin's admin page shown.
	var $config = array();
	var $options = array(); // Don't use this directly
	var $plugin_basename = '';
	var $plugin_name = '';
	var $short_name = '';

	/**
	 * Handles installation tasks, such as ensuring plugin options are instantiated and saved to options table.
	 *
	 * @return void
	 */
	function RememberMeControls() {
		$this->plugin_basename = plugin_basename(__FILE__);

		add_action('init', array(&$this, 'init'));
		add_action('activate_' . str_replace(trailingslashit(WP_PLUGIN_DIR), '', __FILE__), array(&$this, 'install'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('auth_cookie_expiration', array(&$this, 'auth_cookie_expiration'));
		add_action('login_head', array(&$this, 'login_head'));
	}

	/**
	 * Handles installation tasks, such as ensuring plugin options are instantiated and saved to options table.
	 *
	 * @return void
	 */
	function install() {
		$this->options = $this->get_options();
		update_option($this->admin_options_name, $this->options);
	}

	/**
	 * Handles actions to be hooked to 'init' action, such as loading text domain and loading plugin config data array.
	 *
	 * @return void
	 */
	function init() {
		load_plugin_textdomain( $this->textdomain, false, basename(dirname(__FILE__)) );
		$this->load_config();
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * @return void
	 */
	function load_config() {
		$this->plugin_name = __('Remember Me Controls', $this->textdomain);
		$this->short_name = __('Remember Me Controls', $this->textdomain);

		$this->config = array(
			'auto_remember_me' => array('input' => 'checkbox', 'default' => false,
					'label' => __('Have the "Remember Me" checkbutton automatically checked?', $this->textdomain),
					'help' => __('If checked, then the "Remember Me" checkbox will automatically be checked when visiting the login form.', $this->textdomain)),
			'disable_remember_me' => array('input' => 'checkbox', 'default' => false,
					'label' => __('Disable the "Remember Me" feature?', $this->textdomain),
					'help' => __('If checked, then the "Remember Me" checkbox will not appear on login and the login session will last no longer than 24 hours.', $this->textdomain)),
			'remember_me_duration' => array('input' => 'text', 'default' => '',
					'label' => __('Remember Me duration', $this->textdomain),
					'help' => __('The number of hours a login with "Remember Me" checked will last.  Initially, this is set to XXX, which is WordPress\'s default of two weeks.', $this->textdomain))
		);
	}

	/**
	 * Registers the admin options page and the Settings link.
	 *
	 * @return void
	 */
	function admin_menu() {
		if ( $this->show_admin && current_user_can('manage_options') ) {
			add_filter( 'plugin_action_links_' . $this->plugin_basename, array(&$this, 'plugin_action_links') );
			add_options_page($this->plugin_name, $this->short_name, 'manage_options', $this->plugin_basename, array(&$this, 'options_page'));
		}
	}

	/**
	 * Adds a 'Settings' link to the plugin action links.
	 *
	 * @param int $limit The default limit value for the current posts query.
	 * @return array Links associated with a plugin on the admin Plugins page
	 */
	function plugin_action_links( $action_links ) {
		$settings_link = '<a href="options-general.php?page='.$this->plugin_basename.'">' . __('Settings', $this->textdomain) . '</a>';
		array_unshift( $action_links, $settings_link );
		return $action_links;
	}

	/**
	 * Returns either the buffered array of all options for the plugin, or
	 * obtains the options and buffers the value.
	 *
	 * @return array The options array for the plugin (which is also stored in $this->options if !$with_options).
	 */
	function get_options() {
		if ( !empty($this->options) ) return $this->options;
		// Derive options from the config
		$options = array();
		foreach ( array_keys($this->config) as $opt ) {
			$options[$opt] = $this->config[$opt]['default'];
		}
		$this->options = wp_parse_args(get_option($this->admin_options_name), $options);
		return $this->options;
	}

	/**
	 * Saves updates to options, if being POSTed.  In either case, also return the options.
	 *
	 * @return array $options The array of options.
	 */
	function maybe_save_options() {
		$options = $this->get_options();
		// See if user has submitted form
		if ( isset($_POST['submitted']) ) {
			check_admin_referer($this->nonce_field);
			foreach ( array_keys($options) AS $opt ) {
				$options[$opt] = htmlspecialchars(stripslashes($_POST[$opt]));
				$input = $this->config[$opt]['input'];
				if ( ($input == 'checkbox') && !$options[$opt] )
					$options[$opt] = 0;
				if ( $this->config[$opt]['datatype'] == 'array' ) {
					if ( $input == 'text' )
						$options[$opt] = explode(',', str_replace(array(', ', ' ', ','), ',', $options[$opt]));
					else
						$options[$opt] = array_map('trim', explode("\n", trim($options[$opt])));
				}
				elseif ( $this->config[$opt]['datatype'] == 'hash' ) {
					if ( !empty($options[$opt]) ) {
						$new_values = array();
						foreach ( explode("\n", $options[$opt]) AS $line ) {
							list($shortcut, $text) = array_map('trim', explode("=>", $line, 2));
							if ( !empty($shortcut) ) $new_values[str_replace('\\', '', $shortcut)] = str_replace('\\', '', $text);
						}
						$options[$opt] = $new_values;
					}
				}
			}
			// Remember to put all the other options into the array or they'll get lost!
			update_option($this->admin_options_name, $options);
			$this->options = $options;
			echo "<div id='message' class='updated fade'><p><strong>" . __('Settings saved.', $this->textdomain) . '</strong></p></div>';
		}
		return $options;
	}

	/**
	 * Outputs the markup for an option's form field (and surrounding markup)
	 *
	 * @param string $opt The name/key of the option.
	 * @return void
	 */
	function display_option( $opt ) {
		$options = $this->get_options();
		$input = $this->config[$opt]['input'];
		if ( $input == 'none' ) continue;
		$label = $this->config[$opt]['label'];
		$value = $options[$opt];
		if ( $input == 'checkbox' ) {
			$checked = ($value == 1) ? 'checked=checked ' : '';
			$value = 1;
		} else {
			$checked = '';
		};
		if ( $this->config[$opt]['datatype'] == 'array' ) {
			if ( !is_array($value) )
				$value = '';
			else {
				if ( $input == 'textarea' || $input == 'inline_textarea' )
					$value = implode("\n", $value);
				else
					$value = implode(', ', $value);
			}
		} elseif ( $this->config[$opt]['datatype'] == 'hash' ) {
			if ( !is_array($value) )
				$value = '';
			else {
				$new_value = '';
				foreach ($value AS $shortcut => $replacement) {
					$new_value .= "$shortcut => $replacement\n";
				}
				$value = $new_value;
			}
		}
		echo "<tr valign='top'>";
		if ( $input == 'textarea' ) {
			echo "<td colspan='2'>";
			if ( $label ) echo "<strong>$label</strong><br />";
			echo "<textarea name='$opt' id='$opt' {$this->config[$opt]['input_attributes']}>" . esc_html($value) . '</textarea>';
		} else {
			echo "<th scope='row'>$label</th><td>";
			if ( $input == "inline_textarea" )
				echo "<textarea name='$opt' id='$opt' {$this->config[$opt]['input_attributes']}>" . esc_html($value) . '</textarea>';
			elseif ( $input == 'select' ) {
				echo "<select name='$opt' id='$opt'>";
				foreach ($this->config[$opt]['options'] as $sopt) {
					$selected = $value == $sopt ? " selected='selected'" : '';
					echo "<option value='$sopt'$selected>$sopt</option>";
				}
				echo "</select>";
			} else {
				$tclass = ($input == 'short_text') ? 'small-text' : 'regular-text';
				if ($input == 'short_text') $input = 'text';
				echo "<input name='$opt' type='$input' id='$opt' value='" . esc_attr($value) .
					"' class='$tclass' $checked {$this->config[$opt]['input_attributes']} />";
			}
		}
		if ( $this->config[$opt]['help'] ) {
			echo "<br /><span style='color:#777; font-size:x-small;'>";
			echo $this->config[$opt]['help'];
			echo "</span>";
		}
		echo "</td></tr>";
	}

	/**
	 * Outputs the options page for the plugin, and saves user updates to the
	 * options.
	 *
	 * @return void
	 */
	function options_page() {
		$options = $this->maybe_save_options();
		$action_url = $_SERVER['PHP_SELF'] . '?page=' . $this->plugin_basename;
		$logo = plugins_url(basename($_GET['page'], '.php') . '/c2c_minilogo.png');

		echo "<div class='wrap'>";
		echo "<div class='icon32' style='width:44px;'><img src='$logo' alt='" . esc_attr__('A plugin by coffee2code', $this->textdomain) . "' /><br /></div>";
		echo '<h2>' . __('Remember Me Controls Settings', $this->textdomain) . '</h2>';
		echo '<p>' . __('Take control of the "Remember Me" feature for WordPress.  For those unfamiliar, "Remember Me" is a checkbox present when logging into WordPress.  If checked, WordPress will remember the login session for 14 days.  If unchecked, the login session will be remembered for only 2 days.  Once a login session expires, WordPress will require you to log in again if you wish to continue using the admin section of the site.', $this->textdomain) . '</p>';
		echo '<p>' . __('This plugin provides three primary controls over the behavior of the "Remember Me" feature:', $this->textdomain) . '</p>';
		echo '<ul>';
		echo '<li>' . __('Automatically check "Remember Me" : Have the "Remember Me" checkbox automatically checked when the login form is loaded (it isn\'t by default).', $this->textdomain) . '</li>';
		echo '<li>' . __('Customize the duration of the "Remember Me" : Customize how long WordPress will remember a login session when "Remember Me" is checked.', $this->textdomain) . '</li>';
		echo '<li>' . __('Disable "Remember Me" : Completely disable the feature, preventing the checkbox from appearing and restricting all login sessions to one day.', $this->textdomain) . '</li>';
		echo '</ul>';

		echo "<form name='remember_me_controls' action='$action_url' method='post'>";
		wp_nonce_field($this->nonce_field);
		echo '<table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform form-table"><tbody>';
		foreach ( array_keys($options) as $opt ) {
			$this->display_option($opt);
		}
		$txt = esc_attr__('Save Changes', $this->textdomain);
		echo <<<END
			</tbody></table>
			<input type="hidden" name="submitted" value="1" />
			<div class="submit"><input type="submit" name="Submit" class="button-primary" value="{$txt}" /></div>
		</form>
			</div>
END;

		echo <<<END
		<style type="text/css">
			#c2c {
				text-align:center;
				color:#888;
				background-color:#ffffef;
				padding:5px 0 0;
				margin-top:12px;
				border-style:solid;
				border-color:#dadada;
				border-width:1px 0;
			}
			#c2c div {
				margin:0 auto;
				padding:5px 40px 0 0;
				width:45%;
				min-height:40px;
				background:url('$logo') no-repeat top right;
			}
			#c2c span {
				display:block;
				font-size:x-small;
			}
		</style>
		<div id='c2c' class='wrap'>
			<div>
END;
		$c2c = '<a href="http://coffee2code.com" title="coffee2code.com">' . __('Scott Reilly, aka coffee2code', $this->textdomain) . '</a>';
		echo sprintf(__('This plugin brought to you by %s.', $this->textdomain), $c2c);
		echo '<span><a href="http://coffee2code.com/donate" title="' . esc_attr__('Please consider a donation', $this->textdomain) . '">' .
		__('Did you find this plugin useful?', $this->textdomain) . '</a></span>';
		echo '</div></div>';
	}

	/**
	 * Outputs CSS within style tags
	 *
	 * @return void
	 */
	function add_css() {
		$options = $this->get_options();
		if ( $options['disable_remember_me'] ) {
			echo <<<CSS
		<style type="text/css">
		.forgetmenot { display:none; }
		</style>
CSS;
		}
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
			$use_jquery = file_exists(ABSPATH . $jquery_path);
			if ( $use_jquery ) :
				$jquery_js = esc_attr(site_url($jquery_path));
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
		$this->add_js(); // Would rather do this in the footer, but no such hooks exist.
	}

	/**
	 * Possibly modifies the authorization cookie expiration duration based on plugin configuration.
	 *
	 * @return void
	 */
	function auth_cookie_expiration( $expiration, $user_id, $remember ) {
		$options = $this->get_options();
		if ( $options['disable_remember_me'] ) // Regardless of checkbutton state, if 'remember me' is disabled, use the non-remember-me duration
			$expiration = 172800;
		elseif ( $remember )
			$expiration = intval($options['remember_me_duration']);
		return $expiration;
	}

} // end RememberMeControls

endif; // end if !class_exists()

if ( class_exists('RememberMeControls') )
	new RememberMeControls();

?>