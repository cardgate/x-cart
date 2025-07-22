<meta charset="utf-8">
<?php
error_reporting( 0 );

$statimg = array();
$step = array();
$step["status"] = "ok";
$step["notify"] = "";
$step["error"] = "";
$laststep = 4;
$oldversion = false;
if ( isset( $_GET["cs"] ) ) {
    $current_step = $_GET["cs"];
} else {
    $current_step = 0;
};
$files = array(
    "../payment/cc_cgp_afterpay.php",
    "../payment/cc_cgp_banktransfer.php",
    "../payment/cc_cgp_billink.php",
    "../payment/cc_cgp_bitcoin.php",
    "../payment/cc_cgp_crypto.php",
    "../payment/cc_cgp_creditcard.php",
    "../payment/cc_cgp_directdebit.php",
    "../payment/cc_cgp_directebanking.php",
    "../payment/cc_cgp_giftcard.php",
    "../payment/cc_cgp_ideal.php",
    "../payment/cc_cgp_idealqr.php",
    "../payment/cc_cgp_klarna.php",
    "../payment/cc_cgp_mistercash.php",
	"../payment/cc_cgp_onlineueberweisen.php",
    "../payment/cc_cgp_paypal.php",
    "../payment/cc_cgp_paysafecard.php",
    "../payment/cc_cgp_paysafecash.php",
    "../payment/cc_cgp_przelewy24.php",
	"../payment/cc_cgp_spraypay.php",
    "../payment/cgp_notify.php",
    "../payment/cardgateplus/cardgateplus_lib.php",
    "../payment/cardgateplus/getback.php",
    "../payment/cardgateplus/result.php",
    "../include/templater/plugins/modifier.cgpgiftcarddetails.php"
);

$db_payment_templates = array();
$db_payment_templates['afterpay'] = 'offline.tpl';
$db_payment_templates['banktransfer'] = 'offline.tpl';
$db_payment_templates['billink'] = 'offline.tpl';
$db_payment_templates['bitcoin'] = 'offline.tpl';
$db_payment_templates['crypto'] = 'offline.tpl';
$db_payment_templates['creditcard'] = 'offline.tpl';
$db_payment_templates['directdebit'] = 'offline.tpl';
$db_payment_templates['directebanking'] = 'offline.tpl';
$db_payment_templates['giftcard'] = 'offline.tpl';
$db_payment_templates['ideal'] = 'cgp_ideal.tpl';
$db_payment_templates['idealqr'] = 'offline.tpl';
$db_payment_templates['klarna'] = 'offline.tpl';
$db_payment_templates['mistercash'] = 'offline.tpl';
$db_payment_templates['onlineueberweisen'] = 'offline.tpl';
$db_payment_templates['paypal'] = 'offline.tpl';
$db_payment_templates['paysafecard'] = 'offline.tpl';
$db_payment_templates['paysafecash'] = 'offline.tpl';
$db_payment_templates['przelewy24'] = 'offline.tpl';
$db_payment_templates['spraypay'] = 'offline.tpl';


$payment_names = array();
$payment_names['afterpay'] = 'Afterpay';
$payment_names['banktransfer'] = 'Bank Transfer';
$payment_names['billink'] = 'Billink';
$payment_names['bitcoin'] = 'Bitcoin';
$payment_names['crypto'] = 'Crypto';
$payment_names['creditcard'] = 'Credit Card';
$payment_names['directdebit'] = 'Direct Debit';
$payment_names['directebanking'] = 'DIRECTebanking';
$payment_names['giftcard'] = 'Gift Card';
$payment_names['ideal'] = 'iDEAL';
$payment_names['idealqr'] = 'iDEAL QR';
$payment_names['klarna'] = 'Klarna';
$payment_names['mistercash'] = 'MisterCash';
$payment_names['onlineueberweisen'] = 'OnlineÜberweisen';
$payment_names['paypal'] = 'PayPal';
$payment_names['paysafecard'] = 'Paysafecard';
$payment_names['paysafecash'] = 'Paysafecash';
$payment_names['przelewy24'] = 'Przelewy24';
$payment_names['spraypay'] = 'SprayPay';

$payment_templates = array();
$payment_templates['afterpay'] = 'cgp_afterpay.tpl';
$payment_templates['banktransfer'] = 'cgp_banktransfer.tpl';
$payment_templates['billink'] = 'cgp_billink.tpl';
$payment_templates['bitcoin'] = 'cgp_bitcoin.tpl';
$payment_templates['crypto'] = 'cgp_crypto.tpl';
$payment_templates['creditcard'] = 'cgp_creditcard.tpl';
$payment_templates['directdebit'] = 'cgp_directdebit.tpl';
$payment_templates['directebanking'] = 'cgp_directebanking.tpl';
$payment_templates['giftcard'] = 'cgp_giftcard.tpl';
$payment_templates['ideal'] = 'cgp_ideal.tpl';
$payment_templates['idealqr'] = 'cgp_idealqr.tpl';
$payment_templates['klarna'] = 'cgp_klarna.tpl';
$payment_templates['mistercash'] = 'cgp_mistercash.tpl';
$payment_templates['onlineueberweisen'] = 'cgp_onlineueberweisen.tpl';
$payment_templates['paypal'] = 'cgp_paypal.tpl';
$payment_templates['paysafecard'] = 'cgp_paysafecard.tpl';
$payment_templates['paysafecash'] = 'cgp_paysafecash.tpl';
$payment_templates['przelewy24'] = 'cgp_przelewy24.tpl';
$payment_templates['spraypay'] = 'cgp_spraypay.tpl';


function connectDatabase( $host, $user, $pass, $dbname ) {
    $link = mysqli_connect($host,$user,$pass,$dbname);
    return $link;
}

//Welcome message
if ( ( int ) $current_step == 0 ) {
    $step["notify"] = "This CardGatePlus module is a patch for X-Cart. Configuration and files will be checked before data is added to the database.<br />" .
            "<br />" .
            "Ensure all files have been uploaded to their respective folders before continueing." .
            "Please read the manual for detailed instructions. ";
};

//Read configuration
if ( ( int ) $current_step > 0 ) {
    if ( !file_exists( XCART_CONFIG_FILE ) ) {
        $step["error"] = "X-Cart configuration file not found! Ensure you've uploaded the files in the correct directory.<br />";
    } else {
        include_once(XCART_CONFIG_FILE);
	    if (strpos($sql_host, ':') !== false) {
		    list($host, $port_socket) = explode(':', $sql_host);
	    } else {
		    $host = $sql_host;
		    $port_socket = '';
	    }
	    $link = connectDatabase( $host, $sql_user, $sql_password, $sql_db );
        if ( !$link )
            $step["error"] = "Could not connect to database.<br />";
    };
    if ( !isset( $smarty_skin_dir ) ) {
        $oldversion = true;
        $smarty_skin_dir = DEFAULT_SKIN;
    };

    //Copy files over - versions older then 4.4 use a different folder structure.
    if ( $smarty_skin_dir == "/skin/common_files" ) {
        // Version 4.4
        $base_path = '../skin/common_files/';
    } else {
        // Version 4.1.x -> 4.3
        $base_path = '../skin1/';
    }

    foreach ( $payment_templates as $payment_template ) {
        copy( 'files/payments/cc_' . $payment_template, $base_path . 'payments/cc_' . $payment_template );
    }

    copy( 'files/customer/main/payment_cgp_ideal.tpl', $base_path . 'customer/main/payment_cgp_ideal.tpl' );
    copy( 'files/customer/main/payment_cgp_giftcard.tpl', $base_path . 'customer/main/payment_cgp_giftcard.tpl' );
}

//Check for files
if ( ( int ) $current_step > 1 ) { 

    foreach ( $payment_templates as $payment_template ) {
        $files[] = ".." . $smarty_skin_dir . '/payments/cc_' . $payment_template;
    }

    $files[] = $base_path . 'customer/main/payment_cgp_ideal.tpl';
    $files[] = $base_path . 'customer/main/payment_cgp_giftcard.tpl';

    for ( $i = 0; $i < count( $files ); $i++ ) {
        if ( !file_exists( $files[$i] ) )
            $step["error"] .= "File not found! " . $files[$i] . "<br />";
    };
    if ( $step["error"] )
        $step["error"] .= "<br />Ensure all the files are uploaded and try again<br />";
    //$step["error"] = "";
};

//Install database file
if ( ( int ) $current_step == 3 ) {

    foreach ( $payment_templates as $pm_method => $template ) {

        // Check if exists
        $query = sprintf( "SELECT paymentid FROM %s WHERE processor_file = '%s' LIMIT 1", mysqli_real_escape_string($link, 'xcart_payment_methods' ), 'cc_cgp_'.$pm_method.'.php'
        );
        $result = mysqli_query($link, $query );
        $pm_check = mysqli_fetch_assoc( $result );

        if ( !$pm_check['paymentid'] ) {
            $query = sprintf( file_get_contents( "./database/payment_methods.sql" ), 'xcart_payment_methods', $payment_names[$pm_method], $db_payment_templates[$pm_method], $pm_method, $pm_method );
            $res = mysqli_query( $link, $query );
            if ( !$res ) {
                $step["error"] = "Could not update database table 'xcart_payment_methods'<br />";
            };
            $pm_check['paymentid'] = mysqli_insert_id($link);
        } else {
            // update old parameter
            $query = sprintf( "UPDATE %s SET payment_template ='%s' WHERE paymentid=%s LIMIT 1", mysqli_real_escape_string( $link, 'xcart_payment_methods' ), 'customer/main/payment_'.$db_payment_templates[$pm_method],  $pm_check['paymentid']);
            $res = mysqli_query( $link, $query );
            if ( !$res ) {
                $step["error"] = "Could not update database table 'xcart_payment_methods'<br />";
            };
        }

        if ( !$pm_check['paymentid'] ) {
            $step["error"] .= "Unable to update database<br />";
        } else {
            // Check if exists
            $query = sprintf( "SELECT paymentid FROM %s WHERE processor LIKE '%s' LIMIT 1", mysqli_real_escape_string( $link, 'xcart_ccprocessors' ), 'cc_cgp_' . $pm_method . '.php'
            );
            $result = mysqli_query( $link, $query );
            $processor_check = mysqli_fetch_assoc( $result );
            if ( !$processor_check['paymentid'] ) {
                if ( $pm_method == 'creditcard' ) {
                    $test_mode = 'Y';
                } else {
                    $test_mode = 'N';
                }
                $query = sprintf( file_get_contents( "./database/ccprocessors_4_1.sql" ), 'xcart_ccprocessors', $payment_names[$pm_method], $pm_method, CARDGATEPLUS_PLUGIN_VERSION, $test_mode, $pm_check['paymentid'] );
                $res = mysqli_query( $link, $query );
                if ( !$res ) {
                    $step["error"] = "Could not update database table 'xcart_ccprocessors'<br />";
                };
            } else {
                $query = sprintf( "UPDATE %s SET param01 ='%s' WHERE paymentid=%s LIMIT 1", mysqli_real_escape_string( $link, 'xcart_ccprocessors' ), CARDGATEPLUS_PLUGIN_VERSION, $processor_check['paymentid']
                );
                $res = mysqli_query( $link, $query );
                $processor_check = mysqli_fetch_assoc( $res );
                if ( !$res ) {
                    $step["error"] = "Could not update database table 'xcart_ccprocessors'<br />";
                };
            };
        };
    };
};

$link = 0;

if ( $step["error"] )
    $step["status"] = "error";

//Thank you page
if ( ( int ) $current_step == 4 ) {
    $step["notify"] = "Patch completed successfuly.<br /><br />Before you can use the CardGatePlus payment modules you must configure your <b>CardGate backoffice</b> and the CardGatePlus modules in your <b>X-Cart admin</b> section under Payment Methods.";
    $step["error"] = "Please delete the folder 'install_cardgateplus' from the server for security reasons.";
};

for ( $i = 0; $i <= $laststep; $i++ ) {
    if ( $i > ( int ) $current_step ) {
        $statimg[$i] = "";
    } else {
        if ( $i == ( int ) $current_step ) {
            $statimg[$i] = '<img src="img/' . $step["status"] . '.png" width="16" height="16" />';
        } else {
            $statimg[$i] = '<img src="img/ok.png" width="16" height="16" />';
        };
    };
};
?>