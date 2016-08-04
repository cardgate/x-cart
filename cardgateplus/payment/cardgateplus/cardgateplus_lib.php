<?php

/* * *********************************************************************************\
  +-----------------------------------------------------------------------------+    |
  | X-Cart                                                                            |
  | Copyright (c) 2001-2008 Ruslan R. Fazliev <rrf@rrf.ru>                            |
  | All rights reserved.                                                              |
  +-----------------------------------------------------------------------------+     |
  | PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT"       |
  | FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE        |
  | AT THE FOLLOWING URL: http://www.x-cart.com/license.php                           |
  |                                                                                   |
  | THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE       |
  | THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R.       |
  | FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING       |
  | AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").         |
  | PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT       |
  | CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING,       |
  | COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY       |
  | (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS       |
  | LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS       |
  | AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND       |
  | OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS       |
  | AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE       |
  | THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.      |
  | THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.            |
  |                                                                                   |
  | The Initial Developer of the Original Code is Ruslan R. Fazliev                   |
  | Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2008                 |
  | Ruslan R. Fazliev. All Rights Reserved.                                           |
  +-----------------------------------------------------------------------------+     |
  \********************************************************************************** */

############################ Start ############################
#                                                            														 	#
#	The property of CardGatePlus http://www.cardgate.com             					#
#	                                 						  															#
#	Author : Richard Schoots	  							  											#
#	Version : 1.0.1 Created : dt 22-07-2013                     				 					#
#	Created For X-Cart                      				  												#
#                                                             															#
#	CardGatePlus Library class									  										#
#                                                             															#
############################# End ############################

class cgp_generic {

    var $logDir = './cardgateplus/logs/';
    var $statusCode;
    var $logToFile;
    var $returnData;
    var $_url;
    private $pmType;
    private $version;
    private $testMode;
    private $siteId;
    private $hashKey;
    private $currency;
    private $country;
    private $prefix;
    private $language;
    protected $pm_types = array(
        'creditcard',
        'directebanking',
        'giropay',
        'ideal',
        'mistercash',
        'paypal',
        'directdebit',
        'banktransfer',
        'afterpay',
        'bitcoin',
        'klarna'
    );

    function __construct( $pm_type ) {
        global $sql_tbl;

        $isValidPm = $this->checkPmType( $pm_type );
        if ( !$isValidPm ) {
            die( 'Illegal payment type' );
        }

        $processor = mysql_real_escape_string( 'cc_cgp_' . $pm_type . '.php' );
        $query = sprintf( "SELECT * FROM " . $sql_tbl['ccprocessors'] . " WHERE processor = '%s' LIMIT 1", $processor );
        $result = mysql_query( $query );
        $s = mysql_fetch_assoc( $result );
        $this->version = $s['param01'];
        $this->testMode = $s['param02'];
        $this->siteId = $s['param03'];
        $this->hashKey = $s['param04'];
        $this->currency = $s['param05'];
        $this->country = $s['param06'];
        $this->prefix = $s['param08'];
        $this->language = $s['param09'];
        $this->pmType = $pm_type;

        if ( $s['param07'] == 'Y' ) {
            $this->logToFile = true;
        } else {
            $this->logToFile = false;
        }
        $this->statusCode['NEW'] = "new";
        $this->statusCode['OPEN'] = "open";
        $this->statusCode['PENDING'] = "pending";
        $this->statusCode['ERR'] = "error";
        $this->statusCode['OK'] = "success";
        $this->_url = $this->getUrl();
    }

    function doLogging( $line ) {
        if ( !$this->logToFile )
            return false;

        $filename = sprintf( "%s/#%s.log", $this->logDir, date( "Ymd", time() ) );
        $fp = @fopen( $filename, "a" );
        $line = sprintf( "%s - %s\r\n", date( "H:i", time() ), $line );
        @fwrite( $fp, $line );
        @fclose( $fp );

        return true;
    }

    function setReturnData() {
        $o->transactionID = $_POST['transactionid'];
        $o->siteId = $_POST['site_id'];
        $o->isTest = $_POST['is_test'];
        $o->ref = $_POST['ref'];
        $o->status = $_POST['status'];
        $o->status_id = $_POST['status_id'];
        $o->currency = $_POST['currency'];
        $o->amount = $_POST['amount'];
        $o->billingOption = $_POST['billing_option'];
        $o->hash = $_POST['hash'];
        $o->pmType = $_POST['billing_option'];

        $this->returnData = $o;
    }

    function OnPage() {
        if ( $_SERVER['REQUEST_METHOD'] != 'GET' ) {
            return false;
        } else {
            return true;
        }
    }

    function onCallback() {
        $this->setReturnData();
        return $this->verifyData();
    }

    private function verifyData() {
        global $sql_tbl;

        $this->doLogging( sprintf( "Getback: %s", serialize( $_POST ) ) );

        $orderID = $this->returnData->ref;

        //Get OrderID as integer
        $secureOrderID = func_query_first_cell( "select trstat from $sql_tbl[cc_pp3_data] where ref='" . clean( $orderID ) . "'" );
        $secureOrderID = split( '\|', $secureOrderID, 2 );
        $secureOrderID = $secureOrderID[1];
        $query = sprintf( "SELECT * FROM $sql_tbl[orders] WHERE orderid=%d", $secureOrderID );
        $result = mysql_query( $query );
        $a = mysql_fetch_assoc( $result );
        $hashString = ($this->testMode == 'Y' ? 'TEST' : '') .
                $this->returnData->transactionID .
                $this->currency .
                $a['total'] * 100 .
                $a['orderid'] .
                $this->returnData->status .
                $this->hashKey;

        if ( md5( $hashString ) == $this->returnData->hash ) {
            return true;
        } else {
            return false;
        }
    }

    function getSessionField( $tbl ) {
        global $sql_tbl;
        $table = $sql_tbl[$tbl];
        $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_NAME = '$table' AND COLUMN_NAME = 'sessid'";
        $result = mysql_query( $query );
        $s = mysql_fetch_assoc( $result );

        if ( isset( $s['COLUMN_NAME'] ) ) {
            $sessionName = 'sessid';
        } else {
            $sessionName = 'sessionid';
        }
        return $sessionName;
    }

    function redirect() {
        global $secure_oid;
        global $userinfo;
        global $shop_language;
        global $sql_tbl;
        global $cart;
        global $XCARTSESSID;

        // Create Order ID
        $order_id = $secure_oid[0];

        // Country check
        if ( $this->country == "DETECT" ) {
            $country = $userinfo["b_country"];
        } else {
            $country = "NL"; // Default country
        };

        // Language check
        if ( $this->language == "DETECT" ) {
            if ( in_array( strtoupper( $shop_language ), array( "NL", "EN" ) ) ) {
                $this->language = strtoupper( $shop_language );
            } else {
                $this->language = "EN"; // Default Language
            };
        };

        $cartitems = array();
        $products = $cart['products'];

        foreach ( $products as $product ) {
            $keys = func_query_hash("SELECT * FROM $sql_tbl[products] WHERE productid='".$product['productid']."'" );
            $item = array();
            $item['stock'] = $keys[$productid][0]['avail'];
            $item['quantity'] = $product['amount'];
            $item['sku'] = $product['productcode'];
            $item['name'] = $product['product'];
            $item['price'] = round( $product['price'] * 100, 0 );
            $item['vat_amount'] = round( $this->calculateTax( $product ) * 100, 0 );
            $item['vat_inc'] = 0;
            $item['type'] = 1;
            $cartitems[] = $item;
        }


        if ( $cart['shipping_cost'] > 0 ) {
            $shipping_tax = 0;
            foreach ( $cart['taxes'] as $tax ) {
                $shipping_tax += $tax['tax_cost_shipping'];
            }
            $item = array();
            $item['quantity'] = 1;
            $item['sku'] = 'SHIPPING_' . $cart['shippingid'];
            $item['name'] = $cart['shippingid'];
            $item['price'] = round( $cart['shipping_cost'] * 100, 0 );
            $item['vat_amount'] = round( $shipping_tax * 100, 0 );
            $item['vat_inc'] = 0;
            $item['type'] = 2;
            $cartitems[] = $item;
        }

        $discount = $cart['discount'] + $cart['coupon_discount'];

        if ( $discount > 0 ) {
            $item = array();
            $item['quantity'] = 1;
            $item['sku'] = 'Discount';
            $item['name'] = 'Total discount';
            $item['price'] = round( -1 * $discount * 100, 0 );
            $item['vat'] = 0;
            $item['vat_amount'] = 0;
            $item['vat_inc'] = 0;
            $item['type'] = 4;
            $cartitems[] = $item;
        }
        
        $paymentfee = $cart['payment_surcharge'];
        
        if ( $paymentfee > 0 ) {
            $item = array();
            $item['quantity'] = 1;
            $item['sku'] = 'paymentsurcharge';
            $item['name'] = 'Payment fee';
            $item['price'] = round( $paymentfee * 100, 0 );
            $item['vat'] = 0;
            $item['vat_amount'] = 0;
            $item['vat_inc'] = 0;
            $item['type'] = 5;
            $cartitems[] = $item;
        }

        $fields = array();
        
        $fields['siteid'] = $this->siteId;
        $fields['ref'] = $order_id;
        $fields['first_name'] = $userinfo['firstname'];
        $fields['last_name'] = $userinfo['lastname'];
        $fields['email'] = $userinfo['email'];
        $fields['address'] = $userinfo['b_address'];
        $fields['city'] = $userinfo['b_city'];
        $fields['country_code'] = $userinfo['b_country'];
        $fields['postal_code'] = $userinfo['b_zipcode'];
        $fields['phone_number'] = $userinfo['b_phone'];
        $fields['state'] = $userinfo['b_state'];
        $fields['language'] = $this->language;
        $fields['return_url'] = $this->getHttpHost() . '/payment/cc_cgp_' . $this->pmType . '.php?action=return&status=success&o=' . $order_id;
        $fields['return_url_failed'] = $this->getHttpHost() . '/payment/cc_cgp_' . $this->pmType . '.php?action=return&status=cancelled&o=' . $order_id;
        $fields['shop_name'] = 'X-Cart';
        $fields['shop_version'] = $this->getShopVersion();
        $fields['plugin_name'] = 'Cardgate_' . $this->pmType;
        $fields['plugin_version'] = $this->version;
        $fields['amount'] = ($cart['total_cost'] * 100);
        $fields['currency'] = $this->currency;
        $fields['description'] = 'order_' . $order_id;
        $fields['option'] = $this->pmType;
        if ( count( $cartitems ) > 0 ) {
            $fields['cartitems'] = json_encode( $cartitems, JSON_HEX_APOS | JSON_HEX_QUOT );
        }

        if ( $this->testMode == 'Y' ) {
            $fields['test'] = '1';
            $hash_prefix = 'TEST';
        } else {
            $hash_prefix = '';
        }

        $fields['hash'] = md5( $hash_prefix .
                $this->siteId .
                $fields['amount'] .
                $fields['ref'] .
                $this->hashKey );
        //with an iDEAL transaction, include the bank parameter
        if ( $this->pmType == 'ideal' ) {
            $fields['suboption'] = $_COOKIE['cgp_bank'];
        }

        // Update session status and order data
        $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_NAME = '$sql_tbl[cc_pp3_data]' AND COLUMN_NAME = 'sessid'";
        $result = mysql_query( $query );
        $s = mysql_fetch_assoc( $result );

        if ( isset( $s['COLUMN_NAME'] ) ) {
            $sessionName = 'sessid';
        } else {
            $sessionName = 'sessionid';
        }

        db_query( "REPLACE INTO $sql_tbl[cc_pp3_data] (ref,$sessionName,trstat,param3) VALUES ('" . addslashes( $order_id ) . "','" . $XCARTSESSID . "','CGP|" . implode( '|', $secure_oid ) . "','" . $this->clean( $this->statusCode['NEW'] ) . "')" );

        //Redirect
        func_create_payment_form( $this->_url, $fields, 'Order Form' );
        exit;
    }

    function getHttpHost() {
        global $xcart_http_host;
        global $xcart_https_host;
        global $xcart_web_dir;

        if ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) {
            // SSL connection
            $host = 'https://' . $xcart_https_host . $xcart_web_dir;
        } else {
            $host = 'http://' . $xcart_http_host . $xcart_web_dir;
        }
        return $host;
    }

    function getShopVersion() {
        global $sql_tbl;
        $res = mysql_query( "SELECT value FROM $sql_tbl[config] WHERE name='version'" );
        if ( mysql_num_rows( $res ) < 1 ) {
            $xcart_db_version = "<= 2.4.1";
        } else {
            for ( $i = 0; $i < mysql_num_rows( $res ); $i++ ) {
                list ($version) = mysql_fetch_row( $res );
                if ( $i != 0 )
                    $xcart_db_version .= ", ";
                $xcart_db_version .= $version;
            }
        }
        return $xcart_db_version;
    }

    function clean( $str ) {
        $str = @trim( $str );
        if ( get_magic_quotes_gpc() ) {
            $str = stripslashes( $str );
        }
        return mysql_real_escape_string( ($str ) );
    }

    function checkPmType( $pm_type ) {
        if ( in_array( $pm_type, $this->pm_types ) ) {
            return true;
        }
        return false;
    }

    function generateBankHtml() {
        $html = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';

        if ( $this->getBankOptions() ) {
            $aIssuers = $this->getBankOptions();
        } else {
            $aIssuers = array(
                '0' => 'Choose your bank',
                '0021' => 'Rabobank',
                '0031' => 'ABN Amro',
                '0091' => 'Friesland Bank',
                '0721' => 'ING',
                '0751' => 'SNS Bank',
                '-' => '------ Additional Banks ------',
                '0161' => 'Van Lanschot Bank',
                '0511' => 'Triodos Bank',
                '0761' => 'ASN Bank',
                '0771' => 'SNS Regio Bank',
            );
        }

        $html .= '<select name="bank_options" id="bank_options" style="width:170px;" onchange="store_bank(this.value)">';
        foreach ( $aIssuers as $id => $name ) {
            $html .= '<option value="' . $id . '"';
            if ( isset( $_COOKIE['cgp_bank'] ) && $id == $_COOKIE['cgp_bank'] ) {
                $html .= ' selected="selected" ';
            }
            $html .='">' . $name . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function getBankOptions() {
        $url = 'https://secure.curopayments.net/cache/idealDirectoryCUROPayments.dat';
        if ( !ini_get( 'allow_url_fopen' ) || !function_exists( 'file_get_contents' ) ) {
            $result = false;
        } else {
            $result = file_get_contents( $url );
        }
        if ( $result ) {
            $aBanks = unserialize( $result );
            $aBanks[0] = '-Maak uw keuze a.u.b.-';
            return $aBanks;
        }
        return $result;
    }

    function generateBankScript() {
        $html = '<script type="text/javascript">
						function store_bank(value){
							var exdate=new Date();
							exdate.setDate(exdate.getDate() + 365);
							var c_value=escape(value) + "; expires=" + exdate.toUTCString();
							document.cookie="cgp_bank=" + c_value;
						}
					</script>';
        return $html;
    }

    function getUrl() {
        if ( !empty( $_SERVER['CGP_GATEWAY_URL'] ) ) {
            return $_SERVER['CGP_GATEWAY_URL'];
        } else {
            if ( $this->testMode == 'Y' ) {
                return "https://secure-staging.curopayments.net/gateway/cardgate/";
            } else {
                return "https://secure.curopayments.net/gateway/cardgate/";
            }
        }
    }

    function calculateTax( $product ) {
        $total = 0;
        foreach ( $product['taxes'] as $tax ) {
            $total += $tax['tax_value_precise'];
        }
        return $total;
    }

}

?>
