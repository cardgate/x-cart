<?php

/* * ***************************************************************************\
  +-----------------------------------------------------------------------------+
  | X-Cart                                                                      |															|
  | Copyright (c) 2001-2008 Ruslan R. Fazliev <rrf@rrf.ru>                      |
  | All rights reserved.                                                        |														|
  +-----------------------------------------------------------------------------+
  | PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
  | FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
  | AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
  |                                                                             |																|
  | THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
  | THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
  | FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
  | AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
  | PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
  | CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
  | COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
  | (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
  | LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
  | AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
  | OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
  | AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
  | THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
  | THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
  |                                                                             |																|
  | The Initial Developer of the Original Code is Ruslan R. Fazliev             |
  | Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2008           |
  | Ruslan R. Fazliev. All Rights Reserved.                                     |											|
  +-----------------------------------------------------------------------------+
  \*************************************************************************** */

############################ Start ############################
#                                                               #
#	The property of CardGatePlus http://www.cardgate.com    #
#	Author : Richard Schoots                                #
#	Version : 1.0.1 Created : dt 22-07-2013                 #
#	Created For X-Cart                                      #
#                                                             	#
#	This is the controller file.				#
#	Receives postback, shows success/error page             #
#	and redirects to CardGate                               #
#                                                               #
############################# End ############################
// Load X-Cart configuration
require_once "./auth.php";

// Load CardGatePlus library class
require_once "./cardgateplus/cardgateplus_lib.php";

class cgp_spraypay extends cgp_generic {
    
}

$cardgateplus = new cgp_spraypay( 'spraypay' );

// Exit if the CardGatePlus Payment method is not activated.
if ( !func_is_active_payment( "cc_cgp_spraypay.php" ) )
    die( "Payment Method not activated" );

if ( !isset( $REQUEST_METHOD ) )
    $REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

// Main controller:
if ( $REQUEST_METHOD == "POST" && (isset( $_POST['action'] ) && $_POST['action'] == 'place_order') ) {
    // redirect to CardgatePus
    $cardgateplus->redirect();
} elseif ( $REQUEST_METHOD == 'POST' && isset( $_POST['transaction_id'] ) ) {
    // process callback
    include_once("./cardgateplus/getback.php");
} elseif ( isset( $_GET['action'] ) && $_GET['action'] == 'return' ) {
    // Result page (Error or Success)    
    include_once("./cardgateplus/result.php");
}
?>