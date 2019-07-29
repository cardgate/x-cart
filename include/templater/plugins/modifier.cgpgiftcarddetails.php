<?php

/* vim: set ts=4 sw=4 sts=4 et: */
/* * ***************************************************************************\
  +-----------------------------------------------------------------------------+
  | X-Cart Software license agreement                                           |
  | Copyright (c) 2001-2013 Qualiteam software Ltd <info@x-cart.com>            |
  | All rights reserved.                                                        |
  +-----------------------------------------------------------------------------+
  | PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
  | FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
  | AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
  |                                                                             |
  | THIS AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
  | SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT QUALITEAM SOFTWARE LTD   |
  | (hereinafter referred to as "THE AUTHOR") OF REPUBLIC OF CYPRUS IS          |
  | FURNISHING OR MAKING AVAILABLE TO YOU WITH THIS AGREEMENT (COLLECTIVELY,    |
  | THE "SOFTWARE"). PLEASE REVIEW THE FOLLOWING TERMS AND CONDITIONS OF THIS   |
  | LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY     |
  | INSTALLING, COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND YOUR COMPANY   |
  | (COLLECTIVELY, "YOU") ARE ACCEPTING AND AGREEING TO THE TERMS OF THIS       |
  | LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT, DO |
  | NOT INSTALL OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL  |
  | PROPERTY RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
  | THAT GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT FOR  |
  | SALE OR FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY  |
  | GRANTED BY THIS AGREEMENT.                                                  |
  +-----------------------------------------------------------------------------+
  \**************************************************************************** */

/**
 * Templater plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     cgpgiftcarddetails
 * Purpose:  generate html Gift card data;
 * -------------------------------------------------------------
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @see        ____file_see____
 */
if ( !defined( 'XCART_START' ) ) {
    header( "Location: ../../../" );
    die( "Access denied" );
}

function smarty_modifier_cgpgiftcarddetails( $value ) {
    require_once './payment/cardgateplus/cardgateplus_lib.php';

    if ( isset( $_COOKIE['cgp_cardnumber'] ) ) {
        unset( $_COOKIE['cgp_cardnumber'] );
        unset( $_COOKIE['cgp_pin'] );
        setcookie( 'cgp_cardnumber', null, -1, '/' );
        setcookie( 'cgp_pin', null, -1, '/' );
    }
    
    class cgp_giftcard extends cgp_generic {
        
    }

    $s = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cardnumber<br>';

    $s.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="cgpcardnumber" name="cgp_cardnumber" value="" onchange="store_giftcarddetails()"><br>';
    $s.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pin<br>';
    $s.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="cgppin" name="cgp_pin" value="" onchange="store_giftcarddetails()">';

    $cardgateplus = new cgp_giftcard( 'giftcard' );
    $s .= $cardgateplus->generateGiftcardScript();

    return $s;
}

?>
