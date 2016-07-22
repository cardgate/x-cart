<?php
/*********************************************************************************************\
 +-----------------------------------------------------------------------------+
| X-Cart                                                                      															|
| Copyright (c) 2001-2008 Ruslan R. Fazliev <rrf@rrf.ru>                      								|
| All rights reserved.                                                        														|
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" 		|
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  			|
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                    							|
|                                                                             																|
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE 		|
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. 		|
| FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING 					|
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   				|
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT 				|
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, 				|
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY 			|
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS 			|
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS 					|
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND 		|
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS 				|
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE 			|
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.			|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      		|
|                                                                             																|
| The Initial Developer of the Original Code is Ruslan R. Fazliev             									|
| Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2008           							|
| Ruslan R. Fazliev. All Rights Reserved.                                     											|
+-----------------------------------------------------------------------------+
\*********************************************************************************************/

############################ Start ############################
#                                                             														 	#
#	The property of CardGatePlus http://www.cardgate.com                     	       	#
#	Author : Richard Schoots	  							  											#
#	Version : 1.0.1 Created : dt 22-07-2013                      									#
#	Created For X-Cart                      				  												#
#                                                             															#
#	This is the controller file.				              												#
#	Handles callback from CardGate									  								#
#                                                             															#
############################# End ############################

	// Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
	    $str = @trim($str);
		
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		
		return mysql_real_escape_string(($str));
	}

	// Activate PHP Error reporting
	error_reporting(1);
	
	// Load X-Cart data
	x_load("http");
	x_session_register("cart");
	x_session_register("secure_oid");

	// X-Cart data for bill | REQUIRED!
	$bill_output = array();
	
	// Check callback
	if($cardgateplus->onCallback()){	
		//Get OrderID
		$orderID 	= $cardgateplus->returnData->ref;
	
		//Get OrderID as integer
		$secureOrderID = func_query_first_cell("select trstat from $sql_tbl[cc_pp3_data] where ref='".clean($orderID)."'");
		$secureOrderID = split('\|',$secureOrderID,2);
		$secureOrderID = $secureOrderID[1];

		//Get current order status
		$currentStatus = func_query_first_cell("select param3 from $sql_tbl[cc_pp3_data] where ref='".clean($orderID)."'");
		
		// get processed status
		$processedStatus = func_query_first_cell("select param9 from $sql_tbl[cc_pp3_data] where ref='".clean($orderID)."'");
		
		//Status controller
		switch($cardgateplus->returnData->status){
			case 200 :
				if ($currentStatus == $cardgateplus->statusCode['NEW'] || $currentStatus == $cardgateplus->statusCode['OPEN']){
					$bill_output["code"] 	= 1; // success
					$status 						= 'complete';
				};
				break;
			case 300 :
				if ($currentStatus == $cardgateplus->statusCode['NEW'] || $currentStatus == $cardgateplus->statusCode['OPEN']){
					$bill_output["code"] 	= 2; // Error
					$status 						= 'error';
				};
				break;
			case 301 :
				if ($currentStatus == $cardgateplus->statusCode['NEW']){
					$bill_output["code"] 	= 3; // Processing
					$status 						= 'pending';
				};
				break;
		};
		
		//process while cardgate status is not 200
		if ($processedStatus !=200) {
			// Get order session ID, required for processing
			$bill_output["sessid"] = func_query_first_cell("select sessionid from $sql_tbl[cc_pp3_data] where ref='".clean($orderID)."'");
			
			// Add postback data to order
			$bill_output["billmes"] = "CardGatePlus Status: ".$cardgateplus->returnData->status;
	
			// Update status
			db_query("UPDATE $sql_tbl[cc_pp3_data] SET param3 = '".$status."' WHERE ref='". clean($orderID)."' LIMIT 1");
			
			// Update payment method
			db_query("UPDATE $sql_tbl[orders] SET payment_method = 'CardGatePlus: ". $cardgateplus->returnData->pmType."' WHERE orderid='". clean($secureOrderID) ."' LIMIT 1");
			// Convert and include amount and currency, will be validated by processor
			$payment_return = array(
				"total" => (intval($cardgateplus->returnData->amount)/100)
			);
			$payment_return['currency'] 	= $cardgateplus->returnData->currency;
			$payment_return['_currency'] 	= $cardgateplus->currency;
			
			// Process order
			$skey = $orderID;
			require $xcart_dir . '/payment/payment_ccmid.php';
			db_query("UPDATE $sql_tbl[cc_pp3_data] SET param9 = '".$cardgateplus->returnData->status."' WHERE ref='". clean($orderID)."' LIMIT 1");
		}		
		echo $cardgateplus->returnData->transactionID.'.'.$cardgateplus->returnData->status;
		
	} else {
		// Postback incorrect
		die("Illegal access");
	};
?>
