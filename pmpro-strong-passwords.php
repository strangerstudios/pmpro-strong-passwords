<?php
/*
Plugin Name: PMPro Strong Passwords
Version: 0.1
Plugin URI: http://www.paidmembershipspro.com/add-ons/plugins-on-github/require-strong-passwords/
Description: Force users to submit strong passwords.
Version: .1
Author: Scott Sousa
Author URI: http://slocumstudio.com
*/

/**
 * This function checks to make sure the user has submitted a strong password
 * by checking for length, lowercase/uppercase, numbers, special characters, and matching username.
 */
function pmpro_strong_password_check( $pmpro_continue_registration ) 
{	
	//only bother checking if there are no errors so far
	if(!$pmpro_continue_registration)
		return $pmpro_continue_registration;
	
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	
	// no password (existing user is checking out)
	if(empty($password))
		return $pmpro_continue_registration;
	
	// Check for length (8 characters)
	if ( strlen( $password ) < 8 ) {
		pmpro_setMessage( 'Your password must be at least 8 characters long.', 'pmpro_error' );
		return false;
	}

	// Check for username match
	if ( $password == $username ) {
		pmpro_setMessage( 'Your password must not match your username.', 'pmpro_error' );
		return false;
	}

	// Check for containing username
	if ( strpos( $password, $username ) !== false ) {
		pmpro_setMessage( 'Your password must not contain your username.', 'pmpro_error' );
		return false;
	}

	// Check for lowercase
	if ( ! preg_match( '/[a-z]/', $password ) ) {
		pmpro_setMessage( 'Your password must contain at least 1 lowercase letter.', 'pmpro_error' );
		return false;
	}

	// Check for uppercase
	if ( ! preg_match( '/[A-Z]/', $password ) ) {
		pmpro_setMessage( 'Your password must contain at least 1 uppercase letter.', 'pmpro_error' );
		return false;
	}

	// Check for numbers
	if ( ! preg_match( '/[0-9]/', $password ) ) {
		pmpro_setMessage( 'Your password must contain at least 1 number.', 'pmpro_error' );
		return false;
	}

	// Check for special characters
	if ( ! preg_match( '/[\W]/', $password ) ) {
		pmpro_setMessage( 'Your password must contain at least 1 special character.', 'pmpro_error' );
		return false;
	}

	// If we've passed all of the above, return the current continue registration flag.
	return $pmpro_continue_registration;
}
add_filter( 'pmpro_registration_checks', 'pmpro_strong_password_check' );

function pmprosp_pmpro_checkout_after_password()
{
?>
<p>Note: Your password must be at least 8 characters long and contain upper and lowercase letters, a number, and a special character.</p>
<?php
}
add_action("pmpro_checkout_after_password", "pmprosp_pmpro_checkout_after_password", 1, 0);
