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

require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;

global $api_endpoint, $version, $api_username, $api_password, $api_signature;
global $use_proxy, $proxy_host, $proxy_port;
global $gv_api_errorurl;
global $sbncode, $paypal_url;

/*
PayPal API Module.

Defines all the global variables and the wrapper functions.
*/
$proxy_host = '127.0.0.1';
$proxy_port = '808';

$sandboxflag = ($CFG->paypal_usesandbox ? true : false);

//  PayPal API Credentials.
//  Replace <API_USERNAME> with your API Username.
//  Replace <API_PASSWORD> with your API Password.
//  Replace <API_SIGNATURE> with your Signature.

$api_username = $CFG->paypal_api_username;
$api_password = $CFG->paypal_api_password;
$api_signature = $CFG->paypal_api_signature;


// BN Code is only applicable for partners.
$sbncode = "PP-ECWizard";


/*
 Define the PayPal Redirect URLs.
     This is the URL that the buyer is first sent to do authorize payment with their paypal account
     change the URL depending if you are testing on the sandbox or the live PayPal site.

 For the sandbox, the URL is       https://www.sandbox.paypal.com/webscr&cmd =_express-checkout&token=
 For the live site, the URL is        https://www.paypal.com/webscr&cmd=_express-checkout&token=
*/

if ($sandboxflag == true) {
    $api_endpoint = "https://api-3t.sandbox.paypal.com/nvp";
    $paypal_url = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
} else {
    $api_endpoint = "https://api-3t.paypal.com/nvp";
    $paypal_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
}

$use_proxy = false;
$version = "64";

if (session_id() == "") {
    session_start();
}

/* An express checkout transaction starts with a token, that
   identifies to PayPal your transaction
   In this example, when the script sees a token, the script
   knows that the buyer has already authorized payment through
   paypal.  If no token was found, the action is to send the buyer
   to PayPal to first authorize payment.
   */

/*
 Purpose:     Prepares the parameters for the SetExpressCheckout API Call.
 Inputs:
        paymentamount:      Total value of the shopping cart
        currencycodetype:     Currency code value the PayPal API
        paymenttype:         paymenttype has to be one of the following values: Sale or Order or Authorization
        returnurl:            the page where buyers return to after they are done with the payment review on PayPal
        cancelurl:            the page where buyers return to when they cancel the payment review on PayPal.
*/
function callshortcutexpresscheckout($paymentamount, $currencycodetype, $paymenttype, $returnurl, $cancelurl, $details=null) {
    // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation.

    $nvpstr = "&PAYMENTREQUEST_0_AMT=". $paymentamount;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymenttype;
    $nvpstr = $nvpstr . "&RETURNURL=" . $returnurl;
    $nvpstr = $nvpstr . "&CANCELURL=" . $cancelurl;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencycodetype;

    // Add in the details for the purchase.
    $detailcount = 0;
    if (!empty($details)) {
        foreach ($details as $detail) {
            $nvpstr .= "&L_PAYMENTREQUEST_0_NAME".$detailcount."=".urlencode($detail->fullname);
            $nvpstr .= "&L_PAYMENTREQUEST_0_AMT".$detailcount."=".$detail->price;
            $nvpstr .= "&L_PAYMENTREQUEST_0_QTY".$detailcount."=".$detail->quantity;
            $detailcount++;
        }
    }

    $_SESSION["currencycodetype"] = $currencycodetype;
    $_SESSION["PaymentType"] = $paymenttype;

    //  Make the API call to PayPal.
    //  If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
    //  If an error occured, show the resulting errors.
    $resarray = hash_call("SetExpressCheckout", $nvpstr);
    $ack = strtoupper($resarray["ACK"]);
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        $token = urldecode($resarray["TOKEN"]);
        $_SESSION['TOKEN'] = $token;
    }

    return $resarray;
}

/*
 Purpose:     Prepares the parameters for the SetExpressCheckout API Call.
 Inputs:
        paymentamount:      Total value of the shopping cart
        currencycodetype:     Currency code value the PayPal API
        paymenttype:         paymenttype has to be one of the following values: Sale or Order or Authorization
        returnurl:            the page where buyers return to after they are done with the payment review on PayPal
        cancelurl:            the page where buyers return to when they cancel the payment review on PayPal
        shiptoname:        the Ship to name entered on the merchant's site
        shiptostreet:        the Ship to Street entered on the merchant's site
        shiptocity:            the Ship to City entered on the merchant's site
        shiptostate:        the Ship to State entered on the merchant's site
        shiptocountrycode:    the Code for Ship to Country entered on the merchant's site
        shiptozip:            the Ship to ZipCode entered on the merchant's site
        shiptostreet2:        the Ship to Street2 entered on the merchant's site
        phonenum:            the phonenum  entered on the merchant's site.
*/
function callmarkexpresscheckout($paymentamount, $currencycodetype, $paymenttype, $returnurl,
                                  $cancelurl, $shiptoname, $shiptostreet, $shiptocity, $shiptostate,
                                  $shiptocountrycode, $shiptozip, $shiptostreet2, $phonenum, $details = null
                               ) {
    // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation.

    $nvpstr = "&PAYMENTREQUEST_0_AMT=". $paymentamount;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymenttype;
    $nvpstr = $nvpstr . "&RETURNURL=" . $returnurl;
    $nvpstr = $nvpstr . "&CANCELURL=" . $cancelurl;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencycodetype;
    $nvpstr = $nvpstr . "&ADDROVERRIDE=1";
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTONAME=" . $shiptoname;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET=" . $shiptostreet;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET2=" . $shiptostreet2;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCITY=" . $shiptocity;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTATE=" . $shiptostate;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=" . $shiptocountrycode;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOZIP=" . $shiptozip;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOPHONENUM=" . $phonenum;
    $nvpstr = $nvpstr . "&SOLUTIONTYPE=Sole";


    // Add in the details for the purchase.
    $detailcount = 0;
    if (!empty($details)) {
        foreach ($details as $detail) {
            $nvpstr .= "&L_PAYMENTREQUEST_0_NAME".$detailcount."=".urlencode($detail->fullname);
            $nvpstr .= "&L_PAYMENTREQUEST_0_AMT".$detailcount."=".$detail->price;
            $nvpstr .= "&L_PAYMENTREQUEST_0_QTY".$detailcount."=".$detail->quantity;
            $detailcount++;
        }
    }

    $_SESSION["currencycodetype"] = $currencycodetype;
    $_SESSION["PaymentType"] = $paymenttype;

    //  Make the API call to PayPal.
    //  If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
    //  If an error occured, show the resulting errors.
    $resarray = hash_call("SetExpressCheckout", $nvpstr);
    $ack = strtoupper($resarray["ACK"]);
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        $token = urldecode($resarray["TOKEN"]);
        $_SESSION['TOKEN'] = $token;
    }

    return $resarray;
}

/*
 Purpose:     Prepares the parameters for the getexpresscheckoutdetails API Call.

 Inputs:
        None
 Returns:
        The NVP Collection object of the getexpresscheckoutdetails Call Response.
*/
function getshippingdetails($token) {
    //  At this point, the buyer has completed authorizing the payment
    //  at PayPal.  The function will call PayPal to obtain the details
    //  of the authorization, incuding any shipping information of the
    //  buyer.  Remember, the authorization is not a completed transaction
    //  at this state - the buyer still needs an additional step to finalize
    //  the transaction.

    //  Build a second API request to PayPal, using the token as the
    //   ID to get the details on the payment authorization.
    $nvpstr = "&TOKEN=" . $token;

    //  Make the API call and store the results in an array.
    //     If the call was a success, show the authorization details, and provide
    //      an action to complete the payment.
    $resarray = hash_call("getexpresscheckoutdetails", $nvpstr);
    $ack = strtoupper($resarray["ACK"]);
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        $_SESSION['payer_id'] =    $resarray['PAYERID'];
    }
    return $resarray;
}

/*
 Purpose:     Prepares the parameters for the getexpresscheckoutdetails API Call.

 Inputs:
        sbncode:    The BN code used by PayPal to track the transactions from a given shopping cart.
 Returns:
        The NVP Collection object of the getexpresscheckoutdetails Call Response.
*/
function confirmpayment($finalpaymentamt) {
    /* Gather the information to make the final call to
       finalize the PayPal payment.  The variable nvpstr
       holds the name value pairs.
       */

    // Format the other parameters that were stored in the session from the previous calls.
    $token                 = urlencode($_SESSION['TOKEN']);
    $paymenttype         = urlencode($_SESSION['PaymentType']);
    $currencycodetype     = urlencode($_SESSION['currencycodetype']);
    $payerid             = urlencode($_SESSION['payer_id']);

    $servername         = urlencode($_SERVER['SERVER_NAME']);

    $nvpstr  = '&TOKEN=' . $token .
               '&PAYERID=' . $payerid .
               '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymenttype .
               '&PAYMENTREQUEST_0_AMT=' . $finalpaymentamt;
    $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . $currencycodetype . '&IPADDRESS=' . $servername;

     /* Make the call to PayPal to finalize payment
        If an error occured, show the resulting errors.
        */
    $resarray = hash_call("doexpresscheckoutpayment", $nvpstr);

    /* Display the API response back to the browser.
       If the response from PayPal was a success, display the response parameters'
       If the response was an error, display the errors received using APIError.php.
       */
    $ack = strtoupper($resarray["ACK"]);

    return $resarray;
}

/*
 Purpose:     This function makes a Dodirectpayment API call

 Inputs:
        paymenttype:        paymenttype has to be one of the following values: Sale or Order or Authorization
        paymentamount:      total value of the shopping cart
        currencycode:         currency code value the PayPal API
        firstname:            first name as it appears on credit card
        lastname:            last name as it appears on credit card
        street:                buyer's street address line as it appears on credit card
        city:                buyer's city
        state:                buyer's state
        countrycode:        buyer's country code
        zip:                buyer's zip
        creditcardtype:        buyer's credit card type (i.e. Visa, MasterCard ...)
        creditcardnumber:    buyers credit card number without any spaces, dashes or any other characters
        expdate:            credit card expiration date
        cvv2:                Card Verification Value

 Returns:
        The NVP Collection object of the Dodirectpayment Call Response.
*/


function directpayment($paymenttype, $paymentamount, $creditcardtype, $creditcardnumber,
                        $expdate, $cvv2, $firstname, $lastname, $street, $city, $state, $zip,
                        $countrycode, $currencycode) {
    // Construct the parameter string that describes Dodirectpayment.
    $nvpstr = "&AMT=" . $paymentamount;
    $nvpstr = $nvpstr . "&CURRENCYCODE=" . $currencycode;
    $nvpstr = $nvpstr . "&PAYMENTACTION=" . $paymenttype;
    $nvpstr = $nvpstr . "&CREDITCARDTYPE=" . $creditcardtype;
    $nvpstr = $nvpstr . "&ACCT=" . $creditcardnumber;
    $nvpstr = $nvpstr . "&EXPDATE=" . $expdate;
    $nvpstr = $nvpstr . "&CVV2=" . $cvv2;
    $nvpstr = $nvpstr . "&FIRSTNAME=" . $firstname;
    $nvpstr = $nvpstr . "&LASTNAME=" . $lastname;
    $nvpstr = $nvpstr . "&STREET=" . $street;
    $nvpstr = $nvpstr . "&CITY=" . $city;
    $nvpstr = $nvpstr . "&STATE=" . $state;
    $nvpstr = $nvpstr . "&COUNTRYCODE=" . $countrycode;
    $nvpstr = $nvpstr . "&IPADDRESS=" . $_SERVER['REMOTE_ADDR'];

    $resarray = hash_call("Dodirectpayment", $nvpstr);

    return $resarray;
}


/**
 * hash_call: Function to perform the API call to PayPal using API signature
 * @methodname is name of API  method.
 * @nvpstr is nvp string.
 * returns an associtive array containing the response from the server.
 */
function hash_call($methodname, $nvpstr) {
    // Declaring of global variables.
    global $api_endpoint, $version, $api_username, $api_password, $api_signature;
    global $use_proxy, $proxy_host, $proxy_port;
    global $gv_api_errorurl;
    global $sbncode;

    // Setting the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    // Turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    // If use_proxy constant set to true in Constants.php, then only proxy will be enabled.
    // Set proxy name to proxy_host and port number to proxy_port in constants.php.
    if ($use_proxy) {
        curl_setopt ($ch, CURLOPT_PROXY, $proxy_host. ":" . $proxy_port);
    }

    // NVPRequest for submitting to server.
    $nvpreq = "METHOD=" . urlencode($methodname) .
            "&VERSION=" . urlencode($version) .
            "&PWD=" . urlencode($api_password) .
            "&USER=" . urlencode($api_username) .
            "&SIGNATURE=" . urlencode($api_signature) .
            $nvpstr .
            "&BUTTONSOURCE=" . urlencode($sbncode);

    // Setting the nvpreq as POST FIELD to curl.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    // Getting response from server.
    $response = curl_exec($ch);

    // Convrting NVPResponse to an Associative Array.
    $nvpresarray = deformatnvp($response);
    $nvpreqarray = deformatnvp($nvpreq);
    $_SESSION['nvpreqarray'] = $nvpreqarray;

    if (curl_errno($ch)) {
        // Moving to display page to display curl errors.
          $_SESSION['curl_error_no'] = curl_errno($ch);
          $_SESSION['curl_error_msg'] = curl_error($ch);

          // Execute the Error handling module to display errors.
    } else {
          // Closing the curl.
          curl_close($ch);
    }
    return $nvpresarray;
}

/*
 Purpose: Redirects to PayPal.com site.
 Inputs:  NVP string.
 Returns:.
*/
function redirecttopaypal ($token) {
    global $paypal_url;

    // Redirect to paypal.com here.
    $paypalurl = $paypal_url . $token;
    header("Location: ".$paypalurl);
}


/*
* This function will take NVPString and convert it to an Associative Array and it will decode the response.
* It is usefull to search for a particular key and displaying arrays.
* @nvpstr is NVPString.
* @nvparray is Associative Array.
*/
function deformatnvp($nvpstr) {
    $intial = 0;
     $nvparray = array();

    while (strlen($nvpstr)) {
        // Postion of Key.
        $keypos = strpos($nvpstr, '=');
        // Position of value.
        $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&'): strlen($nvpstr);

        /* Getting the Key and Value values and storing in a Associative Array. */
        $keyval = substr($nvpstr, $intial, $keypos);
        $valval = substr($nvpstr, $keypos + 1, $valuepos-$keypos - 1);
        // Decoding the respose.
        $nvparray[urldecode($keyval)] = urldecode($valval);
        $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
    }
    return $nvparray;
}


