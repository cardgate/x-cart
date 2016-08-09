<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );

function zipfiles($filename, $rootPath){

// Initialize archive object
$zip = new ZipArchive();
$zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::CHILD_FIRST);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();
}

function recurse_copy( $src, $dst, $is_dir ) {
    if ( $is_dir ) {
        // copy directory
        if ( is_dir( $src ) ) {
            if ( $src != '.svn' ) {
                $dir = opendir( $src );
                @mkdir( $dst );
                while ( false !== ( $file = readdir( $dir )) ) {
                    if ( ( $file != '.' ) && ( $file != '..' ) ) {
                        if ( is_dir( $src . '/' . $file ) ) {
                            recurse_copy( $src . '/' . $file, $dst . '/' . $file, true );
                        } else {
                            if ( strpos( $file, '.DS_Store' ) === false ) {
                                copy( $src . '/' . $file, $dst . '/' . $file );
                            }
                        }
                    }
                }
                closedir( $dir );
            }
        } else {
            echo 'dir ' . $src . ' is not found!';
        }
    } else {
        if ( strpos( $src, '.DS_Store' ) === false ) {
            // copy file
            copy( $src, $dst );
        }
    }
}
  
// make file and directory array
function data_element( $src, $dst, $is_dir = false ) {
    $data = array();
    $data['src'] = $src;
    $data['dst'] = $dst;
    $data['isdir'] = $is_dir;
    return $data;
}

// make data

$data = array();


$src = '../include/templater/plugins/modifier.cgpbanks.php';
$dst = 'cardgateplus/include/templater/plugins/modifier.cgpbanks.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_banktransfer.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_banktransfer.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_creditcard.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_creditcard.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_directdebit.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_directdebit.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_directebanking.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_directebanking.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_giropay.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_giropay.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_ideal.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_ideal.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_mistercash.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_mistercash.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_paypal.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_paypal.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_afterpay.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_afterpay.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_klarna.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_klarna.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_bitcoin.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_bitcoin.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../skin/common_files/payments/cc_cgp_przelewy24.tpl';
$dst = 'cardgateplus/install_cardgateplus/files/payments/cc_cgp_przelewy24.tpl';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );

$src = '../payment/cardgateplus/';
$dst = 'cardgateplus/payment/cardgateplus/';
$is_dir = true;
array_push( $data, data_element( $src, $dst, $is_dir ) );

$src = '../payment/cc_cgp_banktransfer.php';
$dst = 'cardgateplus/payment/cc_cgp_banktransfer.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_creditcard.php';
$dst = 'cardgateplus/payment/cc_cgp_creditcard.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_directdebit.php';
$dst = 'cardgateplus/payment/cc_cgp_directdebit.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_directebanking.php';
$dst = 'cardgateplus/payment/cc_cgp_directebanking.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_giropay.php';
$dst = 'cardgateplus/payment/cc_cgp_giropay.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_ideal.php';
$dst = 'cardgateplus/payment/cc_cgp_ideal.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_mistercash.php';
$dst = 'cardgateplus/payment/cc_cgp_mistercash.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_paypal.php';
$dst = 'cardgateplus/payment/cc_cgp_paypal.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_afterpay.php';
$dst = 'cardgateplus/payment/cc_cgp_afterpay.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_bitcoin.php';
$dst = 'cardgateplus/payment/cc_cgp_bitcoin.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_klarna.php';
$dst = 'cardgateplus/payment/cc_cgp_klarna.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cc_cgp_przelewy24.php';
$dst = 'cardgateplus/payment/cc_cgp_przelewy24.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );
$src = '../payment/cgp_notify.php';
$dst = 'cardgateplus/payment/cgp_notify.php';
$is_dir = false;
array_push( $data, data_element( $src, $dst, $is_dir ) );


// copy files

foreach ( $data as $k => $v ) {
        recurse_copy( $v['src'], $v['dst'], $v['isdir'] );
}

// make the zip
echo 'files copied<br>';

// Get real path for our folder
$rootPath = '/home/richard/websites/xcart/htdocs/_plugin/cardgateplus';
$filename = 'cardgateplus.zip';

zipfiles($filename, $rootPath);
echo 'zipfile made<br>';
echo 'done!';
?>