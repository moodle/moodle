<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../paymentprovider.php');
require_once(dirname(__FILE__) . '/paypalfunctions.php');
require_once(dirname(__FILE__) . '/config.php');

function setprop($basket, $propname, $arrayindex, $array, $default = null) {
    if (array_key_exists($arrayindex, $array)) {
        $basket->$propname = $array[$arrayindex];
    } else if ($default) {
        $basket->$propname = $default;
    }
}

class paypal extends payment_provider {
    protected $pre_order_review_processing_html = '';

    public function get_basketpage_html() {

        $_SESSION['Payment_Amount'] = get_basket_total();

        $url = new moodle_url('/blocks/iomad_commerce/checkout/paypal/expresscheckout.php');
        $alttext = get_string('pp_checkout_with_paypal', 'block_iomad_commerce');

        if (has_capability('block/iomad_commerce:buyitnow', context_system::instance())) {
            return "<form action='$url' METHOD='POST'>
                    <input type='image' name='submit' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'
                     border='0' align='top' alt='$alttext'/>
                    </form>";
        } else {
            return;
        }
    }

    public function init() {
        global $USER, $DB, $CFG;

        parent::init();

        $html = '';

        $_SESSION['Payment_Amount'] = get_basket_total();

        // PayPal Express Checkout Module.

        //  The paymentamount is the total value of
        //  the shopping cart, that was set
        //  earlier in a session variable
        //  by the shopping cart page.
        $paymentamount = $_SESSION["Payment_Amount"];

        //  When you integrate this code
        //  set the variables below with
        //  shipping address details
        //  entered by the user on the
        //  Shipping page.

        $basket = get_basket();

        $shiptoname = fullname($USER);
        $shiptostreet = $basket->address;
        $shiptostreet2 = "";
        $shiptocity = $basket->city;
        $shiptostate = $basket->state;
        $shiptocountrycode = $basket->country;
        $shiptozip = $basket->postcode;
        $phonenum = $basket->phone1;

        //  The currencycodetype and paymenttype
        //  are set to the selections made on the Integration Assistant.
        $currencycodetype = $CFG->commerce_admin_currency;
        $paymenttype = "Sale";

        //  The returnurl is the location where buyers return to when a
        //  payment has been succesfully authorized.
        //
        //  This is set to the value entered on the Integration Assistant.

        $returnurl = paypal_returnurl();

        //  The cancelurl is the location buyers are sent to when they hit the
        //  cancel button during authorization of payment during the PayPal flow
        //
        //  This is set to the value entered on the Integration Assistant.
        $cancelurl = paypal_cancelurl();

        // Get the details for the paypal invoice.
        $details = $DB->get_records_sql("SELECT c.fullname, ii.quantity, ii.price from {course} c INNER JOIN {invoiceitem} ii
                                         ON ii.invoiceableitemid=c.id AND invoiceid=".$basket->id);

        //  Calls the SetExpressCheckout API call.
        //
        //  The callmarkexpresscheckout function is defined in the file PayPalFunctions.php,
        //  it is included at the top of this file.
        $resarray = callmarkexpresscheckout ($paymentamount,
                                             $currencycodetype,
                                             $paymenttype,
                                             $returnurl,
                                             $cancelurl,
                                             $shiptoname,
                                             $shiptostreet,
                                             $shiptocity,
                                             $shiptostate,
                                             $shiptocountrycode,
                                             $shiptozip,
                                             $shiptostreet2,
                                             $phonenum,
                                             $details);

        $ack = strtoupper($resarray["ACK"]);
        if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
                $token = urldecode($resarray["TOKEN"]);
                $_SESSION['reshash'] = $token;
                RedirectToPayPal ($token);
        } else {
                // Display a user friendly Error on the page using any of the following error information returned by PayPal.
                $errorcode = urldecode($resarray["L_ERRORCODE0"]);
                $errorshortmessage = urldecode($resarray["L_SHORTMESSAGE0"]);
                $errorlongmesg = urldecode($resarray["L_LONGMESSAGE0"]);
                $errorseveritycode = urldecode($resarray["L_SEVERITYCODE0"]);

                $html .= get_error_table("SetExpressCheckout API call failed.", array(
                    array("Detailed Error Message: ", $errorlongmesg),
                    array("Short Error Message: ", $errorshortmessage),
                    array("Error Code: ", $errorcode),
                    array("Error Severity Code: ", $errorseveritycode)
               ));
        }
        return $html;
    }

    public function pre_order_review_processing() {
        global $DB, $USER;

        $html = '';

        // PayPal Express Checkout Call.
        // Check to see if the Request object contains a variable named 'token'.
        $token = "";
        if (isset($_REQUEST['token'])) {
            $token = $_REQUEST['token'];
        }

        // If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.
        if ($token != "") {

            /*
            '------------------------------------
            ' Calls the GetExpressCheckoutDetails API call
            '
            ' The GetShippingDetails function is defined in PayPalFunctions.jsp
            ' included at the top of this file.
            '-------------------------------------------------
            */

            $resarray = GetShippingDetails($token);
            $ack = strtoupper($resarray["ACK"]);
            if ($ack == "SUCCESS" || $ack == "SUCESSWITHWARNING") {
                /*
                ' The information that is returned by the GetExpressCheckoutDetails call should be integrated
                ' by the partner into his Order Review page
                */
                $basket = new stdClass;
                $basket->id = get_basket_id();
                setprop($basket, 'email',           'EMAIL',                        $resarray, $USER->email);
                setprop($basket, 'pp_payerid',      'PAYERID',                      $resarray);
                setprop($basket, 'pp_payerstatus',  'PAYERSTATUS',                  $resarray);
                setprop($basket, 'firstname',       'FIRSTNAME',                    $resarray);
                setprop($basket, 'lastname',        'LASTNAME',                     $resarray);
                if (!empty($USER->company->name)) {
                    setprop($basket, 'institution',     'BUSINESS',                     $resarray, $USER->company->name);
                } else {
                    setprop($basket, 'institution',     'BUSINESS',                     $resarray);
                }
                setprop($basket, 'city',            'PAYMENTREQUEST_0_SHIPTOCITY',  $resarray);
                setprop($basket, 'state',           'PAYMENTREQUEST_0_SHIPTOSTATE', $resarray);
                setprop($basket, 'postcode',        'PAYMENTREQUEST_0_SHIPTOZIP',   $resarray);
                setprop($basket, 'country',         'COUNTRYCODE',                  $resarray);
                setprop($basket, 'email',           'EMAIL',                        $resarray);
                setprop($basket, 'phone1',          'PHONENUM',                     $resarray, $USER->phone1);

                setprop($basket, 'address',         'PAYMENTREQUEST_0_SHIPTOSTREET', $resarray, '');
                if (array_key_exists("PAYMENTREQUEST_0_SHIPTOSTREET2", $resarray)) {
                    $basket->address .= ', ' . $resarray["PAYMENTREQUEST_0_SHIPTOSTREET2"];
                }

                $DB->update_record('invoice', $basket);
            } else {
                // Display a user friendly Error on the page using any of the following error information returned by PayPal.
                $errorcode = urldecode($resarray["L_ERRORCODE0"]);
                $errorshortmessage = urldecode($resarray["L_SHORTMESSAGE0"]);
                $errorlongmesg = urldecode($resarray["L_LONGMESSAGE0"]);
                $errorseveritycode = urldecode($resarray["L_SEVERITYCODE0"]);

                $html .= get_error_table("GetExpressCheckoutDetails API call failed.", array(
                    array("Detailed Error Message: ", $errorlongmesg),
                    array("Short Error Message: ", $errorshortmessage),
                    array("Error Code: ", $errorcode),
                    array("Error Severity Code: ", $errorseveritycode)
                ));
            }
        }

        $this->pre_order_review_processing_html = $html;
    }

    public function get_order_review_html() {

        $html = '';

        $html .= '<p>' . get_string('pp_paypal_review_instructions', 'block_iomad_commerce') . '</p>';

        $this->pre_order_review_processing_html;

        return $html;
    }

    public function confirm() {
        global $DB;

        $html = '';

        /*==================================================================
         PayPal Express Checkout Call
         ===================================================================
        */

        /*
        '------------------------------------
        ' The paymentamount is the total value of
        ' the shopping cart, that was set
        ' earlier in a session variable
        ' by the shopping cart page
        '------------------------------------
        */

        $finalpaymentamount = $_SESSION["Payment_Amount"];

        /*
        '------------------------------------
        ' Calls the DoExpressCheckoutPayment API call
        '
        ' The confirmpayment function is defined in the file PayPalFunctions.jsp,
        ' that is included at the top of this file.
        '-------------------------------------------------
        */

        $resarray = confirmpayment ($finalpaymentamount);
        $ack = strtoupper($resarray["ACK"]);

        if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
            /*
            '********************************************************************************************************************
            '
            ' THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE
            '                    transactionId & orderTime
            '  IN THEIR OWN  DATABASE
            ' AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT
            '
            '********************************************************************************************************************
            */

            $basket = new stdClass;
            $basket->id = get_basket_id();
            setprop($basket, 'pp_ack',              'ACK',                          $resarray);
            setprop($basket, 'pp_transactionid' ,   'PAYMENTINFO_0_TRANSACTIONID',  $resarray);
            setprop($basket, 'pp_transactiontype',  'PAYMENTINFO_0_TRANSACTIONTYPE', $resarray);
            setprop($basket, 'pp_paymenttype',      'PAYMENTINFO_0_PAYMENTTYPE',    $resarray);
            setprop($basket, 'pp_ordertime',        'PAYMENTINFO_0_ORDERTIME',      $resarray);
            setprop($basket, 'pp_currencycode',     'PAYMENTINFO_0_CURRENCYCODE',   $resarray);
            setprop($basket, 'pp_amount',           'PAYMENTINFO_0_AMT',            $resarray);
            setprop($basket, 'pp_feeamt',           'PAYMENTINFO_0_FEEAMT',         $resarray);
            setprop($basket, 'pp_settleamt',        'PAYMENTINFO_0_SETTLEAMT',      $resarray);
            setprop($basket, 'pp_taxamt',           'PAYMENTINFO_0_TAXAMT',         $resarray);
            setprop($basket, 'pp_exchangerate',     'PAYMENTINFO_0_EXCHANGERATE',   $resarray);
            setprop($basket, 'pp_paymentstatus',    'PAYMENTINFO_0_PAYMENTSTATUS',  $resarray);
            setprop($basket, 'pp_pendingreason',    'PAYMENTINFO_0_PENDINGREASON',  $resarray);
            setprop($basket, 'pp_reason',           'PAYMENTINFO_0_REASONCODE',     $resarray);

            if (strtolower($basket->pp_paymentstatus) == 'completed') {
                $basket->status = INVOICESTATUS_PAID;
            } else {
                $basket->status = INVOICESTATUS_UNPAID;
            }

            $DB->update_record('invoice', $basket);

            return '';
        } else {
            // Display a user friendly Error on the page using any of the following error information returned by PayPal.
            $errorcode = urldecode($resarray["L_ERRORCODE0"]);
            $errorshortmessage = urldecode($resarray["L_SHORTMESSAGE0"]);
            $errorlongmesg = urldecode($resarray["L_LONGMESSAGE0"]);
            $errorseveritycode = urldecode($resarray["L_SEVERITYCODE0"]);

            $html .= get_error_table("GetExpressCheckoutDetails API call failed.", array(
                array("Detailed Error Message: ", $errorlongmesg),
                array("Short Error Message: ", $errorshortmessage),
                array("Error Code: ", $errorcode),
                array("Error Severity Code: ", $errorseveritycode)
            ));
        }

        return $html;
    }

    public function get_confirmation_html($invoice) {
        return '<p>' . get_string('pp_paypal_confirmation', 'block_iomad_commerce', $invoice) . '</p>';
    }
}
