<?php 

############################ Start ############################
#                                                             														 	#
#	The property of CardGatePlus http://www.cardgate.com                     	       	#
#	Author : Richard Schoots	  							  											#
#	Version : 1.0.1 Created : dt 22-07-2013                      									#
#	Created For X-Cart                      				  												#
#                                                             															#
#	This is the controller file.				              												#
#	Receives postback from CardGate									  							#
#                                                             															#
############################# End ############################

// Load X-Cart configuration
	require_once "./auth.php";
	
	if (!isset($REQUEST_METHOD))
        $REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];
	if ($REQUEST_METHOD = 'POST'
			&& isset($_POST['billing_option']) 
			&& isset($_POST['transaction_id']) 
			&& isset($_POST['ref'])
			&& isset($_POST['hash'])) {
		
		// Load CardGatePlus library class
		require_once "./cardgateplus/cardgateplus_lib.php";
		
		class cgp_gen extends cgp_generic {
		}
		
		$pm_method 	= $_POST['billing_option'];
		
		// instantiate class if the payment method is valid
		$cardgateplus 	= new cgp_gen($pm_method);
		
		// Exit if the CardGatePlus Payment method is not activated.
		if (!func_is_active_payment("cc_cgp_".$pm_method.".php"))
			die("Payment Method not activated");
		
		// process callback
		include_once("./cardgateplus/getback.php");
	} 
?>