<?php

defined( 'ABSPATH' ) or die();

class Remember_Me_Controls_Test extends WP_UnitTestCase {

	public static function setUpBeforeClass() {
		c2c_RememberMeControls::get_instance()->install();
	}

	public function setUp() {
		parent::setUp();
		c2c_RememberMeControls::get_instance()->reset_options();
	}

	public function tearDown() {
		parent::tearDown();

		// Reset options
		c2c_RememberMeControls::get_instance()->reset_options();
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	//
	//
	// HELPER FUNCTIONS
	//
	//


	protected function set_option( $settings = array() ) {
		$obj = c2c_RememberMeControls::get_instance();
		$defaults = $obj->get_options();
		$settings = wp_parse_args( (array) $settings, $defaults );
		$obj->update_option( $settings, true );
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
		$this->assertEquals( '050', c2c_RememberMeControls::get_instance()->c2c_plugin_version() );
	}

	public function test_get_version() {
		$this->assertEquals( '1.8.1', c2c_RememberMeControls::get_instance()->version() );
	}

	public function test_instance_object_is_returned() {
		$this->assertTrue( is_a( c2c_RememberMeControls::get_instance(), 'c2c_RememberMeControls' ) );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_RememberMeControls', 'get_instance' ) ) );
	}

	public function test_hooks_action_auth_cookie_expiration() {
		$this->assertNotFalse( has_action( 'auth_cookie_expiration', array( c2c_RememberMeControls::get_instance(), 'auth_cookie_expiration' ), 10, 3 ) );
	}

	public function test_hooks_action_login_head() {
		$this->assertNotFalse( has_action( 'login_head', array( c2c_RememberMeControls::get_instance(), 'add_css' ) ) );
	}

	public function test_hooks_filter_login_footer() {
		$this->assertNotFalse( has_filter( 'login_footer', array( c2c_RememberMeControls::get_instance(), 'add_js' ) ) );
	}

	public function test_option_default_for_auto_remember_me() {
		$this->assertFalse( c2c_RememberMeControls::get_instance()->get_options()['auto_remember_me'] );
	}

	public function test_option_default_for_remember_me_forever() {
		$this->assertFalse( c2c_RememberMeControls::get_instance()->get_options()['remember_me_forever'] );
	}

	public function test_option_default_for_remember_me_duration() {
		$this->assertEmpty( c2c_RememberMeControls::get_instance()->get_options()['remember_me_duration'] );
	}

	public function test_option_default_for_disable_remember_me() {
		$this->assertFalse( c2c_RememberMeControls::get_instance()->get_options()['disable_remember_me'] );
	}

	public function test_auth_cookie_expiration_is_unaffected_if_plugin_not_configured() {
		$this->assertEquals( 456, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, false ) );
	}

	public function test_auth_cookie_expiration_is_unaffected_if_remember_me_not_checked() {
		$this->set_option( array( 'remember_me_forever' => true, 'remember_me_duration' => 27 ) );
		$this->assertEquals( 456, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, false ) );
	}

	public function test_auth_cookie_expiration_if_remember_me_forever() {
		$this->set_option( array( 'remember_me_forever' => true ) );
		$this->assertEquals( 100 * YEAR_IN_SECONDS, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_remember_me_forever_has_priority_over_remember_me_duration() {
		$this->set_option( array( 'remember_me_forever' => true, 'remember_me_duration' => 200 ) );
		$this->assertEquals( 100 * YEAR_IN_SECONDS, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_if_remember_me_duration() {
		$this->set_option( array( 'remember_me_duration' => 24 * 21 ) );
		$this->assertEquals( 24 * 21 * HOUR_IN_SECONDS, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_remember_me_duration_does_not_exceed_max() {
		$this->set_option( array( 'remember_me_duration' => 24 * 365 * 101 ) ); // 101 years
		$this->assertEquals( 100 * YEAR_IN_SECONDS, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_remember_me_duration_of_0_result_in_default_expiration() {
		$this->set_option( array( 'remember_me_duration' => 0 ) );
		$this->assertEquals( 456, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_if_remember_unchecked_and_disable_remember_me() {
		$this->set_option( array( 'disable_remember_me' => true ) );
		$this->assertEquals( 2 * DAY_IN_SECONDS, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, false ) );
	}

	public function test_auth_cookie_expiration_if_remember_checked_and_disable_remember_me() {
		$this->set_option( array( 'disable_remember_me' => true ) );
		$this->assertEquals( 2 * DAY_IN_SECONDS, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, true ) );
	}

	public function test_auth_cookie_expiration_if_remember_checked_that_disable_remember_me_has_top_priority() {
		$this->set_option( array( 'disable_remember_me' => true, 'remember_me_forever' => true, 'remember_me_duration' => 33 ) );
		$this->assertEquals( 2 * DAY_IN_SECONDS, c2c_RememberMeControls::get_instance()->auth_cookie_expiration( 456, 1, true ) );
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
		c2c_RememberMeControls::get_instance()->reset_options();
		$this->test_login_form_defaults_with_disable_remember_me( 'sidebar_login_widget_form_args' );
		c2c_RememberMeControls::get_instance()->reset_options();
		$this->test_login_form_defaults_with_auto_remember_me( 'sidebar_login_widget_form_args' );
		c2c_RememberMeControls::get_instance()->reset_options();
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
		$options     = c2c_RememberMeControls::get_instance()->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}

	public function test_uninstall_deletes_option() {
		$option_name = c2c_RememberMeControls::SETTING_NAME;
		$options     = c2c_RememberMeControls::get_instance()->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'auto_remember_me' => '1' ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_RememberMeControls::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
