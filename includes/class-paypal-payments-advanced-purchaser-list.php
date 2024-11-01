<?php
/**
 * Paypal_Payments_Advanced_Package_List_Table class that will display our Package table
 * 
 */ 
class Paypal_Payments_Advanced_Purchaser_List_Table extends WP_List_Table {
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'purchaser',
            'plural' => 'purchasers',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default( $item, $column_name ) {
        //print_r($column_name);
		return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_amount( $item ) {
        return '<em>' . $item['amount'] . '</em>';
    }
	
    function column_type( $item ) {
        return  $item['subs_period'] ;
    }
	
    function column_email( $item ) {
        return  '<a target="_blank" href="mailto:'.$item['bill_email'].'">'.$item['bill_email'].' </a>' ;
    }		

    function column_card( $item ) {
        return  'XXXXXXXXXXXX'.$item['card_number'] ;
    } 
	  
    function column_sdate( $item ) {
        return  date('d-m-Y h:i:s',strtotime($item['subs_start_date'])) ;
    }	/**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_name( $item ) {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['subs_id'], __('Delete', 'paypal_payments_advanced')),			
        );

        return sprintf('%s %s',
            $item['bill_fname'],
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['subs_id']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'name' => __('Name', 'paypal_payments_advanced'),			
			'email' => __('Email', 'paypal_payments_advanced'),
            'amount' => __('Amount', 'paypal_payments_advanced'),
			'type' => __('Payment Type', 'paypal_payments_advanced'),
			'card' => __('CC Info', 'paypal_payments_advanced'),
			'sdate' => __('Start Date', 'paypal_payments_advanced'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array('bill_fname', true),
            'amount' => array('amount', false),
			'type' => array('subs_period', false),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ppa_subscriber_table'; // do not forget about tables prefix

        if ( 'delete' === $this->current_action() ) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			$profiles = isset($_REQUEST['profile']) ? $_REQUEST['profile'] : array();
			
            if ( is_array($ids) ) $ids = implode(',', $ids);
			
			if ( is_array($profiles) ) $profiles = implode(',', $profiles);
			
			if( !empty($profiles) && !empty($ids) ){
				
				//echo "Subscription";
				
				$payment_setting_querys=get_payment_setting();
		
				if( $payment_setting_querys ){					
					foreach( $payment_setting_querys as $payment_setting_query ){
						$merchant_email=$payment_setting_query->merchant_email;
						$partner=$payment_setting_query->partner;
						$vendor=$payment_setting_query->vendor;
						$user=$payment_setting_query->user;
						$password=$payment_setting_query->password;
						$payment_mode=$payment_setting_query->payment_mode;
					}
				}

				if( $payment_mode==0 )
				{	
					 $mode='TEST';
					//Setup URLS
					$url = 'https://pilot-payflowpro.paypal.com'; //COMMENT THIS LINE OUT FOR a LIVE TRANSACTION
				
				} else {
					 $mode='LIVE';
					//Setup URLS
					$url = 'https://payflowpro.paypal.com';
					
				}		
				//payments settings info	
				
				$profile_id=$_REQUEST['profile'];
				
				//These are required parameters and must be included in the call.
				$params = array(
				'PARTNER' => $partner,  //Payflow Partner.  This should always be PayPal
				'VENDOR' => $vendor, //Put your manager.paypal.com vendor login here
				'USER' => $user, //Put your manager.paypal.com user login here
				'PWD' => $password, //Put your manager.paypal.com vendor password here 
				'TRXTYPE' => 'R',//Deactivate the recurring profile. PayPal records the cancellation date
				'TENDER' => 'C',//This is the transaction type.  S is for sale, is an Authorization
				'ACTION' => 'C',//This is the transaction type.  S is for sale, is an Authorization
				'ORIGPROFILEID' => $profile_id,//This is the transaction type.  S is for sale, is an Authorization
				);
				
				$querystring = '';
				foreach( $params as $key => $value )
				$querystring .= $key . '=' . $value . '&';
				
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
				foreach($key as $temp) {
					$keyval = explode('=',$temp);
					if( isset($keyval[1]) )
						$responsedata[$keyval[0]] = $keyval[1];
				}
				
				$result=$responsedata['RESULT'];
				$respmsg=$responsedata['RESPMSG'];
				if( ($result==0) && ($respmsg=='Approved') ){
					$wpdb->query( "DELETE FROM $table_name WHERE subs_id IN($ids)" );

				}else{
					
				}
				
			
			}
			elseif ( !empty($ids) ) {
				$wpdb->query( "DELETE FROM $table_name WHERE subs_id IN($ids)" );
			}
			else{
				
			
			}

        }
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ppa_subscriber_table'; // do not forget about tables prefix

        $per_page = 20; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var( "SELECT COUNT(subs_id) FROM $table_name" );

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'bill_fname';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged ), ARRAY_A );

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}

?>