<?php

defined( 'ABSPATH' ) or die();

class Remember_Me_Controls_Test extends WP_UnitTestCase {

	protected $obj;

	public static function setUpBeforeClass() {
		c2c_RememberMeControls::get_instance()->install();
	}

	public function setUp() {
		parent::setUp();

		$this->obj = c2c_RememberMeControls::get_instance();

		$this->obj->reset_options();
	}

	public function tearDown() {
		parent::tearDown();

		// Reset options
		$this->obj->reset_options();
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_default_hooks() {
		return array(
			array( 'action', 'auth_cookie_expiration',               'auth_cookie_expiration' ),
			array( 'action', 'login_head',                           'add_css' ),
			array( 'filter', 'login_footer',                         'add_js' ),
			array( 'filter', 'login_form_defaults',                  'login_form_defaults' ),
			array( 'action', 'bp_before_login_widget_loggedout',     'add_css' ),
			array( 'action', 'bp_after_login_widget_loggedout',      'add_js' ),
			array( 'filter', 'pre_option_login_afo_rem',             '__return_empty_string' ),
			array( 'filter', 'sidebar_login_widget_form_args',       'compat_for_sidebar_login' ),
			array( 'action', 'wp_ajax_sidebar_login_process',        'compat_for_sidebar_login_ajax_handler', 1 ),
			array( 'action', 'wp_ajax_nopriv_sidebar_login_process', 'compat_for_sidebar_login_ajax_handler', 1 ),
		);
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	protected function set_option( $settings = array() ) {
		$defaults = $this->obj->get_options();
		$settings = wp_parse_args( (array) $settings, $defaults );
		$this->obj->update_option( $settings, true );
	}

	protected function get_javascript() {
		return <<<JS
		<script type="text/javascript">
			var checkbox = document.getElementById('rememberme');
			if ( null != checkbox )
				checkbox.checked = true;
		</script>

JS;
	}


	//
	//
	// TESTS
	//
	//


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_RememberMeControls' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_RememberMeControls_Plugin_050' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '050', $this->obj->c2c_plugin_version() );
	}

	public function test_get_version() {
		$this->assertEquals( '1.8.1', $this->obj->version() );
	}

	public function test_instance_object_is_returned() {
		$this->assertTrue( is_a( $this->obj, 'c2c_RememberMeControls' ) );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_RememberMeControls', 'get_instance' ) ) );
	}

	public function test_setting_name() {
		$this->assertEquals( 'c2c_remember_me_controls', c2c_RememberMeControls::SETTING_NAME );
	}

	/**
	 * @dataProvider get_default_hooks
	 */
	public function test_default_hooks( $hook_type, $hook, $function, $priority = 10 ) {
		$callback = 0 === strpos( $function, '__' ) ? $function : array( $this->obj, $function );

		$prio = $hook_type === 'action' ?
			has_action( $hook, $callback ) :
			has_filter( $hook, $callback );
		$this->assertNotFalse( $prio );
		if ( $priority ) {
			$this->assertEquals( $priority, $prio );
		}
	}

	public function test_option_default_for_auto_remember_me() {
		$this->assertFalse( $this->obj->get_options()['auto_remember_me'] );
	}

	public function test_option_default_for_remember_me_forever() {
		$this->assertFalse( $this->obj->get_options()['remember_me_forever'] );
	}

	public function test_option_default_for_remember_me_duration() {
		$this->assertEmpty( $this->obj->get_options()['remember_me_duration'] );
	}

	public function test_option_default_for_disable_remember_me() {
		$this->assertFalse( $this->obj->get_options()['disable_remember_me'] );
	}

	/*
	 * add_css()
	 */

	public function test_add_css_if_remember_me_disabled() {
		$this->set_option( array( 'disable_remember_me' => true ) );
		$expected = '<style type="text/css">.forgetmenot { display:none; }</style>' . "\n";

		$this->expectOutputRegex( '~^' . preg_quote( $expected ) . '$~', $this->obj->add_css() );
	}

	public function test_add_css_if_remember_me_not_disabled() {
		$this->set_option( array( 'disable_remember_me' => false ) );

		$this->expectOutputRegex( '~^$~', $this->obj->add_css() );
	}

	/*
	 * add_js()
	 */

	public function test_add_js_if_auto_remember_me_but_not_disable_remember_me() {
		$this->set_option( array( 'auto_remember_me' => true, 'disable_remember_me' => false ) );

		$this->expectOutputRegex( '~^' . preg_quote( $this->get_javascript() ) . '$~', $this->obj->add_js() );
	}

	public function test_add_js_if_not_auto_remember_me_but_disable_remember_me() {
		$this->set_option( array( 'auto_remember_me' => false, 'disable_remember_me' => true ) );

		$this->expectOutputRegex( '~^$~', $this->obj->add_js() );
	}

	public function test_add_js_if_not_auto_remember_me_and_not_disable_remember_me() {
		$this->set_option( array( 'auto_remember_me' => false, 'disable_remember_me' => false ) );

		$this->expectOutputRegex( '~^$~', $this->obj->add_js() );
	}

	public function test_add_js_if_auto_remember_me_and_disable_remember_me() {
		$this->set_option( array( 'auto_remember_me' => true, 'disable_remember_me' => true ) );

		$this->expectOutputRegex( '~^$~', $this->obj->add_js() );
	}

	/*
	 * auth_cookie_expiration()
	 */

	public function test_auth_cookie_expiration_is_unaffected_if_plugin_not_configured() {
		$this->assertEquals( 456, $this->obj->auth_cookie_expiration( 456, 1, false ) );
	}

	public function test_auth_cookie_expiration_is_unaffected_if_remember_me_not_checked() {
		$this->set_option( array( 'remember_me_forever' => true, 'remember_me_duration' => 27 ) );
		$this->assertEquals( 456, $this->obj->auth_cookie_expiration( 456, 1, false ) );
	}

	public function test_auth_cookie_expiration_if_remember_me_forever() {
		$this->set_option( array( 'remember_me_forever' => true ) );
		$this->assertEquals( 100 * YEAR_IN_SECONDS, $this->obj->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_remember_me_forever_has_priority_over_remember_me_duration() {
		$this->set_option( array( 'remember_me_forever' => true, 'remember_me_duration' => 200 ) );
		$this->assertEquals( 100 * YEAR_IN_SECONDS, $this->obj->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_if_remember_me_duration() {
		$this->set_option( array( 'remember_me_duration' => 24 * 21 ) );
		$this->assertEquals( 24 * 21 * HOUR_IN_SECONDS, $this->obj->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_remember_me_duration_does_not_exceed_max() {
		$this->set_option( array( 'remember_me_duration' => 24 * 365 * 101 ) ); // 101 years
		$this->assertEquals( 100 * YEAR_IN_SECONDS, $this->obj->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_remember_me_duration_of_0_result_in_default_expiration() {
		$this->set_option( array( 'remember_me_duration' => 0 ) );
		$this->assertEquals( 456, $this->obj->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_if_remember_unchecked_and_disable_remember_me() {
		$this->set_option( array( 'disable_remember_me' => true ) );
		$this->assertEquals( 2 * DAY_IN_SECONDS, $this->obj->auth_cookie_expiration( 456, 1, false ) );
	}

	public function test_auth_cookie_expiration_if_remember_checked_and_disable_remember_me() {
		$this->set_option( array( 'disable_remember_me' => true ) );
		$this->assertEquals( 2 * DAY_IN_SECONDS, $this->obj->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_if_remember_checked_that_disable_remember_me_has_top_priority() {
		$this->set_option( array( 'disable_remember_me' => true, 'remember_me_forever' => true, 'remember_me_duration' => 33 ) );
		$this->assertEquals( 2 * DAY_IN_SECONDS, $this->obj->auth_cookie_expiration( 456, 1, true ) );
	}

	/*
	 * login_form_defaults()
	 */

	public function test_login_form_defaults_unaffected_by_default( $filter = 'login_form_defaults' ) {
		$defaults = array(
			'remember'       => true,
			'value_remember' => false,
		);

		$this->assertEquals( $defaults, apply_filters( $filter, $defaults ) );
	}

	public function test_login_form_defaults_with_disable_remember_me( $filter = 'login_form_defaults' ) {
		$defaults = array(
			'remember'       => true,
			'value_remember' => false,
		);

		$this->set_option( array( 'disable_remember_me' => true ) );

		$new_defaults = apply_filters( $filter, $defaults );

		$this->assertFalse( $new_defaults['remember'] );
		$this->assertFalse( $new_defaults['value_remember'] );
	}

	public function test_login_form_defaults_with_auto_remember_me( $filter = 'login_form_defaults' ) {
		$defaults = array(
			'remember'       => true,
			'value_remember' => false,
		);

		$this->set_option( array( 'auto_remember_me' => true ) );

		$new_defaults = apply_filters( $filter, $defaults );

		$this->assertTrue( $new_defaults['remember'] );
		$this->assertTrue( $new_defaults['value_remember'] );
	}

	public function test_login_form_defaults_with_both( $filter = 'login_form_defaults' ) {
		$defaults = array(
			'remember'       => true,
			'value_remember' => false,
		);

		$this->set_option( array( 'auto_remember_me' => true, 'disable_remember_me' => true ) );

		$new_defaults = apply_filters( $filter, $defaults );

		$this->assertFalse( $new_defaults['remember'] );
		$this->assertFalse( $new_defaults['value_remember'] );
	}

	/*
	 * Compatibility with Sidebar Login plugin.
	 */

	public function test_sidebar_login_widget_form_args() {
		$this->test_login_form_defaults_unaffected_by_default( 'sidebar_login_widget_form_args' );
		$this->obj->reset_options();
		$this->test_login_form_defaults_with_disable_remember_me( 'sidebar_login_widget_form_args' );
		$this->obj->reset_options();
		$this->test_login_form_defaults_with_auto_remember_me( 'sidebar_login_widget_form_args' );
		$this->obj->reset_options();
		$this->test_login_form_defaults_with_both( 'sidebar_login_widget_form_args' );
	}

	public function test_compat_for_sidebar_login_ajax_handler_by_default() {
		$_POST['remember'] = true;
		do_action( 'wp_ajax_sidebar_login_process' );

		$this->assertTrue( $_POST['remember'] );
	}

	public function test_compat_for_sidebar_login_ajax_handler() {
		$this->set_option( array( 'disable_remember_me' => true ) );
		$_POST['remember'] = true;
		do_action( 'wp_ajax_sidebar_login_process' );

		$this->assertFalse( isset( $_POST['remember'] ) );
	}

	/*
	 * Compatibility with Login Widget With Shortcode plugin
	 */

	public function test_compat_for_login_widget_with_shortcode() {
		$option = 'login_afo_rem';
		update_option( $option, 'Yes' );

		$this->assertNotEquals( 'Yes', get_option( $option ) );
	}

	/*
	 * Setting handling
	 */

	public function test_does_not_immediately_store_default_settings_in_db() {
		$option_name = c2c_RememberMeControls::SETTING_NAME;
		// Get the options just to see if they may get saved.
		$options     = $this->obj->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}

	public function test_uninstall_deletes_option() {
		$option_name = c2c_RememberMeControls::SETTING_NAME;
		$options     = $this->obj->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'auto_remember_me' => '1' ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_RememberMeControls::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
