<?php
/*
Plugin Name: PMPro Strong Passwords
Version: 0.1
Plugin URI: http://www.paidmembershipspro.com/add-ons/plugins-on-github/require-strong-passwords/
Description: Force users to submit strong passwords.
Version: .2_alpha
Author: Scott Sousa
Author URI: http://slocumstudio.com
Text Domain: pmpro-strong-passwords
Domain Path: /languages
*/

/**
 * Load text domain
 * pmprosp_load_plugin_text_domain
 */
function pmprosp_load_plugin_text_domain() {
	load_plugin_textdomain( 'pmpro-strong-passwords', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'pmprosp_load_plugin_text_domain' );

// Default password blacklist
$pmprosp_password_blacklist = array( 'administrator', '@dministrator', '@dmin', 'test', 'tester' );

function pmprosp_password_strength_scripts_and_styles() {
	global $pmpro_pages, $pmprosp_password_blacklist;
	// Only load on checkout page
	if ( is_page( $pmpro_pages['checkout'] ) && ! is_user_logged_in() ) {
		wp_enqueue_script( 'password-strength-meter' );
		wp_enqueue_script( 'pmprosp-js', plugins_url( 'js/jquery.pmpro-strong-passwords.js', __FILE__ ), array( 'jquery' ), false, true  );
		wp_enqueue_style( 'pmprosp-css', plugins_url( 'css/pmpro-strong-passwords.css', __FILE__ ) );
	}

	wp_localize_script(
		'password-strength-meter',
		'pwsL10n',
		array(
			'empty'    => _x( 'Strength indicator', 'password strength', 'pmpro-strong-passwords' ),
			'short'    => _x( 'Very weak', 'password strength', 'pmpro-strong-passwords' ),
			'bad'      => _x( 'Weak', 'password strength', 'pmpro-strong-passwords' ),
			'good'     => _x( 'Medium', 'password strength', 'pmpro-strong-passwords' ),
			'strong'   => _x( 'Strong', 'password strength', 'pmpro-strong-passwords' ),
			'mismatch' => _x( 'Mismatch', 'password strength', 'pmpro-strong-passwords' ),
			'password_tooltip' => _x( 'Note: A good password is at least 8 characters long and contain upper and lowercase letters, a number, and a special character', 'password tooltip', 'pmpro-strong-passwords' ),
			'allow_weak' => apply_filters( 'pmprosp_allow_weak_passwords', false ),
			'progressbar_bg_color' => apply_filters( 'pmprosp_progressbar_bg_color', '#aaaaaa' ),
			'display_progressbar' => apply_filters( 'pmprosp_display_progressbar', true ),
			'display_password_strength' => apply_filters( 'pmprosp_display_password_strength', true ),
			'display_password_tooltip' => apply_filters( 'pmprosp_display_password_tooltip', true ),
			'password_blacklist' => json_encode( apply_filters( 'pmprosp_password_blacklist', $pmprosp_password_blacklist ) ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'pmprosp_password_strength_scripts_and_styles' );

/**
 * This function checks to make sure the user has submitted a strong password
 * by checking for length, lowercase/uppercase, numbers, special characters, and matching username.
 */
function pmpro_strong_password_check( $pmpro_continue_registration ) {
	//only bother checking if there are no errors so far
	if( ! $pmpro_continue_registration )
		return $pmpro_continue_registration;

	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];

	// no password (existing user is checking out)
	if( empty( $password ) )
		return $pmpro_continue_registration;

	// Check for length (8 characters)
	if ( strlen( $password ) < 8 ) {
		pmpro_setMessage( esc_html__( 'Your password must be at least 8 characters long.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	// Check for username match	
	if ( $password == $username ) {
		pmpro_setMessage( esc_html__( 'Your password must not match your username.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	// Check for containing username
	if ( strpos( $password, $username ) !== false ) {
		pmpro_setMessage( esc_html__( 'Your password must not contain your username.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	// Check for lowercase
	if ( ! preg_match( '/[a-z]/', $password ) ) {
		pmpro_setMessage( esc_html__( 'Your password must contain at least 1 lowercase letter.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	// Check for uppercase
	if ( ! preg_match( '/[A-Z]/', $password ) ) {
		pmpro_setMessage( __( 'Your password must contain at least 1 uppercase letter.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	// Check for numbers
	if ( ! preg_match( '/[0-9]/', $password ) ) {
		pmpro_setMessage( esc_html__( 'Your password must contain at least 1 number.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	// Check for special characters
	if ( ! preg_match( '/[\W]/', $password ) ) {
		pmpro_setMessage( esc_html__( 'Your password must contain at least 1 special character.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	// If we've passed all of the above, return the current continue registration flag.
	return $pmpro_continue_registration;
}
// Leaving this logic here if user's want to bring this back int future versions.
// add_filter( 'pmpro_registration_checks', 'pmpro_strong_password_check' );

function pmprosp_pmpro_checkout_after_password() {
	?>
	<div id="pmprosp-container"></div>
	<?php
}
// load as early as possible in case there are uses of filter
add_filter( 'pmpro_checkout_after_password', 'pmprosp_pmpro_checkout_after_password', 1 );
