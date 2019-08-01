<?php

/* Sample filter usage for pmprosp
 * copy  these in your theme's functions.php or
 * in your customization plugin
 */

function my_bad_password_list() {
	global $pmprosp_password_blacklist;
	$bad_words = array(
		'test',
		'admin',
		'administrator',
		'donkeykong',
	);
	foreach ( $bad_words as $bad_word ) {
		$pmprosp_password_blacklist[] = $bad_word;
	}

	return $pmprosp_password_blacklist;
}
add_filter( 'pmprosp_password_blacklist', 'my_bad_password_list' );

function my_pmprosp_display_password_tooltip() {
	return false;
}
add_filter( 'pmprosp_display_password_tooltip', 'my_pmprosp_display_password_tooltip' );

function my_pmprosp_allow_weak() {
	return true;
}
add_filter( 'pmprosp_allow_weak_passwords', 'my_pmprosp_allow_weak' );

function my_pmprosp_display_progressbar() {
	return false;
}
add_filter( 'pmprosp_display_progressbar', 'my_pmprosp_display_progressbar' );

function my_pmprosp_display_tooltip() {
	return false;
}
add_filter( 'pmprosp_display_password_tooltip', 'my_pmprosp_display_tooltip' );

function my_pmprosp_display_password_strength() {
	return false;
}
add_filter( 'pmprosp_display_password_strength', 'my_pmprosp_display_password_strength' );


function my_pmprosp_progressbar_bg_color() {
	return 'dodgerblue';
}
add_filter( 'pmprosp_progressbar_bg_color', 'my_pmprosp_progressbar_bg_color' );
