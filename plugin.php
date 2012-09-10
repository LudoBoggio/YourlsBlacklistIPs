<?php
/*
Plugin Name: BlackListIP
Plugin URI: https://github.com/LudoBoggio/YourlsBlackListIPs
Description: Plugin which block blacklisted IPs
Version: 1.1
Author: Ludo
Author URI: http://ludo.boggio.fr
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Hook the custom function into the 'pre_check_ip_flood' event
yourls_add_action( 'pre_check_ip_flood', 'ludo_blacklist_ip_root' );

// Hook the admin page into the 'plugins_loaded' event
yourls_add_action( 'plugins_loaded', 'ludo_blacklist_ip_add_page' );

// Get blacklisted IPs from YOURLS options feature and compare with current IP address
function ludo_blacklist_ip_root ( $args ) {

	$ip = $args[0];
	$liste_ip = yourls_get_option ('ludo_blacklist_ip_liste');
	if ( !$liste_ip ) {
		$liste_ip_display = unserialize ( $liste_ip );

		if (in_array($ip, $liste_ip_display)) {
	//		yourls_die ( "Your IP has been blacklisted.", "Black list",403);
			echo "<center>Your IP has been blacklisted.</center>";
			die();
		}
	}
}

// Add admin page
function ludo_blacklist_ip_add_page () {
	yourls_register_plugin_page( 'ludo_blacklist_ip', 'Blacklist IPs', 'ludo_blacklist_ip_do_page' );
}

// Display admin page
function ludo_blacklist_ip_do_page () {
	if( isset( $_POST['action'] ) && $_POST['action'] == 'blacklist_ip' ) {
		ludo_blacklist_ip_process ();
	} else {
		ludo_blacklist_ip_form ();
	}
}

// Display form to administrate blacklisted IPs list
function ludo_blacklist_ip_form () {
	$nonce = yourls_create_nonce( 'blacklist_ip' ) ;
	$liste_ip = yourls_get_option ('ludo_blacklist_ip_liste','Enter IP addresses here, one per line');
	if ($liste_ip != 'Enter IP addresses here, one per line' )
		$liste_ip_display = implode ( "\n" , unserialize ( $liste_ip ) );
	echo <<<HTML
		<h2> BlackList IPs</h2>
		<form method="post">
		
		<input type="hidden" name="action" value="blacklist_ip" />
		<input type="hidden" name="nonce" value="$nonce" />
		
		<p>Blacklist following IPs
		<textarea cols="30" rows="5" name="blacklist_form">
		$liste_ip_display
		</textarea>
		</p>
		
		<p><input type="submit" value="Save" /></p>
		</form>
HTML;
}

// Update blacklisted IPs list
function ludo_blacklist_ip_process () {
	// Check nonce
	yourls_verify_nonce( 'blacklist_ip' ) ;

	$IP_Array = $_POST['blacklist_form'];

	// IP address verification
	foreach ($IP_Array as $key => $value ) {
		if (ludo_blacklist_ip_check_ip($value) !==TRUE ) unset ($IP_Array[$key]);
	}

	// Update list
<<<<<<< HEAD
	$sent_list = serialize ( explode ( '\n' , $IP_Array ) );
	yourls_update_option ( $sent_list , 'ludo_blacklist_ip_liste' );
=======
	$sent_list = serialize ( explode ( '\n' , $_POST['blacklist_form'] ) );
	yourls_update_option ( 'ludo_blacklist_ip_liste' , $sent_list );
>>>>>>> f4904534db2c4b699379a0e01761b6ccfd514909
	echo "Black list updated" ;
}

function ludo_blacklist_ip_check_ip () {

}
?>
