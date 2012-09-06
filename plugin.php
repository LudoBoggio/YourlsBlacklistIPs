<?php
/*
Plugin Name: BlackListIP
Plugin URI: http://yourls.org
Description: Plugin which block blacklisted IPs
Version: 1.0
Author: Ludo
Author URI: http://ludo.boggio.fr
*/

// Hook our custom function into the 'pre_redirect' event
yourls_add_action( 'pre_check_ip_flood', 'Ludo_BlackListIP_Root' );

// For this plugin to work, you need to define $yourls_blacklist_ip
// in the configuration file with entries like this :
// $yourls_blacklist_ip = array ("149.3.136.114","119.180.110.179");
function Ludo_BlackListIP_Root ( $args ) {

    global $yourls_blacklist_ip;

    $BlackListIP = $args[0];

    if (in_array($BlackListIP, $yourls_blacklist_ip)) {
//        yourls_die ( "Your IP has been blacklisted.", "Black list",403);
    echo "<center>Your IP has been blacklisted.</center>";
        die();
    }
}
?>
