<?php

function paypal_payments_advanced_direct( $bill_package_title, $bill_spid, $bill_company_name, $bill_user_id, $bill_package_id ) {

	global $wpdb;

	$result = $_GET['RESULT'];
	
	if ( $result == '0' ) {
		//error_reporting(1);
		$name = sanitize_text_field( $_GET['BILLTONAME'] );
		$bill_fname = sanitize_text_field( $_GET['BILLTOFIRSTNAME'] );
		$bill_lname = sanitize_text_field( $_GET['BILLTOLASTNAME'] );
		$bill_street = sanitize_text_field( $_GET['BILLTOSTREET'] );
		$bill_city = sanitize_text_field( $_GET['BILLTOCITY'] );
		$bill_zip = sanitize_text_field( $_GET['BILLTOZIP'] );
		$bill_state = sanitize_text_field( $_GET['BILLTOSTATE'] );
		$bill_email = sanitize_text_field( $_GET['BILLTOEMAIL'] );
		$bill_company = $bill_company_name;
		$pnref_value = sanitize_text_field( $_GET['PNREF'] );
		$resp_msg = sanitize_text_field( $_GET['RESPMSG'] );
		$subs_start_date = sanitize_text_field( $_GET['TRANSTIME'] );
		$card_type = sanitize_text_field( $_GET['CARDTYPE'] );
		$card_number = sanitize_text_field( $_GET['ACCT'] );
		$card_exp_date = sanitize_text_field( $_GET['EXPDATE'] );
		$trx_type = sanitize_text_field( $_GET['TRXTYPE'] );
		$tender = sanitize_text_field( $_GET['TENDER'] );
		$amount = sanitize_text_field( $_GET['AMT'] );
		$subs_period = 'Direct Payment';				
		$user_id_session = $bill_user_id;
		$package_id = $bill_package_id;				
		$status = 1;			

		$wpdb->insert( 
			$wpdb->prefix ."ppa_subscriber_table", 
			array( 
				'bill_fname' => $bill_fname,
				'bill_lname' => $bill_lname,
				'bill_street' => $bill_street,
				'bill_city' => $bill_city,
				'bill_zip' => $bill_zip,
				'bill_state' => $bill_state,
				'bill_email' => $bill_email,
				'bill_company' => $bill_company,
				'pnref_value' => $pnref_value,
				'resp_msg' => $resp_msg,
				'subs_start_date' => $subs_start_date,
				'card_type' => $card_type,
				'card_number' => $card_number,
				'card_exp_date' => $card_exp_date,
				'user_id' => $user_id_session,
				'spid' => $bill_spid,
				'amount' => $amount,
				'subs_period' => $subs_period,
				'trx_type' => $trx_type,
				'tender' => $tender,	
				'package_id' => $package_id,								
				'status' => $status,
				'entry_date' => date('Y-m-d H:i:s'),
			)
		
		);			
		if ($wpdb->insert_id > 0) {
			 $msg= "Successfully Value Saved";
		} else {
			  $msg= "Value not Saved";
		}
		
		//exit();
			
		mysql_close();	
		
		//Mail Send for buyer
		$to = $bill_email;
		$admin_email = get_option( 'admin_email' ); 
		$from = $admin_email ;
		$site_url_get=site_url();
		$subject = 'Thanks for your payment of '.$site_url_get;						

		$message = '

		<html>

			<head>

				<title>Thanks for your payment of '.$site_url_get.'</title>

			</head>

			<body style="font-family:Gautami,Arial,Helvetica,sans-serif; width:991px; margin:auto; text-align:left;">
			
			<div style="width:660px; margin:auto">
				<h1 style="color:#802144; text-align:left; margin:0;">Thanks for your payment</h1>

				<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">
Dear '.$name .',</p>

				<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">
Thank you, your payment successfully completed.   </p>


								
				<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">Sincerely,</p>

				<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">'.$site_url_get.'</p>
			</div>

			</body>

		</html>

		'; //Specify Email Message

	 $headers  = 'MIME-Version: 1.0' . "\r\n";

	 $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//To send HTML mail, the Content-type header must be set

	 $headers .= 'From:' .$from. "\r\n"; //Specify Sender		
	$sent=mail( $to, $subject, $message, $headers ); //sent it
		 
	if ( isset($sent) ) {

		//print "Your mail was sent successfully. Please Check Your E-mail"; 
	} else {

		//print "We encountered an error sending your mail"; 

	} 
	
	//Mail Send for buyer
	
	//Mail Send for admin
	
	$to_admin = $admin_email ;

	$from_admin = $admin_email ;
	
	$subject_admin = 'New direct payment of '.$site_url_get;						

	$message_admin = '

	<html>

		<head>

			<title>New direct payment of '.$site_url_get.'</title>

		</head>

		<body style="font-family:Gautami,Arial,Helvetica,sans-serif; width:991px; margin:auto; text-align:left;">


			<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">Dear admin,</p>

			<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">New direct payment successfully  completed.  </p>
			
			<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">User Name: '.$name.'</p>
			
			<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">Email: '.$bill_email.'</p>				



			<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">Sincerely,</p>

			<p style="color:#2b2b2b; font-size:16px; margin-bottom:24px; margin-top:0; text-align:justify; line-height:20px;">'.$site_url_get.'</p>

		</body>

	</html>

	'; //Specify Email Message
	
	
	 $headers_admin  = 'MIME-Version: 1.0' . "\r\n";

	 $headers_admin .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//To send HTML mail, the Content-type header must be set

	 $headers_admin .= 'From:' .$from_admin. "\r\n"; //Specify Sender
	$sent_admin=mail($to_admin, $subject_admin, $message_admin, $headers_admin); //sent it
	 
	if( isset($sent_admin) ) {

		//print "Your mail was sent successfully. Please Check Your E-mail"; 
	}else {

		//print "We encountered an error sending your mail"; 

	} 

	//Mail Send for admin
	

	$ppa_subscribeurl = current_page_url();
	
	//exit();
	$explode_subscribeurl=explode('?AVSZIP',$ppa_subscribeurl);
	
	$subscribeurl=$explode_subscribeurl[0];
	if ( isset($_GET['RESPMSG']) ) {
		$RESPMSG=$_GET['RESPMSG'];
	}

	if ( isset($_GET['DESCRIPTION']) ) {
		$bill_desc=$_GET['DESCRIPTION'];
		$bill_desc=explode('_',$bill_desc);
		$bill_payment_type=$bill_desc[1];
		$bill_spid=0;
	}	
	if ( strpos($subscribeurl,'?') !== false ) {
		$subscription_pageurl=$subscribeurl.'&respmsg='.$RESPMSG.'&payment_type='.$bill_payment_type.'&data='.$bill_spid;
	} else {
		$subscription_pageurl=$subscribeurl.'?respmsg='.$RESPMSG.'&payment_type='.$bill_payment_type.'&data='.$bill_spid;				
	}
	
	//mysql_close();	

	
	echo "<script>parent.location='".$subscription_pageurl."';</script>";  	
	


	?>

	
	<?php } else {
		
		$ppa_subscribeurl = current_page_url();
		echo "<script>parent.location='".$ppa_subscribeurl."';</script>";     
	
	} 
}


function current_page_url() {
	 $ppad_pageurl = 'http';
	 if ( isset($_SERVER["HTTPS"]) ) { if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";} }
	 $ppad_pageurl .= "://";
	 if ( $_SERVER["SERVER_PORT"] != "80" ) {
	  $ppad_pageurl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $ppad_pageurl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }	
	 return $ppad_pageurl;
}

function get_payment_setting() {
	global $wpdb;
	$all_payment_setting = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."ppa_paypal_settings where p_settings_id =1 " );	
	return $all_payment_setting;

}


?>