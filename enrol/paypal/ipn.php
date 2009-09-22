<?php  // $Id$

/**
* Listens for Instant Payment Notification from PayPal
*
* This script waits for Payment notification from PayPal,
* then double checks that data by sending it back to PayPal.
* If PayPal verifies this then it sets up the enrolment for that
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

/// Read all the data from PayPal and get it ready for later;
/// we expect only valid UTF-8 encoding, it is the responsibility
/// of user to set it up properly in PayPal business acount,
/// it is documented in docs wiki.

    $req = 'cmd=_notify-validate';

    $data = new object();

    foreach ($_POST as $key => $value) {
        $value = stripslashes($value);
        $req .= "&$key=".urlencode($value);
        $data->$key = $value;
    }

    $custom = explode('-', $data->custom);
    $data->userid           = (int)$custom[0];
    $data->courseid         = (int)$custom[1];
    $data->payment_gross    = $data->mc_gross;
    $data->payment_currency = $data->mc_currency;
    $data->timeupdated      = time();


/// get the user and course records

    if (! $user = get_record("user", "id", $data->userid) ) {
        email_paypal_error_to_admin("Not a valid user id", $data);
        die;
    }

    if (! $course = get_record("course", "id", $data->courseid) ) {
        email_paypal_error_to_admin("Not a valid course id", $data);
        die;
    }

    if (! $context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        email_paypal_error_to_admin("Not a valid context id", $data);
        die;
    }

/// Open a connection back to PayPal to validate the data

    $header = '';
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $paypaladdr = empty($CFG->usepaypalsandbox) ? 'www.paypal.com' : 'www.sandbox.paypal.com';
    $fp = fsockopen ($paypaladdr, 80, $errno, $errstr, 30);

    if (!$fp) {  /// Could not open a socket to PayPal - FAIL
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


            // check the payment_status and payment_reason

            // If status is not completed or pending then unenrol the student if already enrolled
            // and notify admin

            if ($data->payment_status != "Completed" and $data->payment_status != "Pending") {
                role_unassign(0, $data->userid, 0, $context->id);
                email_paypal_error_to_admin("Status not completed or pending. User unenrolled from course", $data);
                die;
            }

            // If currency is incorrectly set then someone maybe trying to cheat the system 

            if ($data->mc_currency != $course->currency) {
                email_paypal_error_to_admin("Currency does not match course settings, received: ".addslashes($data->mc_currency), $data);
                die;
            }

            // If status is pending and reason is other than echeck then we are on hold until further notice
            // Email user to let them know. Email admin.

            if ($data->payment_status == "Pending" and $data->pending_reason != "echeck") {
                email_to_user($user, get_admin(), "Moodle: PayPal payment", "Your PayPal payment is pending.");
                email_paypal_error_to_admin("Payment pending", $data);
                die;
            }

            // If our status is not completed or not pending on an echeck clearance then ignore and die
            // This check is redundant at present but may be useful if paypal extend the return codes in the future

            if (! ( $data->payment_status == "Completed" or
                   ($data->payment_status == "Pending" and $data->pending_reason == "echeck") ) ) {
                die;
            }

            // At this point we only proceed with a status of completed or pending with a reason of echeck



            if ($existing = get_record("enrol_paypal", "txn_id", addslashes($data->txn_id))) {   // Make sure this transaction doesn't exist already
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

            if (!$course = get_record('course', 'id', $data->courseid)) { // Check that course exists
                email_paypal_error_to_admin("Course $data->courseid doesn't exist", $data);;
                die;
            }

            // Check that amount paid is the correct amount
            if ( (float) $course->cost < 0 ) {
                $cost = (float) $CFG->enrol_cost;
            } else {
                $cost = (float) $course->cost;
            }

            if ($data->payment_gross < $cost) {
                $cost = format_float($cost, 2);
                email_paypal_error_to_admin("Amount paid is not enough ($data->payment_gross < $cost))", $data);
                die;

            }

            // ALL CLEAR !

            if (!insert_record("enrol_paypal", addslashes_object($data))) {       // Insert a transaction record
                email_paypal_error_to_admin("Error while trying to insert valid transaction", $data);
            }

            if (!enrol_into_course($course, $user, 'paypal')) {
                email_paypal_error_to_admin("Error while trying to enrol ".fullname($user)." in '$course->fullname'", $data);
                die;
            } else {
                $teacher = get_teacher($course->id);

                if (!empty($CFG->enrol_mailstudents)) {
                    $a->coursename = $course->fullname;
                    $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";
                    email_to_user($user, $teacher, get_string("enrolmentnew", '', $course->shortname),
                                  get_string('welcometocoursetext', '', $a));
                }

                if (!empty($CFG->enrol_mailteachers)) {
                    $a->course = $course->fullname;
                    $a->user = fullname($user);
                    email_to_user($teacher, $user, get_string("enrolmentnew", '', $course->shortname),
                                  get_string('enrolmentnewuser', '', $a));
                }

                if (!empty($CFG->enrol_mailadmins)) {
                    $a->course = $course->fullname;
                    $a->user = fullname($user);
                    $admins = get_admins();
                    foreach ($admins as $admin) {
                        email_to_user($admin, $user, get_string("enrolmentnew", '', $course->shortname),
                                      get_string('enrolmentnewuser', '', $a));
                    }
                }

            }


        } else if (strcmp ($result, "INVALID") == 0) { // ERROR
            insert_record("enrol_paypal", addslashes_object($data), false);
            email_paypal_error_to_admin("Received an invalid payment notification!! (Fake payment?)", $data);
        }
    }

    fclose($fp);
    exit;



/// FUNCTIONS //////////////////////////////////////////////////////////////////


function email_paypal_error_to_admin($subject, $data) {
    $admin = get_admin();
    $site = get_site();

    $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";

    foreach ($data as $key => $value) {
        $message .= "$key => $value\n";
    }

    email_to_user($admin, $admin, "PAYPAL ERROR: ".$subject, $message);

}

?>
