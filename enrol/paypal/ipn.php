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


/// Keep out casual intruders
    if (empty($_POST)) {
        error("Sorry, you can not use the script that way.");
    }

/// Read all the data from Paypal and get it ready for later

    $req = 'cmd=_notify-validate';

    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
        $data->$key = $value;
    }

    $data->courseid         = $data->item_number;
    $data->userid           = $data->custom;
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

            } 
            
            if () {   // Check that the email is the one we want it to be

            } 
            
            if (!$user = get_record('user', 'id', $data->userid)) {   // Check that user exists
                email_paypal_error_to_admin("User $data->userid doesn't exist", $data);
            }

            if (!$course = get_record('user', 'id', $data->courseid)) { // Check that course exists
                email_paypal_error_to_admin("Course $data->courseid doesn't exist", $data);
            }

            if () {   // Check that amount paid is the correct amount

            }

            // ALL CLEAR !

            if (!insert_record("enrol_paypal", $data)) {       // Insert a transaction record
                email_paypal_error_to_admin("Error while trying to insert valid transaction", $data);
            }

            if (!enrol_student($user->id, $course->id)) {       // Enrol the student
                email_paypal_error_to_admin("Error while trying to enrol ".fullname($user)." in '$course->fullname'", $data);
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
