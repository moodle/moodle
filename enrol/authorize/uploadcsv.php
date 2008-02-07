<?php // $Id$

/// Load libraries
    require_once('../../config.php');
    require_once($CFG->libdir.'/uploadlib.php');
    require_once($CFG->dirroot.'/enrol/authorize/const.php');
    require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');

/// Require capabilites
    require_login();
    require_capability('enrol/authorize:uploadcsv', get_context_instance(CONTEXT_USER, $USER->id));

/// Print header
    $struploadcsv = get_string('uploadcsv', 'enrol_authorize');
    $navlinks = array();
    $navlinks[] = array('name' => $struploadcsv, 'link' => "uploadcsv.php", 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header_simple($struploadcsv, "", $navigation);
    print_heading_with_help($struploadcsv, 'uploadcsv', 'enrol/authorize');

/// Handle CSV file
    if (($form = data_submitted()) && confirm_sesskey()) {
        $um = new upload_manager('csvfile', false, false, null, false, 0);
        if ($um->preprocess_files()) {
            $filename = $um->files['csvfile']['tmp_name'];
            // Fix mac/dos newlines
            $text = file_get_contents($filename);
            $text = preg_replace('!\r\n?!', "\n", $text);
            $fp = fopen($filename, "w");
            fwrite($fp, $text);
            fclose($fp);
            authorize_process_csv($filename);
        }
    }

/// Print submit form
    $maxuploadsize = get_max_upload_file_size();
    echo '<center><form method="post" enctype="multipart/form-data" action="uploadcsv.php">
          <input type="hidden" name="MAX_FILE_SIZE" value="'.$maxuploadsize.'" />
          <input type="hidden" name="sesskey" value="'.$USER->sesskey.'">';
          upload_print_form_fragment(1, array('csvfile'), array(get_string('file')));
    echo '<input type="submit" value="'.get_string('upload').'" />';
    echo '</form></center><br />';

/// Print footer
    print_footer();

?><?php

function authorize_process_csv($filename)
{
    global $CFG, $SITE;

/// We need these fields
    $myfields = array(
        'Transaction ID',           // enrol_authorize.transid or enrol_authorize_refunds.transid; See: Reference Transaction ID
        'Transaction Status',       // Under Review,Approved Review,Review Failed,Settled Successfully
        'Transaction Type',         // Authorization w/ Auto Capture, Authorization Only, Capture Only, Credit, Void, Prior Authorization Capture
        'Settlement Amount',        //
        'Settlement Currency',      //
        'Settlement Date/Time',     //
        'Authorization Amount',     //
        'Authorization Currency',   //
        'Submit Date/Time',         // timecreated
        'Reference Transaction ID', // enrol_authorize.transid if Transaction Type = Credit
        'Total Amount',             // enrol_authorize.cost
        'Currency',                 // enrol_authorize.currency
        'Invoice Number',           // enrol_authorize.id: Don't trust this! Backup/Restore changes this
        'Customer ID'               // enrol_authorize.userid
    );

/// Open the file and get first line
    $handle = fopen($filename, "r");
    if (!$handle) {
        error('CANNOT OPEN CSV FILE');
    }
    $firstline = fgetcsv($handle, 8192, ",");
    $numfields = count($firstline);
    if ($numfields != 49 && $numfields != 70) {
        @fclose($handle);
        error('INVALID CSV FILE; Each line must include 49 or 70 fields');
    }

/// Re-sort fields
    $csvfields = array();
    foreach ($myfields as $myfield) {
        $csvindex = array_search($myfield, $firstline);
        if ($csvindex === false) {
            $csvfields = array();
            break;
        }
        $csvfields[$myfield] = $csvindex;
    }
    if (empty($csvfields)) {
        @fclose($handle);
        error("<b>INVALID CSV FILE:</b> First line must include 'Header Fields' and
               the file must be type of <br />'Expanded Fields/Comma Separated'<br />or<br />
              'Expanded Fields with CAVV Result Code/Comma Separated'");
    }

/// Read lines
    $sendem = array();
    $ignoredlines = '';

    $imported = 0;
    $updated = 0;
    $ignored = 0;
    while (($data = fgetcsv($handle, 8192, ",")) !== FALSE) {
        if (count($data) != $numfields) {
            $ignored++; // ignore empty lines
            continue;
        }

        $transid = $data[$csvfields['Transaction ID']];
        $transtype = $data[$csvfields['Transaction Type']];
        $transstatus = $data[$csvfields['Transaction Status']];
        $reftransid = $data[$csvfields['Reference Transaction ID']];
        $settlementdate = strtotime($data[$csvfields['Settlement Date/Time']]);

        if ($transstatus == 'Approved Review' || $transstatus == 'Review Failed') {
            if (($order = get_record('enrol_authorize', 'transid', $transid))) {
                $order->status = ($transstatus == 'Approved Review') ? AN_STATUS_APPROVEDREVIEW : AN_STATUS_REVIEWFAILED;
                update_record('enrol_authorize', $order);
                $updated++; // Updated order status
            }
            continue;
        }

        if (!empty($reftransid) && is_numeric($reftransid) && 'Settled Successfully' == $transstatus && 'Credit' == $transtype) {
            if (($order = get_record('enrol_authorize', 'transid', $reftransid))) {
                if (AN_METHOD_ECHECK == $order->paymentmethod) {
                    $refund = get_record('enrol_authorize_refunds', 'transid', $transid);
                    if ($refund) {
                        $refund->status = AN_STATUS_CREDIT;
                        $refund->settletime = $settlementdate;
                        update_record('enrol_authorize_refunds', $refund);
                        $updated++;
                    }
                    else {
                        $ignored++;
                        $ignoredlines .= $reftransid . ": Not our business(Reference Transaction ID)\n";
                    }
                }
            }
            else {
                $ignored++;
                $ignoredlines .= $reftransid . ": Not our business(Transaction ID)\n";
            }
            continue;
        }

        if (! ($transstatus == 'Settled Successfully' && $transtype == 'Authorization w/ Auto Capture')) {
            $ignored++;
            $ignoredlines .= $transid . ": Not settled\n";
            continue;
        }

        // TransactionId must match
        $order = get_record('enrol_authorize', 'transid', $transid);
        if (!$order) {
            $ignored++;
            $ignoredlines .= $transid . ": Not our business\n";
            continue;
        }

        // Authorized/Captured and Settled
        $order->status = AN_STATUS_AUTHCAPTURE;
        $order->settletime = $settlementdate;
        update_record('enrol_authorize', $order);
        $updated++; // Updated order status and settlement date

        if ($order->paymentmethod != AN_METHOD_ECHECK) {
            $ignored++;
            $ignoredlines .= $transid . ": The method must be echeck\n";
            continue;
        }

        // Get course and context
        $course = get_record('course', 'id', $order->courseid);
        if (!$course) {
            $ignored++;
            $ignoredlines .= $transid . ": Could not find this course: " . $order->courseid . "\n";
            continue;
        }
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        if (!$coursecontext) {
            $ignored++;
            $ignoredlines .= $transid . ": Could not find course context: " . $order->courseid . "\n";
            continue;
        }

        // Get user
        $user = get_record('user', 'id', $order->userid);
        if (!$user) {
            $ignored++;
            $ignoredlines .= $transid . ": Could not find this user: " . $order->userid . "\n";
            continue;
        }

        // If user wasn't enrolled, enrol now. Ignore otherwise. Because admin user might submit this file again.
        if (($role = get_default_course_role($course))) {
            if (! user_has_role_assignment($user->id, $role->id, $coursecontext->id)) {
                $timestart = $timeend = 0;
                if ($course->enrolperiod) {
                    $timestart = time();
                    $timeend = $timestart + $course->enrolperiod;
                }
                if (role_assign($role->id, $user->id, 0, $coursecontext->id, $timestart, $timeend, 0, 'authorize')) {
                    $imported++;
                    if (!empty($CFG->enrol_mailstudents)) {
                        $sendem[] = $order->id;
                    }
                }
                else {
                    $ignoredlines .= $transid . ": Error while trying to enrol " . fullname($user) . " in '$course->fullname' \n";
                }
            }
        }
    }
    fclose($handle);

/// Send email to admin
    if (!empty($ignoredlines)) {
        $admin = get_admin();
        email_to_user($admin, $admin, "$SITE->fullname: Authorize.net CSV ERROR LOG", $ignoredlines);
    }

/// Send welcome messages to users
    if (!empty($sendem)) {
        send_welcome_messages($sendem);
    }

/// Show result
    notice("<b>Done...</b><br />Imported: $imported<br />Updated: $updated<br />Ignored: $ignored");
}

?>
