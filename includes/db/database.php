<?php 
/**
* Create custom tables
*/
function paypal_payments_settings_table() {
	//error_log('plugin activated');
	global $wpdb;
	$settings_tablename= $wpdb->prefix."ppa_paypal_settings";	
	if ( $wpdb->get_var("SHOW TABLES LIKE '$settings_tablename'")!=$settings_tablename ) {
		$sql="CREATE TABLE IF NOT EXISTS ".$settings_tablename." (
			  `p_settings_id` int(11) NOT NULL AUTO_INCREMENT,
			  `merchant_email` varchar(100) NOT NULL,
			  `partner` varchar(100) NOT NULL,
			  `vendor` varchar(100) NOT NULL,
			  `user` varchar(100) NOT NULL,
			  `password` varchar(255) NOT NULL,
			  `payment_mode` int(11) NOT NULL,
			  PRIMARY KEY (`p_settings_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta ( $sql );			
	}

	
}
function paypal_payments_period_table() {
	//error_log('plugin activated');
	global $wpdb;
	$subs_period_tablename= $wpdb->prefix."ppa_subs_period";	
	if ( $wpdb->get_var("SHOW TABLES LIKE '$subs_period_tablename'")!=$subs_period_tablename ) {
		$sql_period="CREATE TABLE IF NOT EXISTS ".$subs_period_tablename." (
					  `subs_period_id` int(11) NOT NULL AUTO_INCREMENT,
					  `subs_period_name` varchar(255) CHARACTER SET utf8 NOT NULL,
					  PRIMARY KEY (`subs_period_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta ( $sql_period );			
	}

	
}

function paypal_payments_package_table() {
	//error_log('plugin activated');
	global $wpdb;
	$package_tablename= $wpdb->prefix."ppa_package_table";	
	if ( $wpdb->get_var("SHOW TABLES LIKE '$package_tablename'")!=$package_tablename ) {
		$sql_package="CREATE TABLE IF NOT EXISTS ".$package_tablename." (
					`package_id` int(11) NOT NULL AUTO_INCREMENT,
					`package_title` varchar(255) NOT NULL,
					`payment_type` varchar(255) NOT NULL,
					`initial_amount` double NOT NULL DEFAULT '1',
					`package_amount` double NOT NULL,
					`subs_period_id` int(11) NOT NULL,
					`entry_date` datetime NOT NULL,
					PRIMARY KEY (`package_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta ( $sql_package );			
	}

	
}
function paypal_payments_subscriber_table() {
	//error_log('plugin activated');
	global $wpdb;
	$subscriber_tablename= $wpdb->prefix."ppa_subscriber_table";	
	if ( $wpdb->get_var("SHOW TABLES LIKE '$subscriber_tablename'")!=$subscriber_tablename ) {
		$sql_subscriber="CREATE TABLE IF NOT EXISTS `wp_ppa_subscriber_table` (
					  `subs_id` int(11) NOT NULL AUTO_INCREMENT,
					  `bill_fname` varchar(255) NOT NULL,
					  `bill_lname` varchar(255) NOT NULL,
					  `bill_city` varchar(255) NOT NULL,
					  `bill_street` varchar(255) NOT NULL,
					  `bill_zip` varchar(255) NOT NULL,
					  `bill_state` varchar(255) NOT NULL,
					  `bill_email` varchar(255) NOT NULL,
					  `bill_company` varchar(255) NOT NULL,
					  `pnref_value` varchar(255) NOT NULL,
					  `rpref_value` varchar(255) NOT NULL,
					  `profile_id` varchar(255) NOT NULL,
					  `resp_msg` varchar(255) NOT NULL,
					  `subs_start_date` varchar(255) NOT NULL,
					  `card_type` int(11) NOT NULL,
					  `card_number` varchar(100) NOT NULL,
					  `card_exp_date` varchar(100) NOT NULL,
					  `user_id` int(11) NOT NULL,
					  `spid` int(11) NOT NULL,
					  `amount` varchar(250) NOT NULL,
					  `subs_period` varchar(255) NOT NULL,
					  `trx_type` varchar(255) NOT NULL,
					  `tender` varchar(255) NOT NULL,
					  `package_id` int(11) NOT NULL,
					  `status` int(11) NOT NULL,
					  `entry_date` datetime NOT NULL,
					  PRIMARY KEY (`subs_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta ( $sql_subscriber );			
	}
	
}

function paypal_payments_settings_install_data() {
    global $wpdb;

    $paypal_settings = $wpdb->prefix . 'ppa_paypal_settings'; 

	
	$query = $wpdb->get_var("SELECT COUNT(*) FROM ".$paypal_settings." ");
	if ( $query == 0 ) {
		$wpdb->insert($paypal_settings, array(
			'merchant_email' => 'sample@example.com',
			'partner' => 'PayPal',
			'vendor' => 'sample',
			'user' => 'sample',
			'password' => '1234',
			'payment_mode' => '0'		
		));
	}
	
}

function paypal_payments_period_install_data() {
    global $wpdb;

    $subs_period = $wpdb->prefix . 'ppa_subs_period'; 

	$query = $wpdb->get_var( "SELECT COUNT(*) FROM ".$subs_period." " );
	
	if ( $query == 0 ) {	

		$wpdb->insert($subs_period, array(
			'subs_period_name' => 'Day'
		));
		$wpdb->insert($subs_period, array(
			'subs_period_name' => 'Week'
		));	
		$wpdb->insert($subs_period, array(
			'subs_period_name' => 'Month'
		));		
		$wpdb->insert($subs_period, array(
			'subs_period_name' => 'Year'
		));	
	}

	
}

?>