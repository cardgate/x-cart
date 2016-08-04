<?php

/* * *******************************************************************************************\
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
  \******************************************************************************************** */

############################ Start ############################
#                                                             	#
#	The property of CardGatePlus http://www.cardgate.com    #
#	Author : Richard Schoots	  			#
#	Version : 1.0.1 Created : dt 22-07-2013                 #
#	Created For X-Cart                                      #
#                                                               #
#	Shows success/error page				#
#                                                               #
############################# End ############################
// Check checksum data
if ( $cardgateplus->OnPage() ) {

    // Convert Reference to Order
    $skey = $_GET['o'];

    if ( $_GET['status'] == 'cancelled' ) {
        $sessionName = $cardgateplus->getSessionField( 'cc_pp3_data' );
        $query = db_query( "SELECT * FROM $sql_tbl[cc_pp3_data] WHERE ref='$skey' LIMIT 1" );
        $qResult = db_fetch_array( $query );
        if ( $qResult['param3'] == 'new' ) {
            // cancel, redirect to cart
            db_query( "UPDATE $sql_tbl[cc_pp3_data] SET param1 = 'cart.php?$XCART_SESSION_NAME=$qResult[$sessionName]&mode=checkout', param3 = 'error' , is_callback = 'N' WHERE ref = '$skey'" );
        } else {
            db_query( "UPDATE $sql_tbl[cc_pp3_data] SET param1 = 'error_message.php?$XCART_SESSION_NAME=$qResult[$sessionName]&error=error_ccprocessor_error&bill_message=Order+is+cancelled+', param3 = 'error' , is_callback = 'N' WHERE ref = '$skey'" );
        }
    }

    // clear cart upon successfull transaction
    if ( $_GET['status'] == 'success' ) {
        x_session_unregister( 'cart', $unset_global = false );
    }
    // Show page with Error message or Invoice depending on Status
    require($xcart_dir . "/payment/payment_ccview.php");
} else {
// Checksum did not match
    die( "Illegal access" );
}
?>