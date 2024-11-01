<?php
// only execute the contents of the file if the plugin is really being uninstalled
	if ( !defined('WP_UNINSTALL_PLUGIN') ) {
		exit();
	}
	global $wpdb;
	$paypal_settings= $wpdb->prefix."ppa_paypal_settings";	
	if( $wpdb->get_var("SHOW TABLES LIKE '$paypal_settings'")==$paypal_settings ){
		$sql="DROP TABLE ".$paypal_settings;
		$wpdb->query( $sql );
	}
	
	$subs_period= $wpdb->prefix."ppa_subs_period";	
	if( $wpdb->get_var("SHOW TABLES LIKE '$subs_period'")==$subs_period ){
		$sql="DROP TABLE ".$subs_period;
		$wpdb->query( $sql );
	}
	
	$package_table= $wpdb->prefix."ppa_package_table";	
	if( $wpdb->get_var("SHOW TABLES LIKE '$package_table'")==$package_table ){
		$sql="DROP TABLE ".$package_table;
		$wpdb->query( $sql );
	}

	$ppa_subscriber= $wpdb->prefix."ppa_subscriber_table";	
	if( $wpdb->get_var("SHOW TABLES LIKE '$ppa_subscriber'")==$ppa_subscriber ){
		$sql="DROP TABLE ".$ppa_subscriber;
		$wpdb->query( $sql );
	}		
	
?>