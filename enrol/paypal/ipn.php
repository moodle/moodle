<?php  // $Id$

/**
* Listens for Instant Payment Notification from Paypal
*
* This script waits for Payment notification from Paypal,
* then double checks that data by sending it back to Paypal.
* If Paypal verifies this then it sets up the enrolment for that
* 
* Set the $user->timeaccess course array
*
* @param    user  referenced object, must contain $user->id already set
*/


    require("../../config.php");
    require("enrol.php");

/// Keep out casual intruders
    if (empty($_POST) or !empty($_GET)) {
        error("Sorry, you can not use the script that way.");
    }

/// Read all the data from Paypal and get it ready for later

    $req = 'cmd=_notify-validate';

    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
        $data->$key = urldecode($value);
    }

    $custom = explode('-', $data->custom);
    $data->userid           = $custom[0];
    $data->courseid         = $custom[1];
    $data->payment_amount   = $data->mc_gross;
    $data->payment_currency = $data->mc_currency;


/// Open a connection back to PayPal to validate the data

    $header = '';
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

    if (!$fp) {  /// Could not open a socket to Paypal - FAIL
        echo "<p>Error: could not access paypal.com</p>";
        email_paypal_error_to_admin("Could not access paypal.com to verify payment", $data);
        die;
    }

/// Connection is OK, so now we post the data to validate it

    fputs ($fp, $header.$req);

/// Now read the response and check if everything is OK.

    while (!feof($fp)) {
        $result = fgets($fp, 1024);
        if (strcmp($result, "VERIFIED") == 0) {          // VALID PAYMENT!

            // check the payment_status is Completed

            if ($data->payment_status != "Completed") {   // Not complete?
                email_paypal_error_to_admin("Transaction status is: $data->payment_status", $data);
                die;
            }

            if ($existing = get_record("enrol_paypal", "txn_id", $data->txn_id)) {   // Make sure this transaction doesn't exist already
                email_paypal_error_to_admin("Transaction $data->txn_id is being repeated!", $data);
                die;

            } 
            
            if ($data->business != $CFG->enrol_paypalbusiness) {   // Check that the email is the one we want it to be
                email_paypal_error_to_admin("Business email is $data->business (not $CFG->enrol_paypalbusiness)", $data);
                die;

            } 
            
            if (!$user = get_record('user', 'id', $data->userid)) {   // Check that user exists
                email_paypal_error_to_admin("User $data->userid doesn't exist", $data);
                die;
            }

            if (!$course = get_record('user', 'id', $data->courseid)) { // Check that course exists
                email_paypal_error_to_admin("Course $data->courseid doesn't exist", $data);;
                die;
            }

            // Check that amount paid is the correct amount
            if ( (float) $course->cost < 0 ) {
                $cost = (float) $CFG->enrol_cost;
            } else {
                $cost = (float) $course->cost;
            }
            $cost = format_float($cost, 2);

            if ($data->payment_gross < $cost) {   
                email_paypal_error_to_admin("Amount paid is not enough ($data->payment_gross < $cost))", $data);
                die;

            }

            // ALL CLEAR !

            if (!insert_record("enrol_paypal", $data)) {       // Insert a transaction record
                email_paypal_error_to_admin("Error while trying to insert valid transaction", $data);
            }

            if (!enrol_student($user->id, $course->id)) {       // Enrol the student
                email_paypal_error_to_admin("Error while trying to enrol ".fullname($user)." in '$course->fullname'", $data);
                die;
            } else {
                if (!empty($CFG->enrol_paypalemail)) {
                    $teacher = get_teacher();
                    email_to_user($teacher, $user, get_string("enrolmentnew"), "I have enrolled in your class via Paypal");
                    email_to_user($user, $teacher, get_string("enrolmentnew"), get_string('welcometocoursetext'));
                }
            }


        } else if (strcmp ($result, "INVALID") == 0) { // ERROR
            insert_record("enrol_paypal", $data);
            email_paypal_error_to_admin("Received an invalid payment notification!! (Fake payment?)", $data);
        }
    }

    fclose($fp);
    exit;



/// FUNCTIONS //////////////////////////////////////////////////////////////////


function email_paypal_error_to_admin($subject, $data) {
    $admin = get_admin();
    $site = get_admin();

    $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";

    foreach ($data as $key => $value) {
        $message .= "$key => $value\n";
    }

    email_to_user($admin, $admin, "PAYPAL ERROR: ".$subject, $message);

}

?>
