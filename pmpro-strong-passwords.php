<?php
/**
 * Plugin Name: PMPro Strong Passwords
 * Description: Enables strong password default for checkout.
 * Plugin URI: https://paidmembershipspro.com
 * Author: Stranger Studios
 * Author URI: https://paidmembershipspro.com
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: Text Domain
 */

defined( 'ABSPATH' ) or exit;

/** 
 * This function enqueues all the required scripts to enable the password strength meter functionality.
 */
function pmprosp_enqueue_scripts(){

	wp_register_script( 'pmpro-strong-pass', plugins_url( '/js/pmpro-strong-pass.js', __FILE__ ), array( 'jquery', 'password-strength-meter' ), false, false );
	wp_register_style( 'pmpro-strong-pass-css', plugins_url( '/css/pmpro-strong-pass.css', __FILE__ ) );
	
	wp_enqueue_script( 'password-strength-meter' );
	wp_enqueue_style( 'pmpro-strong-pass-css' );

	//
	$blacklisted_array = apply_filters( 'pmpro_password_blacklist', array() );
	
	wp_localize_script( 'pmpro-strong-pass', 'blacklist_words', $blacklisted_array );
	wp_enqueue_script( 'pmpro-strong-pass' );

}

 add_action( 'wp_enqueue_scripts', 'pmprosp_enqueue_scripts' );


