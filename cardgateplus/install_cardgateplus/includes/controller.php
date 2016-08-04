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
    "../payment/cc_cgp_creditcard.php",
    "../payment/cc_cgp_ideal.php",
    "../payment/cc_cgp_directebanking.php",
    "../payment/cc_cgp_mistercash.php",
    "../payment/cc_cgp_paypal.php",
    "../payment/cc_cgp_giropay.php",
    "../payment/cc_cgp_afterpay.php",
    "../payment/cc_cgp_klarna.php",
    "../payment/cc_cgp_bitcoin.php",
    "../payment/cc_cgp_directdebit.php",
    "../payment/cc_cgp_banktransfer.php",
    "../payment/cgp_notify.php",
    "../payment/cardgateplus/cardgateplus_lib.php",
    "../payment/cardgateplus/getback.php",
    "../payment/cardgateplus/result.php",
    "../include/templater/plugins/modifier.cgpbanks.php"
);

$payment_names = array();
$payment_names['creditcard'] = 'Credit Card';
$payment_names['ideal'] = 'iDEAL';
$payment_names['directebanking'] = 'DIRECTebanking';
$payment_names['mistercash'] = 'MisterCash';
$payment_names['paypal'] = 'PayPal';
$payment_names['giropay'] = 'Giropay';
$payment_names['afterpay'] = 'Afterpay';
$payment_names['bitcoin'] = 'Bitcoin';
$payment_names['klarna'] = 'Klarna';
$payment_names['directdebit'] = 'Direct Debit';
$payment_names['banktransfer'] = 'Bank Transfer';

$payment_templates = array();
$payment_templates['ideal'] = 'cgp_ideal.tpl';
$payment_templates['creditcard'] = 'cgp_creditcard.tpl';
$payment_templates['directebanking'] = 'cgp_directebanking.tpl';
$payment_templates['mistercash'] = 'cgp_mistercash.tpl';
$payment_templates['paypal'] = 'cgp_paypal.tpl';
$payment_templates['giropay'] = 'cgp_giropay.tpl';
$payment_templates['afterpay'] = 'cgp_afterpay.tpl';
$payment_templates['klarna'] = 'cgp_klarna.tpl';
$payment_templates['bitcoin'] = 'cgp_bitcoin.tpl';
$payment_templates['directdebit'] = 'cgp_directdebit.tpl';
$payment_templates['banktransfer'] = 'cgp_banktransfer.tpl';

function connectDatabase( $host, $user, $pass, $dbname ) {
    if ( !mysql_connect( $host, $user, $pass ) ) {
        return false;
    } elseif ( !mysql_select_db( $dbname ) ) {
        return false;
    }

    return true;
}

;


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
        if ( !connectDatabase( $sql_host, $sql_user, $sql_password, $sql_db ) )
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
}

//Check for files
if ( ( int ) $current_step > 1 ) {

    foreach ( $payment_templates as $payment_template ) {
        $files[] = ".." . $smarty_skin_dir . '/payments/cc_' . $payment_template;
    }

    $files[] = $base_path . 'customer/main/payment_cgp_ideal.tpl';

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
        $query = sprintf( "SELECT paymentid FROM %s WHERE payment_template LIKE '%%%s' LIMIT 1", mysql_real_escape_string( 'xcart_payment_methods' ), $template
        );
        $result = mysql_query( $query );
        $pm_check = mysql_fetch_assoc( $result );

        if ( !$pm_check['paymentid'] ) {
            $query = sprintf( file_get_contents( "./database/payment_methods.sql" ), 'xcart_payment_methods', $payment_names[$pm_method], $pm_method );
            $res = mysql_query( $query );
            if ( !$res ) {
                $step["error"] = "Could not update database table 'xcart_payment_methods'<br />";
            };
            $pm_check['paymentid'] = mysql_insert_id();
        };

        if ( !$pm_check['paymentid'] ) {
            $step["error"] .= "Unable to update database<br />";
        } else {
            // Check if exists
            $query = sprintf( "SELECT paymentid FROM %s WHERE processor LIKE '%s' LIMIT 1", mysql_real_escape_string( 'xcart_ccprocessors' ), 'cc_cgp_' . $pm_method . '.php'
            );
            $result = mysql_query( $query );
            $processor_check = mysql_fetch_assoc( $result );
            if ( !$processor_check['paymentid'] ) {
                if ( $pm_method == 'creditcard' ) {
                    $test_mode = 'Y';
                } else {
                    $test_mode = 'N';
                }
                $query = sprintf( file_get_contents( "./database/ccprocessors_4_1.sql" ), 'xcart_ccprocessors', $payment_names[$pm_method], $pm_method, CARDGATEPLUS_PLUGIN_VERSION, $test_mode, $pm_check['paymentid'] );
                $res = mysql_query( $query );
                if ( !$res ) {
                    $step["error"] = "Could not update database table 'xcart_ccprocessors'<br />";
                };
            } else {
                $query = sprintf( "UPDATE %s SET param01 ='%s' WHERE paymentid=%s LIMIT 1", mysql_real_escape_string( 'xcart_ccprocessors' ), CARDGATEPLUS_PLUGIN_VERSION, $processor_check['paymentid']
                );
                $res = mysql_query( $query );
                $processor_check = mysql_fetch_assoc( $res );
                if ( !$res ) {
                    $step["error"] = "Could not update database table 'xcart_ccprocessors'<br />";
                };
            }
        };
    };
};

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