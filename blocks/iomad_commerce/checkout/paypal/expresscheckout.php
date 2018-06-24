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

require_once("paypalfunctions.php");
require_once("config.php");
require_once("../../lib.php");
require_once("../../processor/processor.php");

require_commerce_enabled();

// Start payment process - update invoice table to know.
global $DB, $SESSION;

$basket = get_basket();
enrich_invoice($basket);
$basket->checkout_method = basename(dirname(__FILE__));
$DB->update_record('invoice', $basket);
processor::trigger_oncheckout($basket->id);

// PayPal Express Checkout Module.

//  The paymentamount is the total value of
//  the shopping cart, that was set
//  earlier in a session variable
//  by the shopping cart page.
$paymentamount = $_SESSION["Payment_Amount"];

//  The currencycodetype and paymenttype
//  are set to the selections made on the Integration Assistant.
$currencycodetype = $CFG->commerce_admin_currency;
$paymenttype = "Sale";

//  The returnurl is the location where buyers return to when a
//  payment has been succesfully authorized.
//  This is set to the value entered on the Integration Assistant.
$returnurl = paypal_returnurl();

//  The cancelurl is the location buyers are sent to when they hit the
//  cancel button during authorization of payment during the PayPal flow.
//  This is set to the value entered on the Integration Assistant.
$cancelurl = paypal_cancelurl();

// Get the details for the paypal invoice.
$details = $DB->get_records_sql("SELECT c.fullname, ii.quantity, ii.price
                                 FROM {course} c INNER JOIN {invoiceitem} ii
                                 ON ii.invoiceableitemid=c.id
                                 AND invoiceid=".$basket->id);

//  Calls the SetExpressCheckout API call.
//  The calshortcutexpresscheckout function is defined in the file PayPalFunctions.php,
//  it is included at the top of this file.
$resarray = callshortcutexpresscheckout($paymentamount, $currencycodetype, $paymenttype, $returnurl, $cancelurl, $details);
$ack = strtoupper($resarray["ACK"]);
if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
    RedirectToPayPal ($resarray["TOKEN"]);
} else {
    // Display a user friendly Error on the page using any of the following error information returned by PayPal.
    $errorcode = urldecode($resarray["L_ERRORCODE0"]);
    $errorshortmsg = urldecode($resarray["L_SHORTMESSAGE0"]);
    $errorlongmsg = urldecode($resarray["L_LONGMESSAGE0"]);
    $errorseveritycode = urldecode($resarray["L_SEVERITYCODE0"]);

    echo "SetExpressCheckout API call failed. ";
    echo "Detailed Error Message: " . $errorlongmsg;
    echo "Short Error Message: " . $errorshortmsg;
    echo "Error Code: " . $errorcode;
    echo "Error Severity Code: " . $errorseveritycode;
}
