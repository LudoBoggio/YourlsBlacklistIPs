<?php

function ludo_blacklist_ip_Analyze_IP ( $Input ) {
	if ( strpos ( $Input , "/" ) !== FALSE ) { // Case input contain "/"
		$Inputs = array_map ("trim", explode ( "/" , $Input ) );
		if (ludo_blacklist_ip_Check_IP ( $Inputs[0] ) && ctype_digit ($Inputs[1]) && $Inputs[1] > 0 && $Inputs[1] <= 32) { // Cas CIDR
			$Cible = ludo_blacklist_ip_CalculIPMask ( $Inputs[0] , ludo_blacklist_ip_MaskType2Mask ( $Inputs[1] ) ) ;
		} elseif (ludo_blacklist_ip_Check_IP ( $Inputs[0] ) && ludo_blacklist_ip_Check_IP ($Inputs[1]) && ludo_blacklist_ip_Check_Mask ($Inputs[1]) ){ // Case IP/Mask
			$Cible = ludo_blacklist_ip_CalculIPMask ( $Inputs[0] , $Inputs[1] ) ;
		} else { // Contains "/" but invalid
			$Cible="NULL";
		}
	}
	elseif ( strpos ( $Input , "-" ) !== FALSE ) { // Case input contains "-"
		$Inputs = array_map ("trim", explode ( "-" , $Input ) );
		if ( ludo_blacklist_ip_Check_IP ( $Inputs[0] ) && ludo_blacklist_ip_Check_IP ( $Inputs[1] ) ) {
			if ( $Inputs[0] < $Inputs[1] ) { // Check IP orders
				$Cible = $Inputs[0] . "-" . $Inputs[1] ;
			}
			else { // If wrong order, reverse it
				$Cible = $Inputs[1] . "-" . $Inputs[0] ;
			}
		} else { // Contains "-" but invalid
			$Cible="NULL";
		}
	}
	elseif (ludo_blacklist_ip_Check_IP ( $Input )) { // Case input is a single IP
		$Inputs = array_map ("trim", explode ( "." , $Input ) );
		if ( $Inputs[0] == 0 && $Inputs[1] == 0 && $Inputs[2] == 0 && $Inputs[3] == 0 ) { // Case 0.0.0.0
			$Cible = "0.0.0.0-255.255.255.255" ;
		}
		elseif ( $Inputs[1] == 0 && $Inputs[2] == 0 && $Inputs[3] == 0 ) { // Case A.0.0.0
			$Cible = $Inputs[0] . ".0.0.0-" . $Inputs[0] . ".255.255.255";
		}
		elseif ( $Inputs[2] == 0 && $Inputs[3] == 0 ) { // Case A.B.0.0
			$Cible = $Inputs[0] . "." . $Inputs[1] . ".0.0-" . $Inputs[0] . "." . $Inputs[1] . ".255.255";
		}
		elseif ( $Inputs[3] == 0 ) { // Case A.B.C.0
			$Cible = $Inputs[0] . "." . $Inputs[1] . "." . $Inputs[2] . ".0-" . $Inputs[0] . "." . $Inputs[1] . "." . $Inputs[2] . ".255";
		}
		else { // Case of a single IP address
			$Cible = $Input . "-" . $Input ;
		}
	}
	else { // Invalid IP address
			$Cible="NULL";
	}
	return $Cible;
}

function ludo_blacklist_ip_Check_IP ( &$IP ) {
	// Input : String of IP address
	// Output : TRUE if string is a valid IP address
	$IPs = array_map("ludo_blacklist_ip_IP_trim", explode ( "." , $IP ) ) ;
	if (count ($IPs) != 4 ) return false ;
	foreach ( $IPs as $value ) {
		if ( $value < 0 || $value > 255 ) {
			return false ;
		}
	}
	$IP = implode ( ".", $IPs );
	return true;
}

function ludo_blacklist_ip_IP_Trim ( $IP ) {
	// Input : array of IP address with strings 
	// Output : array of the IP address with integer
	return (int) ltrim ( trim ( $IP ) , "0" ) ;
}

function ludo_blacklist_ip_Check_Mask ( $Mask ) {
// Input :  Mask to be checked, string
// Return OK if the Mask string is correct
	$Masks = array_map("ludo_blacklist_ip_IP_trim", explode ( "." , $Mask ) ) ;
	if (count ($Masks) != 4 ) return false ;
	$OctetSignificatif = -1;
	foreach ( $Masks as $key => $value ) {
		if ( $value == 255 and $OctetSignificatif == -1 ) {
			continue;
		}
		if ( $value == 255 and $OctetSignificatif != -1 ) {
			return false ;
		}
		if ( $value >0 and $OctetSignificatif == -1 ) {
			$OctetSignificatif = $key ;
			continue;
		}
		if ( $value >0 and $OctetSignificatif != -1 ) {
			return false ;
		}
		if ( $value == 0 and $OctetSignificatif == -1 and $Masks[$key-1] != 255) {
			return false ;
		}
		if ( $value == 0 and $OctetSignificatif == -1 and $Masks[$key-1] == 255) {
			$OctetSignificatif = $key;
			continue ;
		}
		if ( $value == 0 and $OctetSignificatif != -1 ) {
			continue ;
		}
	}
	return (($OctetSignificatif != -1) and in_array ($Masks[$OctetSignificatif],array ("255","254","252","248","240","224","192","128","0")));
}

function ludo_blacklist_ip_MaskType2Mask ( $MaskType ) {
// Input : Integer value
// Output : Mask with $MaskType bit at 1, others at 0, string
	for ($boucle = 0; $boucle < 4 ; $boucle++ ) {
		if ( $MaskType > 8 ) $Masks[$boucle] = 255;
		elseif ($MaskType <= 0 ) $Masks[$boucle] = 0;
		else $Masks[$boucle] = bindec (str_repeat ( "1" , $MaskType ) . str_repeat ("0" , 8-$MaskType ) );
		$MaskType -= 8;
	}
	return implode ( "." , $Masks);
}

function ludo_blacklist_ip_CalculIPMask ( $IP , $Mask ) {
// Input : IP address and Mask, strings
// Output a string $IPStart."-".$IPEnd for those IP and mask
	$IPs = explode ( "." , $IP ) ;
	$Masks = explode ( "." , $Mask ) ;
	$OctetSignificatif = -1;
	
	for ($boucle = 0 ; $boucle < sizeof ( $IPs ) ; $boucle++ ) {
		$IP_Starts[$boucle] =  (0+$IPs[$boucle]) & (0+$Masks[$boucle]) ;
		if (($Masks[$boucle] < 255) && ( $OctetSignificatif == -1 ) ) {
			$OctetSignificatif = $boucle;
		}
	}
	for ($boucle = 0 ; $boucle < sizeof ( $IPs ) ; $boucle++ ) {
		if ($boucle < $OctetSignificatif ) 
			$IP_Ends[$boucle] = 0+$IPs[$boucle] ;
		elseif ($boucle == $OctetSignificatif )
			$IP_Ends[$boucle] = (0+$IPs[$boucle]) | ( 255-$Masks[$boucle] ) ;
		else
			$IP_Ends[$boucle] = 255;
	}
	return implode ( "." , $IP_Starts)."-".implode ( "." , $IP_Ends);
}

?>
