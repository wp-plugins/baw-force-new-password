<?php
/*
Plugin Name: Baw Force New Password
Description: Force new users to change their password on their first login.
Plugin URI: http://blog.secupress.fr/nouvel-utilisateur-nouveau-mot-de-passe-186.html
Version: 1.1
Author: Julio Potier
Author URI: http://wp-rocket.me
License: GPL v2 or later
*/

add_action( 'init', 'bawfnp_l10n_init' );
function bawfnp_l10n_init() {
	load_plugin_textdomain( 'bawfnp', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

add_action( 'init', 'bawfnp_redirect' );
function bawfnp_redirect() {
	global $current_user, $pagenow;
	$current_user = wp_get_current_user();
	$_pagenow = is_admin() && $pagenow ? $pagenow : null;
	if ( 'profile.php' != $_pagenow && is_user_logged_in() && get_user_meta( $current_user->ID, 'force-new-password', true ) ) {
		wp_redirect( admin_url( 'profile.php' ) );
		die();
	}
}

add_action( 'user_register', 'bawfnp_user_register' );
function bawfnp_user_register( $user_id ) {
	add_user_meta( $user_id, 'force-new-password', true );
}

add_action( 'personal_options_update', 'bawfnp_personal_options_update' );
add_action( 'edit_user_profile_update', 'bawfnp_personal_options_update' );
function bawfnp_personal_options_update( $user_id ) {
	if ( isset( $_POST['pass1'], $_POST['pass2'] ) && $_POST['pass1'] == $_POST['pass2'] ) {
		delete_user_meta( $user_id, 'force-new-password' );
	}
}

add_action( 'admin_notices', 'bawfnp_notices' );
function bawfnp_notices() {
	if ( get_user_meta( $GLOBALS['current_user']->ID, 'force-new-password', true ) ) {
		printf(
			'<div class="error"><p>%s %s</p></div>',
			__( 'You&rsquo;re using the auto-generated password for your account. Please change it now.', 'bawfnp' ),
			__( '(required)' )
			);
	}
}