<?php
/*
Plugin Name: Wedothewebs Paypal Advanced
Plugin URI: http://wedothewebs.com/wordpress/ppa_demo/
Description: Wedothewebs Paypal Advanced is the Paypal Payments Advanced eCommerce Plugins for WordPress. It works as a Payment gateway plugin which supports Direct, Recurring and Woocomerce store checkout.
Version:1.0.0
Author: Wedothewebs
Author URI: http://www.wedothewebs.com

Copyright 2015  wedothewebs.com.   (email : info@wedothewebs.com)

Created by wedothewebs.com
(website: wedothewebs.com       email : info@wedothewebs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 3 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if ( is_admin() ){ // admin actions

	//Include the database file
	include("includes/db/database.php");
	include("includes/functions.php");
	
	// Plugin activation
	register_activation_hook( __FILE__, 'paypal_payments_settings_table');
	
	register_activation_hook( __FILE__, 'paypal_payments_period_table');
	
	register_activation_hook( __FILE__, 'paypal_payments_package_table');
	
	register_activation_hook( __FILE__, 'paypal_payments_subscriber_table');
	
	register_activation_hook( __FILE__, 'paypal_payments_settings_install_data');
	
	register_activation_hook( __FILE__, 'paypal_payments_period_install_data');
	
	// Plugin deactivation
	register_deactivation_hook( __FILE__, 'paypal_payments_deactivate');
	
	
	add_action( 'admin_menu', 'paypal_payments_admin_menu' );
	
	
	/**
	 * admin_menu hook implementation
	 */
	function paypal_payments_admin_menu() {
		add_menu_page(__('Paypal Payments Advanced', 'paypal_payments_advanced'), __('Paypal Payments Advanced', 'paypal_payments_advanced'), 'administrator', 'ppa_settings', 'paypal_payments_advanced_paypal_settings_update');
		add_submenu_page('ppa_settings', __('Paypal Settings', 'paypal_payments_advanced'), __('Paypal Settings', 'paypal_payments_advanced'), 'administrator', 'ppa_settings', 'paypal_payments_advanced_paypal_settings_update');
		add_submenu_page('ppa_settings', __('Package Management', 'paypal_payments_advanced'), __('Package Management', 'paypal_payments_advanced'), 'administrator', 'ppa_package_management', 'paypal_payments_advanced_package_list');
		add_submenu_page('ppa_settings', __('Create Package', 'paypal_payments_advanced'), __('Create Package', 'paypal_payments_advanced'), 'administrator', 'ppa_package_create', 'paypal_payments_advanced_package_create');	
		add_submenu_page('ppa_settings', __('View Purchaser', 'paypal_payments_advanced'), __('View Purchaser', 'paypal_payments_advanced'), 'administrator', 'ppa_view_purchaser', 'paypal_payments_advanced_purchaser_list');	
	}
	
	
	/**
	* Create a form
	*/
	function paypal_payments_advanced_paypal_settings_update() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . 'ppa_paypal_settings';
		if ( isset($_REQUEST['nonce']) ) {
			if ( wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__)) ) {

			if ( isset( $_POST['paypal_settings_update'] ) ) {
			//Update
			$settings_merchant_email = sanitize_text_field( $_POST['settings_merchant_email'] );
			$settings_partner = sanitize_text_field( $_POST['settings_partner'] ); 
			$settings_vendor = sanitize_text_field( $_POST['settings_vendor'] );
			$settings_user = sanitize_text_field( $_POST['settings_user'] );
			$settings_password = sanitize_text_field( $_POST['settings_password'] );
			$settings_payment_mode = sanitize_text_field( $_POST['settings_payment_mode'] );
			$updated = $wpdb->update( 
									$table_name, 
									array( 
										'merchant_email' => $settings_merchant_email,	
										'partner' => $settings_partner,	
										'vendor' => $settings_vendor,	
										'user' => $settings_user,
										'password' => $settings_password,
										'payment_mode' => $settings_payment_mode
									), 
									array( 'p_settings_id' => 1 ), 
									array( 
										'%s',	
										'%s',	
										'%s',	
										'%s',	
										'%s',	
										'%d',										
									), 
									array( '%d' ) 
									);
			if ( $updated ) {
				$update_message = _e('Item was successfully updated', 'paypal_payments_advanced');
				
			}									
		}
			}
		} else { 
			//$update_message = 'Item not updated';			
		}
		//Select Data
		$results = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE p_settings_id  = 1' );
		?>
		<div class="wrap">
			<?php screen_icon();?>
			<h2>Paypal Settings</h2>
            <?php if ( isset($update_message) ) {
				echo '<div id="message" class="updated below-h2">';
					echo '<p>'.$update_message.'</p>';
				echo '</div>';				
			}?>
            
			<form action="" method="post">
				<?php settings_fields('paypal-settings-group');?>
				<?php @do_settings_fields('paypal-settings-group');?>
				<div class="metabox-holder" id="poststuff">
					<div id="post-body">
						<div id="post-body-content">  
                        	<div id="package_create_meta_box" class="postbox ">  
                            	<div class="inside">            
                                	<table cellspacing="2" cellpadding="5" class="form-table">
                                    <tbody>
                                        <tr class="form-field">
                                            <th valign="top" scope="row">
                                                <label for="settings_merchant_email"><?php _e('Merchant Email', 'paypal_payments_advanced')?></label>
                                            </th>
                                            <td>
                                                <input id="settings_merchant_email" class="code" type="email" value="<?php echo esc_attr( stripslashes($results->merchant_email ) ); ?>" name="settings_merchant_email" required>
                                            </td>
                                        </tr>                
                                        <tr class="form-field">
                                            <th valign="top" scope="row">
                                                <label for="settings_partner"><?php _e('Partner', 'paypal_payments_advanced')?></label>
                                            </th>
                                            <td>
                                                <input id="settings_partner" class="code" type="text" value="<?php echo esc_attr( stripslashes( $results->partner ) ); ?>"   name="settings_partner" required>
                                            </td>
                                        </tr>
                                        <tr class="form-field">
                                            <th valign="top" scope="row">
                                                <label for="settings_vendor"><?php _e('Vendor', 'paypal_payments_advanced')?> </label>
                                            </th>
                                            <td>
                                                <input id="settings_vendor" class="code" type="text" value="<?php echo esc_attr( stripslashes( $results->vendor ) ); ?>"   name="settings_vendor" required>
                                            </td>
                                        </tr>                                    
                                        <tr class="form-field">
                                            <th valign="top" scope="row">
                                                <label for="settings_user"><?php _e('User', 'paypal_payments_advanced')?> </label>
                                            </th>
                                            <td>
                                                <input id="settings_user" class="code" type="text" value="<?php echo esc_attr( stripslashes( $results->user ) ); ?>" name="settings_user" required>
                                            </td>
                                        </tr> 
                                        <tr class="form-field">
                                            <th valign="top" scope="row">
                                                <label for="settings_password"><?php _e('Password', 'paypal_payments_advanced')?> </label>
                                            </th>
                                            <td>
                                                <input id="settings_password" class="code" type="text" value="<?php echo esc_attr( stripslashes( $results->password ) ); ?>" name="settings_password" required>
                                            </td>
                                        </tr>                     
                                        <tr class="form-field">
                                            <th valign="top" scope="row">
                                                <label for="settings_payment_mode"><?php _e('Payment Mode', 'paypal_payments_advanced')?> </label>
                                            </th>
                                            <td>
                                                <?php  $payment_mode = $results->payment_mode ; ?>
                                                <select name ='settings_payment_mode' id ='settings_payment_mode' class="regular-text all-options">
                                                    <option value ="0" <?php if ($payment_mode == 0) { echo "selected = 'selected'"; } else {}?>>Sandbox</option>
                                                    <option value ="1" <?php if ($payment_mode == 1) { echo "selected = 'selected'"; } else {}?> >Live</option>
                                                </select>                             
                                            </td>
                                        </tr>                     
                      
                                    </tbody>          
                                </table>
                                </div>
                             </div>
                            <p class="submit">
                                <input type="hidden" name="paypal_settings_update" value="1">
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                                <input id="submit" class="button button-primary" type="submit" value="Save Changes" name="submit">
                            </p>                             
                        </div>
                   </div>
               </div>            
			</form>
		</div>
		<?php
	}
	
	/**
	* Package List
	*/
	if ( !class_exists('WP_List_Table') ) {
		require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
	}
	
	require_once 'includes/class-paypal-payments-advanced-package-list.php';
	
	require_once 'includes/class-paypal-payments-advanced-purchaser-list.php';
	
	function paypal_payments_advanced_package_list() {
		global $wpdb;
	
		$table = new Paypal_Payments_Advanced_Package_List_Table();
		$table->prepare_items();
	
		$message = '';
		if ( 'delete' === $table->current_action() ) {
			$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'paypal_payments_advanced'), count($_REQUEST['id'])) . '</p></div>';
		}
		?>
		<div class="wrap">
		
			<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
			<h2><?php _e('Package Management', 'paypal_payments_advanced')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ppa_package_create');?>"><?php _e('Add New', 'paypal_payments_advanced')?></a>
			</h2>
			<?php echo $message; ?>
		
			<form id="persons-table" method="GET">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<?php $table->display() ?>
			</form>
		
		</div>
	<?php
	}
	
	
	function paypal_payments_advanced_purchaser_list() {
		global $wpdb;
	
		$table = new Paypal_Payments_Advanced_Purchaser_List_Table();
		$table->prepare_items();
	
		$message = '';
		if ( 'delete' === $table->current_action() ) {
			$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'paypal_payments_advanced'), count($_REQUEST['id'])) . '</p></div>';
		}
		?>
		<div class="wrap">
		
			<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
			<h2><?php _e('View Purchaser', 'paypal_payments_advanced')?> </h2>
			<?php echo $message; ?>
		
			<form id="frm_purchaser" method="GET">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<?php $table->display() ?>
			</form>
		
		</div>
	<?php
	}
	
	/**
	 * Form page handler checks is there some data posted and tries to save it
	 * Also it renders basic wrapper in which we are callin meta box render
	 */
	function paypal_payments_advanced_package_create() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'ppa_package_table'; // do not forget about tables prefix
	
		$message = '';
		$notice = '';
	
		// this is default $item which will be used for new records
		$default = array(
			'package_id' => 0,
			'package_title' => '',
			'payment_type' => '',
			'package_amount' => null,
			'entry_date'=>date('Y-m-d h:i:s'),
			
		);
		
		
		
	
		// here we are verifying does this request is post back and have correct nonce
		if ( isset($_REQUEST['nonce']) ) {
			if ( wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__)) ) {
			// combine our default item with request params
	
			$item = shortcode_atts($default, $_REQUEST);
	
			// validate data, and if all ok save item to database
			// if id is zero insert otherwise update
			$item_valid = paypal_payments_advanced_validate_package($item);
			if ( $item_valid === true ) {
				if ( $item['package_id'] == 0 ) {
					$result = $wpdb->insert( $table_name, $item );
					$item['package_id'] = $wpdb->insert_id;
					if ( $result ) {
						$message = __('Item was successfully saved', 'paypal_payments_advanced');
					} else {
						$notice = __('There was an error while saving item', 'paypal_payments_advanced');
					}
				} else {
					//print_r($item);
					$result = $wpdb->update( $table_name, $item, array('package_id' => $item['package_id']) );
					if ($result) {
						$message = __('Item was successfully updated', 'paypal_payments_advanced');
					} else {
						$notice = __('There was an error while updating item', 'paypal_payments_advanced');
					}
				}
			} else {
				// if $item_valid not true it contains error message(s)
				$notice = $item_valid;
			}
		}
		}
		else {
			// if this is not post back we load item to edit or give new one to create
			$item = $default;
			if ( isset($_REQUEST['id']) ) {
				$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE package_id = %d" , $_REQUEST['id']), ARRAY_A );
				
				if ( !$item ) {
					$item = $default;
					$notice = __('Item not found', 'paypal_payments_advanced');
				}
			}
		}
	
		// here we adding our custom meta box
		add_meta_box('package_create_meta_box', 'Package data', 'paypal_payments_advanced_package_create_meta_box_handler', 'package', 'normal', 'default');
	
		?>
		<div class="wrap">
			<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
			<h2><?php _e('Create Package', 'paypal_payments_advanced')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ppa_package_management');?>"><?php _e('View List', 'paypal_payments_advanced')?></a>
			</h2>
		
			<?php if ( !empty($notice) ): ?>
			<div id="notice" class="error"><p><?php echo $notice ?></p></div>
			<?php endif;?>
			<?php if ( !empty($message) ): ?>
			<div id="message" class="updated"><p><?php echo $message ?></p></div>
			<?php endif;?>
		
			<form id="form" method="POST">
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
				<?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
				<input type="hidden" name="package_id" value="<?php echo $item['package_id'] ?>"/>
		
				<div class="metabox-holder" id="poststuff">
					<div id="post-body">
						<div id="post-body-content">
							<?php /* And here we call our custom meta box */ ?>
							<?php do_meta_boxes('package', 'normal', $item); ?>
							<input type="submit" value="<?php _e('Save', 'paypal_payments_advanced')?>" id="submit" class="button-primary" name="submit">
						</div>
					</div>
				</div>
			</form>
		</div>
	<?php
	}
	
	/**
	 * This function renders our custom meta box
	 * $item is row
	 *
	 * @param $item
	 */
	function paypal_payments_advanced_package_create_meta_box_handler( $item ) { ?>
    
    	<?php $payment_type='Direct Payment'; ?>
	
		<table cellspacing="2" cellpadding="5" class="form-table">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="package_title"><?php _e('Package Title', 'paypal_payments_advanced')?></label>
				</th>
				<td>
					<input id="package_title" name="package_title" type="text" value="<?php echo esc_attr( stripslashes( $item['package_title'] ) )?>"  class="code" placeholder="<?php _e('Your Package Title', 'paypal_payments_advanced')?>" required>
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="payment_type"><?php _e('Payment Type', 'paypal_payments_advanced')?></label>
				</th>
				<td>
					<select name="payment_type" id="payment_type" style="width: 95%" class="code"  onChange="get_payment_type_change(this.value);">
						<option value="Direct Payment" <?php if ( $item['payment_type']=='Direct Payment' ) echo 'selected=selected'?>>Direct Payment</option>
					</select>
				</td>
			</tr>
	
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="package_amount"><?php _e('Amount', 'paypal_payments_advanced')?></label>
				</th>
				<td>
					<input id="package_amount" name="package_amount" type="number" step="any" min="1" style="width: 95%" 
					value="<?php echo esc_attr($item['package_amount'])?>" size="50" class="code" placeholder="<?php _e('Amount', 'paypal_payments_advanced')?>" required>
				</td>
			</tr>
			</tbody>
		</table>
	<?php
	}
	
	/**
	 * Simple function that validates data and retrieve bool on success
	 * and error message(s) on error
	 *
	 * @param $item
	 * @return bool|string
	 */
	function paypal_payments_advanced_validate_package( $item ) {
		$messages = array();
	
		if ( empty($item['package_title']) ) $messages[] = __('Package Title is required', 'paypal_payments_advanced');
		if ( empty($item['package_amount']) ) $messages[] = __('Amount is required', 'paypal_payments_advanced');

		//subs_period_id
		if ( ($item['payment_type']) == 'Subscription' ) if ( ($item['initial_amount']) == '' ) $messages[] = __('Subscription Initial Amount is required', 'paypal_payments_advanced'); elseif ( ($item['subs_period_id'])=='' ) $messages[] = __('Select a Subscription Period', 'paypal_payments_advanced');
		
		if ( empty($messages) ) return true;
		return implode('<br />', $messages);
	}
	
	
	function get_subsperiod() {
		global $wpdb;
		$all_subsperiod = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."ppa_subs_period ORDER BY subs_period_id" );	
		return $all_subsperiod;
	
	}
	
	/**
	 * Proper way to enqueue scripts and styles
	 */
	
	 
	
	function paypal_payment_scripts() {
		wp_enqueue_style( 'paypal-payment-admin', plugins_url('css/admin/paypal-payment.css', __FILE__) );
			
	}
	
	add_action( 'admin_enqueue_scripts', 'paypal_payment_scripts' );	
	
	/// Plugin removal
	
	function paypal_payments_deactivate() {
		error_log('plugin deactivated');
	}
}else{

// For frontend
	include("includes/functions.php");

	function paypal_payments_advanced_view_package( $attr ) {
	
		global $wpdb;
		$user_ID = get_current_user_id();
		$package_table_name = $wpdb->prefix . 'ppa_package_table'; // do not forget about tables prefix
		$ppa_subs_period_name = $wpdb->prefix . 'ppa_subs_period'; // do not forget about tables prefix
	
		$message = '';
		$notice = '';
		
		$default=array(
			"id" => "1",
			"currency" => "$"
		);
		/* get page url*/
	
		$ppa_pageurl=current_page_url();
		
		$subscription_data = shortcode_atts($default,$attr);
		$get_package_id = $subscription_data['id'];
	
		// this is default $item which will be used for new records
		$default_package = array(
			'bill_to_firstname' => '',
			'bill_to_lastname' => '',
			'bill_to_street' => '',
			'bill_to_city' => '',
			'bill_to_state' => '',
			'bill_to_zip' => '',
			'bill_to_email' => '',
			'bill_to_company_name'=>''
		);
		$item_package = $default_package;	
		
		if ( isset($get_package_id) ) {
	
			if ( is_user_logged_in() ) { // Check User Logged in or not
	
				$item_package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $package_table_name LEFT JOIN $ppa_subs_period_name ON ($package_table_name.subs_period_id = $ppa_subs_period_name.subs_period_id) WHERE $package_table_name.package_id = %d", $get_package_id ), ARRAY_A );		
				
				if ( !$item_package ) {
					$item_package = $default_package;
					$notice = __('Item not found', 'paypal_payments_advanced');
				} else {
					if ( isset($_POST['process_payment']) ) {
						if ( isset($_REQUEST['nonce']) ) {
							if ( wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__)) ) {
								paypal_payments_advanced_subscriptions();
							}
						} else {
							//$notice = __('Some error occured', 'paypal_payments_advanced');
						}
					} elseif ( isset($_POST['paypal_subscription']) ) {
						if ( isset($_REQUEST['nonce']) ) {
							if ( wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__)) ) {
								paypal_payments_advanced_confirm();
							}
						} else {
							//$notice = __('Some error occured', 'paypal_payments_advanced');
						}
					} else {
		
						if ( isset($_GET['respmsg']) ) {

							if ( isset($_GET['payment_type']) ) {
								$bill_payment_type = $_GET['payment_type'];
							}
							if ( $_REQUEST['respmsg'] == 'Approved' ) { ?>
								<?php $current_page = current_page_url(); 
								if ( strpos($current_page,'?respmsg') !== false ) {
									//true
									$back_explode = explode('?respmsg',$current_page);
									$back_btnurl = $back_explode['0'];
								} else {
									$back_explode = explode('&respmsg',$current_page);
									$back_btnurl = $back_explode['0'];
								}
								
								//$back_btn=
								if ( $bill_payment_type == 'Direct Payment' ) { //Direct Payment
									echo '<div class="alert alert-success"><strong>Success !</strong> Thank You, Your direct payment successfully  completed. </div>';
									echo '<a href="'.$back_btnurl.'" class="bck-btn">Back</a>';
																
								} else {
										
								}
							} else {
									echo '<div class="alert alert-warning"><strong>Warning !</strong> Some error occured in your payment. </div>';
							}
						} else {
							if ( isset($_GET['RESPMSG']) ) { //Not Approved
								if ( $_REQUEST['RESPMSG'] == 'Declined' ) {
									echo '<div class="alert alert-warning"><strong>Warning !</strong> Your payment not completed .There was an error with your request. </div>';						
								}
							} 
							?>
							<?php if ( !empty($message) ): ?>
								<div id="message" class="updated"><p><?php echo $message ?></p></div>
							<?php endif; ?> 
							<?php $subs_count = get_subscriber_count($user_ID,$item_package['package_id']);
							if ( $subs_count > 0 ) { 
								
								$subscriber_user_querys = get_subscriber_user( $user_ID, $item_package['package_id'] );
								if ( $subscriber_user_querys ) {
									foreach ( $subscriber_user_querys as $subscriber_user_query ) {
										$bill_fname = $subscriber_user_query->bill_fname;
										$bill_lname = $subscriber_user_query->bill_lname;
										$subs_period = $subscriber_user_query->subs_period;
										$amount = $subscriber_user_query->amount;
										$card_number = $subscriber_user_query->card_number;
										$subs_start_date = date('d-m-Y H:i:s',strtotime($subscriber_user_query->subs_start_date));         
										$status = $subscriber_user_query->status;                          
									}
								} 
							?>                      
	
								<div class="portlet light">
									<div class="recipes-wrap">
									  <div class="recipes-main mt0">
											<div class="wrap content">
												<div class="panel panel-warning">
													<!-- Default panel contents -->
													<div class="panel-heading">
														<h3 class="panel-title">Your subscription plan details:</h3>
													</div>
													<!-- List group -->
													<ul class="list-group">
														<?php if ( $status == 1 ) { ?>
														<li class="list-group-item">
															 <span>Name &nbsp;:&nbsp; </span><?php echo $bill_fname.' '. $bill_lname; ?>
														</li>
														<li class="list-group-item">
															 <span>Subscription Type &nbsp;:&nbsp; </span><?php echo $subs_period; ?>
														</li>
														<li class="list-group-item">
															 <span>Amount &nbsp;:&nbsp; </span><?php echo $subscription_data['currency']; ?> <?php echo $amount; ?>
														</li>
														<?php if ( isset($card_number) ) { ?>
														<li class="list-group-item">
															 <span>Card Number &nbsp;:&nbsp; </span><?php echo 'XXXXXXXXXXXX'.$card_number; ?>
														</li>
														<?php } ?>
														<?php if ( isset($subs_start_date) ) { ?>
														<li class="list-group-item">
															 <span>Start Date &nbsp;:&nbsp; </span><?php echo $subs_start_date; ?>
														</li>
														<?php } ?>
														
														<li class="list-group-item">
															 <span>Status &nbsp;:&nbsp; </span><i class="label label-success"><?php echo 'Active'; ?></i></li>
														</li>
														<?php } ?>
														<?php if ( $status == 2 ) {  ?>
														<li class="list-group-item">
															 <span>Name &nbsp;:&nbsp; </span><?php echo $bill_fname.' '. $bill_lname; ?>
														</li>
														<li class="list-group-item">
															 <span>Subscription Type &nbsp;:&nbsp; </span><?php echo $subs_period; ?>
														</li>
														<li class="list-group-item">
															 <span>Amount &nbsp;:&nbsp; </span><?php echo $subscription_data['currency']; ?> <?php echo $amount; ?>
														</li>
														
														<li class="list-group-item">
															 <span>Status &nbsp;:&nbsp; </span><i class="label label-danger"><?php echo 'Deactive'; ?></i>
														</li>
														<?php } ?>
                                                        <?php if ( $status == 0 ) {  ?>
														<li class="list-group-item">
															 <span>Some Error Occured &nbsp;:&nbsp; </span><?php echo $bill_fname.' '. $bill_lname; ?>
														</li>

														<?php } ?>
													</ul>
												</div>                                                
												
												<div class="clearfix"></div>
										   </div>    
									  </div>
									</div>
								</div>	                        
							
							<?php	
							}else{
							?>
                            
							<form  action="<?php the_permalink(); ?>" method="post" id="formID" name="user_subs_form">                        
	
								<h3 class="ppa-title"><?php _e(esc_attr( stripslashes($item_package['package_title']) ) )?> </h3>						
								<div class="form-group ppa-initial-amount">
									<label class="control-label"><?php _e('Initial Amount', 'paypal_payments_advanced')?> :  </label>
									<span><?php echo $subscription_data['currency'].' '.esc_attr($item_package['initial_amount'])?> </span>
								</div>			 
								<div class="form-group ppa-amount">
									<label class="control-label"><?php _e('Subscribe Amount', 'paypal_payments_advanced')?> :  </label>
									<span><?php echo $subscription_data['currency'].' '.esc_attr($item_package['package_amount'])?> (Per <?php echo esc_attr($item_package['subs_period_name'])?> ) </span>
								</div>  
								<p class="hint"><?php _e('Enter your personal details below', 'paypal_payments_advanced')?> : </p>                                                                                   
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('First Name', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix" placeholder="<?php _e('First Name', 'paypal_payments_advanced')?>" name="bill_to_firstname" id="bill_to_firstname" value="" type="text" required>
								</div>
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('Last Name', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix validate[required]" placeholder="<?php _e('Last Name', 'paypal_payments_advanced')?>" name="bill_to_lastname" id="bill_to_lastname" value="" type="text" required>
								</div>
							   
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('Street', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix validate[required]" placeholder="<?php _e('Street', 'paypal_payments_advanced')?>" name="bill_to_street" id="bill_to_street" value="" type="text" required>
								</div>
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('City', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix validate[required]" placeholder="<?php _e('City', 'paypal_payments_advanced')?>" name="bill_to_city" id="bill_to_city" value="" type="text" required>
								</div>
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('State', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix validate[required]" placeholder="<?php _e('State', 'paypal_payments_advanced')?>" name="bill_to_state" id="bill_to_state" value="" type="text" required>
								</div>
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('Zip', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix validate[required]" placeholder="<?php _e('Zip', 'paypal_payments_advanced')?>" name="bill_to_zip" id="bill_to_zip" value="" type="text" required>
								</div>
								
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('Email', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix validate[required,custom[email]]" placeholder="<?php _e('Email', 'paypal_payments_advanced')?>" name="bill_to_email" id="bill_to_email" value="" type="email" required>
								</div>
								<div class="form-group">
									<label class="control-label visible-ie8 visible-ie9"><?php _e('Company Name', 'paypal_payments_advanced')?></label>
									<input class="form-control placeholder-no-fix validate[required]" placeholder="Company Name" name="bill_to_company_name" id="bill_to_company_name" value="" type="text">
								</div>
								<div class="form-actions">
									<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
									<input type="hidden" name="user_id_session" value="<?php echo $user_ID ; ?>" >
									<input type="hidden" name="amount" value="<?php echo esc_attr( stripslashes($item_package['package_amount'] ) );?>" >
									<input type="hidden" name="initial_amount" value="<?php echo esc_attr( stripslashes($item_package['initial_amount'] ) );?>" >
									<input type="hidden" name="payment_type" value="<?php echo esc_attr( stripslashes($item_package['payment_type'] ) );?>" >
									<input type="hidden" name="spid" value="<?php echo esc_attr( stripslashes($item_package['subs_period_id'] ) );?>" >
									<input type="hidden" name="package_id" value="<?php echo esc_attr( stripslashes($item_package['package_id'] ) );?>" >
									<input type="hidden" name="package_title" value="<?php echo esc_attr( stripslashes($item_package['package_title'] ) );?>" >
									<input type="hidden" name="ppa_pageurl" value="<?php echo $ppa_pageurl?>" >
									<input type="hidden" name="currency" value="<?php echo esc_attr( $subscription_data['currency'] );?>" >                   
									<button type="submit" name="process_payment" value="submit" id="register-submit-btn" class="btn btn-success uppercase pull-right">Process Payment</button>
								</div>
							</form>
		
					<?php  }
					
					} 
						
		
					}
					
				}
			}else{
				
				echo "Please <a href='".site_url()."/wp-login.php'>Log in</a> for subscription";
				
			}
			
		}	
		
	}
	
	add_shortcode( 'view_package', 'paypal_payments_advanced_view_package' );
	
	function paypal_payments_advanced_subscriptions() {
	
		if ( isset($_POST['process_payment']) ) {
			$user_id_session = sanitize_text_field( $_POST['user_id_session'] );
			$amount = sanitize_text_field( $_POST['amount'] );
			$currency = sanitize_text_field( $_POST['currency'] );
			$initial_amount = sanitize_text_field( $_POST['initial_amount'] );
			$spid = sanitize_text_field( $_POST['spid'] );
			$payment_type = sanitize_text_field( $_POST['payment_type'] );
			$package_title = sanitize_text_field( $_POST['package_title'] );
			$ppa_pageurl = sanitize_text_field( $_POST['ppa_pageurl'] );
			$success_site_url = $ppa_pageurl;
			$error_site_url = $ppa_pageurl;
			$cancel_site_url = $ppa_pageurl;
			
			//---------------------------------------Paypal------------------------------------------------------------
			$payment_setting_querys=get_payment_setting();
	
			if ( $payment_setting_querys ) {					
				foreach ( $payment_setting_querys as $payment_setting_query ) {
					$merchant_email = $payment_setting_query->merchant_email;
					$partner = $payment_setting_query->partner;
					$vendor = $payment_setting_query->vendor;
					$user = $payment_setting_query->user;
					$password = $payment_setting_query->password;
					$payment_mode = $payment_setting_query->payment_mode;
				}
			}
			
			//Start building array of parameters for CURL API call
			$params = array(
			
			//These are required parameters and must be included in the call.
			'PARTNER' => $partner,  //Payflow Partner.  This should always be PayPal
			'VENDOR' => $vendor, //Put your manager.paypal.com vendor login here
			'USER' => $user, //Put your manager.paypal.com user login here
			'PWD' => $password, //Put your manager.paypal.com vendor password here
			
			'TRXTYPE' => 'S', 			//This is the transaction type.  S is for sale, is an Authorization
			'CREATESECURETOKEN' => 'Y',	//Tells the payflow server to create a secure token for you
			'SECURETOKENID' =>	md5(uniqid(rand(), true)),  //This needs to be a pseudo random string up to 36 characters.  I am just md5 hashing a pseudo random number
			'AMT' => sanitize_text_field( $_POST['amount'] ),  //Set the Amount of the order.  Needs to be calculated but I am just hardcoding the example.  This needs to be accurate because the token returned is only good for this amount
			'RETURNURL' => $success_site_url,  //Setup your return url.  This is where paypal will return when complete.  See my success.php page
			'ERRORURL' => $error_site_url,  //Setup your error url.  This is where paypal will return when an error occurs.  See my error.php page
			'CANCELURL' => $cancel_site_url,  //Setup your cancel url.  This is where paypal will return when the user cancels an order.  See my cancel.php page
				
			//Optional variables from form post
			'BILLTOFIRSTNAME' => $_POST['bill_to_firstname'],
			'BILLTOLASTNAME' => $_POST['bill_to_lastname'],
			'BILLTOSTREET' => $_POST['bill_to_street'],
			'BILLTOCITY' => $_POST['bill_to_city'],
			'BILLTOSTATE' => $_POST['bill_to_state'],
			'BILLTOZIP' => $_POST['bill_to_zip'],
			'EMAIL' => $_POST['bill_to_email'],
			'DESC' => $_POST['bill_to_company_name'].'_'.$_POST['payment_type'].'_'.$_POST['package_title'].'_'.$_POST['spid'].'_'.$user_id_session.'_'.$_POST['initial_amount'].'_'.$_POST['package_id'],	
			
			);
			
			//In PHP CURL will only accept a string for parameters so I am going to turn my array into a query string
			//DO NOT use http_build_query or any sort of URL encoding on the parameters.
			//Just take the key and value
			$querystring = '';
			foreach($params as $key => $value)
			$querystring .= $key . '=' . $value . '&';
			
			//Setup URLS
		
			if ( $payment_mode == 0 )	{	
				 $mode = 'TEST';
				//Setup URLS
				$url = 'https://pilot-payflowpro.paypal.com'; //COMMENT THIS LINE OUT FOR a LIVE TRANSACTION
				//Setup LInk URLS
				$link_url = 'https://pilot-payflowlink.paypal.com'; //Comment this line for live transaction
			
			} else {
				
				 $mode = 'LIVE';
				//Setup URLS
				$url = 'https://payflowpro.paypal.com';
				//Setup LInk URLS
				$link_url = 'https://payflowlink.paypal.com';
			
			}		
			
			//Setup CURL CALL to get a secure token for the transaction
			$ch = curl_init ();
			curl_setopt ($ch, CURLOPT_URL,$url);
			curl_setopt ($ch, CURLOPT_VERBOSE, 1);
			curl_setopt ($ch, CURLOPT_POST, true);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $querystring);  //Set My query string
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);		//Execute the API Call
			
			//The response is sent back as a string so we need to decode it into an array to use
			$responsedata = array();
			$key = explode('&',$response);
			foreach ( $key as $temp ) {
				$keyval = explode('=',$temp);
				if( isset($keyval[1]) )
					$responsedata[$keyval[0]] = $keyval[1];
			}
			//print_r($responsedata);	//Uncomment to view the decoded response
	
			//---------------------------------------Paypal------------------------------------------------------------
			?>
	
			<h3>Confirmation</h3>
			<p>Confirm order before payment.</p>
		   
			<table class="user_confirmation">
				
				<tbody>
				<tr class="name_value">
					<td class="name_title">Name : </td>
                    
					<td><?php echo esc_attr( stripslashes($_POST['bill_to_firstname'])); ?>&nbsp;<?php echo esc_attr( stripslashes($_POST['bill_to_lastname'])); ?></td>
				</tr>
                <?php if ( $_POST['bill_to_company_name']!='' ) { ?>
				<tr class="name_value">
					<td class="name_title">Company Name : </td>
					<td><?php echo esc_attr( stripslashes( $_POST['bill_to_company_name'] ) ); ?></td>
				</tr> 
                <?php } ?>
				<tr class="name_value">
					<td class="name_title">Amount : </td>
					<td><?php echo esc_attr( stripslashes( $_POST['currency'] ) ).' '. esc_attr( stripslashes( $_POST['amount'] ) ); ?></td>
				</tr>            
										   
				<tr class="name_value">
					<td class="name_title">Email : </td>
					<td><?php echo esc_attr( stripslashes( $_POST['bill_to_email'] ) ); ?></td>
				</tr>							
				</tbody>
			</table>
			
			<div class="paypal_btn">
				<form method="post" name="subs_form_confirmation" action="<?php echo $ppa_pageurl;?>" target="_parent">
					<input type="hidden" name="con_url" value="<?php echo $link_url; ?>">
					<input type="hidden" name="con_token" value="<?php echo esc_attr( $responsedata['SECURETOKEN'] ); ?>">
					<input type="hidden" name="con_tokenid" value="<?php echo esc_attr( $responsedata['SECURETOKENID'] ); ?>">
					<input type="hidden" name="con_mode" value="<?php echo $mode ?>">
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                    <button type="submit" class="paypal_subscription" name="paypal_subscription" value="paypal_subscription" >Confirm Payment</button>
				</form>
			</div>	
			<?php
			
			
		}		
		
	}
	function paypal_payments_advanced_confirm() {
		
	
		if ( isset($_POST['paypal_subscription']) ) {
			
			//echo "paypal_subscription";
			
			if( $_POST['paypal_subscription'] == 'paypal_subscription' ){
				
				$con_url = $_POST['con_url'];
				$con_token = $_POST['con_token'];
				$con_tokenid = $_POST['con_tokenid'];
				$con_mode = $_POST['con_mode']; ?>

				<div class="paypal-confirm-box">
					<iframe id="frame" width="100%" height="700" allowtransparency="true" frameborder="0"  src="<?php echo $con_url ?>?SECURETOKEN=<?php echo $con_token ?>&SECURETOKENID=<?php echo $con_tokenid ?>&MODE=<?php echo $con_mode; ?>" ></iframe>                    
				</div> 
			<?php			
				
			}
			
		}	
	
	}
	
	if ( isset($_GET['RESPMSG']) ) {
		
		if ( $_GET['RESPMSG']=='Approved' ) {
			$bill_desc=$_GET['DESCRIPTION'];
			$bill_desc=explode('_',$bill_desc);
			$bill_company_name=$bill_desc[0];
			$bill_payment_type=$bill_desc[1];
			$bill_package_title=$bill_desc[2];
			$bill_spid=$bill_desc[3];
			$bill_user_id=$bill_desc[4];
			$bill_initial_amount=$bill_desc[5];
			$bill_package_id=$bill_desc[6];
			
			if ( $bill_payment_type=='Direct Payment' ) { //One Time Payment
	
					paypal_payments_advanced_direct($bill_package_title,$bill_spid,$bill_company_name,$bill_user_id,$bill_package_id);			
				
			} else {
				
			}
		} else {
			
		}
	}
	
	
	// For Unsubscription
	
	function paypal_payments_advanced_unsubscriptions( $attr ) {
	
		global $wpdb;
		$user_ID = get_current_user_id();
		$subscriber_table = $wpdb->prefix . 'ppa_subscriber_table'; 
	
		$message = '';
		$notice = '';
		
		$default=array(
			"id"=>"1" ,
			"currency"=>"$"
		);
		
		$subscriber_data=shortcode_atts($default,$attr);
		$get_package_id=$subscriber_data['id'];
	
		if (isset($get_package_id)) {
	
			if ( is_user_logged_in() ) { // Check User Logged in or not ?>
			
				<?php $subs_count=get_subscriber_count($user_ID,$get_package_id);
				if( $subs_count > 0 ){ 
					$subscriber_user_querys=get_subscriber_user($user_ID,$get_package_id)	;
				?>         
					<div class="portlet light">
						<div class="recipes-wrap">
						  <div class="recipes-main mt0">
								<div class="wrap content">
									<?php 
										if( $subscriber_user_querys ){					
											foreach( $subscriber_user_querys as $subscriber_user_query ){
												$bill_fname=$subscriber_user_query->bill_fname;
												$bill_lname=$subscriber_user_query->bill_lname;
												$subs_period=$subscriber_user_query->subs_period;
												$amount=$subscriber_user_query->amount;
												$card_number=$subscriber_user_query->card_number;
												$subs_start_date=date('d-m-Y H:i:s',strtotime($subscriber_user_query->subs_start_date));         
												$status=$subscriber_user_query->status; 
												$profile_id=$subscriber_user_query->profile_id;
												$bill_name=$bill_fname.' '.$bill_lname;
												$bill_email=$subscriber_user_query->bill_email;
																		 
											}
										} 
									
									?>
		
									<div class="panel panel-warning">
										<div class="panel-heading">
											<h3 class="panel-title">Your subscription Active / Deactive :</h3>
										</div>
										<?php //Message Showing
											if ( isset($_GET['activate']) ){
												if( $_GET['activate']=='success' ){ ?>
													<div class="infoMessage-show">
														<div class="planner-wrap">
															<div id="infoMessage" class="portlet-body" style="text-align:center;">
																<div class="alert alert-success">
																	<strong>Success ! </strong>Thank You, Your <?php echo $subs_period;?> successfully activated.
																</div>
															</div>
														</div>
													</div>  
												<?php
												}else if( $_GET['activate']=='fail' ){ 
													$message='';
													if( isset($_GET['message']) ){ $message=$_GET['message']; }
													
													?>
													<div class="infoMessage-show">
														<div class="planner-wrap">
															<div id="infoMessage" class="portlet-body">
																<div class="alert alert-success">
																	<strong>Warning ! </strong>  <?php echo $message;?>
																</div>
															</div>
														</div>
													</div>
												<?php												
												}else{
												}
												
											}
											if ( isset($_GET['deactivate']) ){
												if( $_GET['deactivate']=='success' ){ ?>
													<div class="infoMessage-show">
														<div class="planner-wrap">
															<div id="infoMessage" class="portlet-body">
																<div class="alert alert-success">
																	<strong>Success ! </strong>Thank You, Your <?php echo $subs_period;?> successfully deactivated.
																</div>
															</div>
														</div>
													</div>  
												<?php
												}else if( $_GET['deactivate']=='fail' ){ 
													$message='';
													if( isset($_GET['message']) ){ $message=$_GET['message']; }
													
													?>
													<div class="infoMessage-show">
														<div class="planner-wrap">
															<div id="infoMessage" class="portlet-body" >
																<div class="alert alert-success">
																	<strong>Warning ! </strong>  <?php echo $message;?>
																</div>
															</div>
														</div>
													</div>
												<?php												
												}else{
												}
												
											}
										?>
																		  
										<div class="panel-body">
											<?php global $wp_query;
												//to allow parameters to be passed in the URL and recognized by WordPress
												if ( isset($wp_query->query_vars['subscriptions']) )
												{
													$subscriptions_type=$wp_query->query_vars['subscriptions'];
													if( $subscriptions_type=='deactivate' ){
														paypal_payments_advanced_deactivate($profile_id,$bill_name,$bill_email,$subs_period);
													}else if( $subscriptions_type=='activate' ){
														paypal_payments_advanced_activate($profile_id,$bill_name,$bill_email,$subs_period);
													}
												} 
												$ppa_pageurl=current_page_url();
												
												// need url  check 
												
												if (strpos($ppa_pageurl,'?') !== false) {
													$subs_activate_pageurl=current_page_url().'&subscriptions=activate';
													$subs_deactivate_pageurl=current_page_url().'&subscriptions=deactivate';
												}else{
													$subs_activate_pageurl=current_page_url().'?subscriptions=activate';
													$subs_deactivate_pageurl=current_page_url().'?subscriptions=deactivate';
												}
												
											 ?>
											 
											 <?php if( $status==1 ){ ?>
											 <h4 class="">Your Status &nbsp;:&nbsp; <span ><?php echo "Active";?></span> 
													<a class="subscription_active" href="<?php echo $subs_deactivate_pageurl; ?>"onclick="return confirm('Are you sure to deactivate this subscription ?');"><button value="Deactivate Subscription" class="btn btn-circle default">Deactivate Subscription</button></a>                                     
											 </h4>
											 <?php } else{ ?>
											 <h4 class="">Your Status &nbsp;:&nbsp; <span ><?php echo "Deactive";?></span> 
													<a class="subscription_active" href="<?php echo $subs_activate_pageurl; ?>"onclick="return confirm('Are you sure to activate this subscription ?');">
													<button value="Activate Subscription" class="btn btn-circle default"> Activate Subscription</button></a>  	                                     
											 </h4>
											 <?php } ?>
										  
										</div>                                
									</div>
										
									
									<div class="clearfix"></div>
							   </div>    
						  </div>
						</div>
					</div>        
			<?php
				}else{ ?>
					<div class="panel panel-warning">
						<div class="panel-heading">
							<h3 class="panel-title">Your subscription Active / Deactive :</h3>
						</div>
						<div class="panel-body">
							 <h4 class="">Please subscription at first </h4>
						</div>                                
					</div>
				<?php
				}
				
			}else{
				
				echo "Please <a href='".site_url()."/wp-login.php'>Log in</a> for subscription";
				
			}
		}
	
	}
	
	add_shortcode( 'unsubscribe_package', 'paypal_payments_advanced_unsubscriptions' );
	
	

	function get_subscriber_count( $user_id, $package_no ) {
		global $wpdb;
		$subscriber_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."ppa_subscriber_table where user_id =".$user_id." and package_id=".$package_no." " );
		return $subscriber_count;
	
	}
	function get_subscriber_user( $user_id, $package_no ) {
		global $wpdb;
		
		$subscriber_get = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."ppa_subscriber_table where user_id =".$user_id." and package_id=".$package_no." " );	
		
		return $subscriber_get;
	
	}
	//Custom url
	add_filter( 'query_vars', 'parameter_queryvars' );
	function parameter_queryvars( $qvars ) {
		$qvars[] = 'subscriptions';
		return $qvars;
	}
	
	function paypal_payment_user_scripts() {
		wp_enqueue_style( 'paypal-payment-user', plugins_url('css/paypal-payment-user.css', __FILE__) );
			
	}
	
	add_action( 'wp_enqueue_scripts', 'paypal_payment_user_scripts' );


}



?>