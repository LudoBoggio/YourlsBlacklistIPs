<?php
/*
Plugin Name: BlackListIP
Plugin URI: https://github.com/LudoBoggio/YourlsBlackListIPs
Description: Plugin which block blacklisted IPs
Version: 1.3
Author: Ludo
Author URI: http://ludovic.boggio.fr
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

include "ludo_blacklist_ip_Check_IP_Module.php";

// Hook the custom function into the 'pre_check_ip_flood' event
yourls_add_action( 'pre_check_ip_flood', 'ludo_blacklist_ip_root' );

// Hook the admin page into the 'plugins_loaded' event
yourls_add_action( 'plugins_loaded', 'ludo_blacklist_ip_add_page' );

// Get blacklisted IPs from YOURLS options feature and compare with current IP address
function ludo_blacklist_ip_root ( $args ) {
	$IP = $args[0];
	$Intervalle_IP = yourls_get_option ('ludo_blacklist_ip_liste');
	$Intervalle_IP = ( $Intervalle_IP ) ? ( unserialize ( $Intervalle_IP ) ):((array)NULL); 

	foreach ( $Intervalle_IP as $value ) {
		$IPs = explode ( "-" , $value );

		if ( $IP >= $IPs[0] AND $IP <= $IPs[1]) {
			//      yourls_die ( "Your IP has been blacklisted.", "Black list",403);
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
    $liste_ip = yourls_get_option ('ludo_blacklist_ip_liste','Enter IP addresses here, one entry per line');
    if ($liste_ip != 'Enter IP addresses here, one entry per line' )
        $liste_ip_display = implode ( "\r\n" , unserialize ( $liste_ip ) );
	else
		$liste_ip_display=$liste_ip;
    echo <<<HTML
        <h2>BlackList IPs</h2>
        <form method="post">
        
        <input type="hidden" name="action" value="blacklist_ip" />
        <input type="hidden" name="nonce" value="$nonce" />
        
        <p>Blacklist following IPs (one range or IP per line, no wildcards allowed) :</p>
        <p><textarea cols="50" rows="10" name="blacklist_form">$liste_ip_display</textarea></p>
        
        <p><input type="submit" value="Save" /></p>
		<p>I suggest to add here IPs that you saw adding bulk URL. It is your own responsibility to check the use of the IPs you block. WARNING : erroneous entries may create unexpected behaviours, please double-check before validation.</p>
		<p>Examples : 
			<ul>
				<li>10.0.0.1/24 : blacklist from 10.0.0.0 to 10.0.0.255 (CIDR notation).</li>
				<li>192.168.1.2/255.255.255.128 : blacklist from 192.168.1.0 to 192.168.0.128.</li>
				<li>192.168.1.12-192.168.1.59 : blacklist from 192.168.1.12 to 192.168.1.59.</li>
				<li>192.168.0.0 : blacklist from 192.168.0.0 to 192.168.255.255</li>
				<li>10.0.0.58 : blacklist only 10.0.0.58 IP address.</li>
			</ul>
		</p>
        </form>
HTML;
}

// Update blacklisted IPs list
function ludo_blacklist_ip_process () {
    // Check nonce
    yourls_verify_nonce( 'blacklist_ip' ) ;
	
	// Check if the answer is correct.
	$IP_Form = explode ( "\r\n" , $_POST['blacklist_form'] ) ;
	
	if (! is_array ($IP_Form) ) {
		echo "Bad answer, Blacklist not updated";
		die ();
	}
	
	$boucle = 0;

	foreach ($IP_Form as $value) {
		$Retour = ludo_blacklist_ip_Analyze_IP ( $value ) ;
		if ( $Retour != "NULL" ) {
			$IPList[$boucle++] = $Retour ;
		}
	}
	// Update list
	yourls_update_option ( 'ludo_blacklist_ip_liste', serialize ( $IPList ) );
	echo "Black list updated. New blacklist is " ;
	if ( count ( $IPList ) == 0 ) 
		echo "empty.";
	else {
		echo ":<BR />";
		foreach ($IPList as $value) echo $value."<BR />";
	}
}



