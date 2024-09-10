<?php
/**
 * Plugin Name: Paid Memberships Pro - Require Strong Passwords
 * Plugin URI: https://www.paidmembershipspro.com/add-ons/require-strong-passwords/
 * Description: Force users to submit strong passwords on checkout.
 * Version: 0.5.1
 * Author: Paid Memberships Pro
 * Author URI: https://www.paidmembershipspro.com
 * Text Domain: pmpro-strong-passwords
 * Domain Path: /languages
 * License: GPL-3.0
 */

use ZxcvbnPhp\Zxcvbn;

define( 'PMPROSP_VERSION', '0.5.1' );

/**
 * Load text domain
 * pmprosp_load_plugin_text_domain
 */
function pmprosp_load_plugin_text_domain() {
	load_plugin_textdomain( 'pmpro-strong-passwords', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'pmprosp_load_plugin_text_domain' );

/**
 * Enqueue scripts and styles for strong password bar.
 * @since 1.0
 */
function pmprosp_password_strength_scripts_and_styles() {
	global $pmpro_pages, $post;

	// Don't load this script at all if user is logged in.
	if ( is_user_logged_in() ) {
		return;
	}

	// Lets do some checks for checkout pages, even ones that are 'standalone'.
	$is_checkout = false;
	if ( pmpro_is_checkout() ) {
		$is_checkout = true;
	}

	// Check if we're using the Signup Shortcode Add On.
	if ( isset( $post ) && ! $is_checkout ) {
		// Has signup shortcode.
		if ( strpos( $post->post_content, '[pmpro_signup' ) !== false ) {
			$is_checkout = true;
		}
	}

	// Only load on certain PMPro pages.
	if ( $is_checkout ) {
		wp_enqueue_script( 'password-strength-meter' );
		wp_enqueue_script( 'pmprosp-js', plugins_url( 'js/jquery.pmpro-strong-passwords.js', __FILE__ ), array( 'jquery', 'password-strength-meter' ), PMPROSP_VERSION, array( 'in_footer' => true ) );
		wp_enqueue_style( 'pmprosp-css', plugins_url( 'css/pmpro-strong-passwords.css', __FILE__ ), array(), PMPROSP_VERSION, 'all' );

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
				'password_tooltip' => wp_get_password_hint(),
				'progressbar_bg_color' => apply_filters( 'pmprosp_progressbar_bg_color', '#aaaaaa' ),
				'display_progressbar' => apply_filters( 'pmprosp_display_progressbar', true ),
				'display_password_strength' => apply_filters( 'pmprosp_display_password_strength', true ),
				'display_password_tooltip' => apply_filters( 'pmprosp_display_password_tooltip', true )
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'pmprosp_password_strength_scripts_and_styles' );

/**
 * This function checks to make sure the user has submitted a strong password
 * by checking for length, lowercase/uppercase, numbers, special characters, and matching username.
 */
function pmpro_strong_password_check( $pmpro_continue_registration ) {

	// Don't load this script at all if user is logged in.
	if ( is_user_logged_in() ) {
		return $pmpro_continue_registration;
	}

	//only bother checking if there are no errors so far
	if( ! $pmpro_continue_registration )
		return $pmpro_continue_registration;

	// Get the username from the request.
	$username = isset( $_REQUEST['username'] ) ? sanitize_text_field( $_REQUEST['username'] ) : NULL;

	// Note: We can't sanitize the passwords. They get hashed when saved.
	// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( isset( $_REQUEST['password'] ) ) {
		$password = $_REQUEST['password'];
	} else {
		$password = '';
	}
	// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	// no password (existing user is checking out)
	if ( empty( $password ) )
		return $pmpro_continue_registration;

	// Run a custom check for older PHP versions (Pre 7).
	if ( version_compare( phpversion(), '7.2', '<' ) ) {
		return pmpro_strong_password_custom_checker( $password, $username );
	}


	require_once plugin_dir_path( __FILE__ ).'vendor/autoload.php';

	$zxcvbn = new Zxcvbn();

	// Check for username match
	if ( $password == $username ) {
		pmpro_setMessage( esc_html__( 'Your password must not match your username.', 'pmpro-strong-passwords' ), 'pmpro_error' );
		return false;
	}

	$verbose_validation = apply_filters( 'pmprosp_enable_verbose_password_validation', false );

	if( $verbose_validation ){

		$user_data = array(
			$username,
			$password
		);

		$password_strength = $zxcvbn->passwordStrength( $password, $user_data );
	} else {
		$password_strength = $zxcvbn->passwordStrength( $password );
	}

	if ( isset( $password_strength['score'] ) && $password_strength['score'] <= apply_filters( 'pmprosp_minimum_password_score', 2, $password_strength ) ) {
		// Localize the suggestions passed in from the zxcvbn library.
		$suggestions = $password_strength['feedback']['suggestions'];
		foreach( $suggestions as $key => $text ) {
			if ( $text === "Add another word or two. Uncommon words are better." ) {
				$suggestions[$key] = __( "Add another word or two. Uncommon words are better.", 'pmpro-strong-passwords' );
			} elseif ( $text === "Avoid dates and years that are associated with you" ) {
				$suggestions[$key] = __( "Avoid dates and years that are associated with you", 'pmpro-strong-passwords' );
			} elseif ( $text === "Capitalization doesn't help very much" ) {
				$suggestions[$key] = __( "Capitalization doesn't help very much", 'pmpro-strong-passwords' );
			} elseif ( $text === "All-uppercase is almost as easy to guess as all-lowercase" ) {
				$suggestions[$key] = __( "All-uppercase is almost as easy to guess as all-lowercase", 'pmpro-strong-passwords' );
			} elseif ( $text === "Predictable substitutions like '@' instead of 'a' don't help very much" ) {
				$suggestions[$key] = __( "Predictable substitutions like '@' instead of 'a' don't help very much", 'pmpro-strong-passwords' );
			} elseif ( $text === "Avoid repeated words and characters" ) {
				$suggestions[$key] = __( "Avoid repeated words and characters", 'pmpro-strong-passwords' );
			} elseif ( $text === "Reversed words aren't much harder to guess" ) {
				$suggestions[$key] = __( "Reversed words aren't much harder to guess", 'pmpro-strong-passwords' );
			} elseif ( $text === "Avoid sequences" ) {
				$suggestions[$key] = __( "Avoid sequences", 'pmpro-strong-passwords' );
			} elseif ( $text === "Use a longer keyboard pattern with more turns" ) {
				$suggestions[$key] = __( "Use a longer keyboard pattern with more turns", 'pmpro-strong-passwords' );
			} elseif ( $text === "Avoid recent years" ) {
				$suggestions[$key] = __( "Avoid recent years", 'pmpro-strong-passwords' );
			} elseif ( $text === "Avoid years that are associated with you" ) {
				$suggestions[$key] = __( "Avoid years that are associated with you", 'pmpro-strong-passwords' );
			} elseif ( $text === "Use a few words, avoid common phrases" ) {
				$suggestions[$key] = __( "Use a few words, avoid common phrases", 'pmpro-strong-passwords' );
			} elseif ( $text === "No need for symbols, digits, or uppercase letters" ) {
				$suggestions[$key] = __( "No need for symbols, digits, or uppercase letters", 'pmpro-strong-passwords' );
			}
		}

		pmpro_setMessage( __( 'Password Error:', 'pmpro-strong-passwords' ) . ' ' .apply_filters( 'pmprosp_minimum_password_score_message', implode( " ", $suggestions ), $password_strength ), 'pmpro_error' );
		return false;
	}

	// If we've passed all of the above, return the current continue registration flag.
	return $pmpro_continue_registration;
}
// Leaving this logic here if user's want to bring this back int future versions.
add_filter( 'pmpro_registration_checks', 'pmpro_strong_password_check' );

function pmprosp_pmpro_checkout_after_password() {
	?>
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form-strong-password-container', 'pmpro_form-strong-password-container' ) ); ?>">
		<div id="pmprosp-container" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form-strong-password-indicator' ) ); ?>"></div>
		<p id="pmprosp-password-notice" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_hint' ) ); ?>"><?php echo esc_html( wp_get_password_hint() ); ?></p>
	</div>
	<?php
}
// load as early as possible in case there are uses of filter
add_filter( 'pmpro_checkout_after_password', 'pmprosp_pmpro_checkout_after_password', 1 );

/**
 * Function for PHP < 7.0
 * @since 0.4
 */
function pmpro_strong_password_custom_checker( $password, $username ) {

	$pass_ok = true;

	$minimum_password_length = apply_filters( 'pmprosp_minimum_password_length', 12 );

	// Check for length (x characters)
	if ( strlen( $password ) < $minimum_password_length ) {
		/* translators: %d is the minimum password length */
		pmpro_setMessage( sprintf( esc_html__( 'Your password must be at least %d characters long.', 'pmpro-strong-passwords' ), $minimum_password_length ), 'pmpro_error' );
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
		pmpro_setMessage( esc_html__( 'Your password must contain at least 1 uppercase letter.', 'pmpro-strong-passwords' ), 'pmpro_error' );
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
	return $pass_ok;
}

/**
 * Add links to the plugin row meta
 */
function pmprosp_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'pmpro-strong-passwords.php' ) !== false ) {
		$new_links = array(
			'<a href="' . esc_url('https://www.paidmembershipspro.com/add-ons/require-strong-passwords/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-strong-passwords' ) ) . '">' . __( 'Docs', 'pmpro-strong-passwords' ) . '</a>',
			'<a href="' . esc_url('https://www.paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro-strong-passwords' ) ) . '">' . __( 'Support', 'pmpro-strong-passwords' ) . '</a>',
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'pmprosp_plugin_row_meta', 10, 2 );
