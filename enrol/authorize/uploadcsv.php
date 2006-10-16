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
    print_header_simple($struploadcsv, "", "<a href=\"uploadcsv.php\">$struploadcsv</a>");
    print_heading_with_help($struploadcsv, 'uploadcsv', 'enrol/authorize');

/// Handle CSV file
    if ($form = data_submitted() && confirm_sesskey()) {
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
          <input type="hidden" name="MAX_FILE_SIZE" value="'.$maxuploadsize.'">
          <input type="hidden" name="sesskey" value="'.$USER->sesskey.'">';
          upload_print_form_fragment(1, array('csvfile'), array(get_string('file')));
    echo '<input type="submit" value="'.get_string('upload').'">';
    echo '</form></center><br />';

/// Print footer
    print_footer();

?><?php

function authorize_process_csv($filename)
{
    global $CFG;

/// We need these fields
    $myfields = array(
        'Transaction ID',           // enrol_authorize.transid
        'Transaction Status',       // Under Review,Approved Review,Review Failed,Settled Successfully
        'Transaction Type',         // Authorization w/ Auto Capture, Authorization Only, Capture Only, Credit, Void, Prior Authorization Capture
        'Settlement Amount',        //
        'Settlement Currency',      //
        'Settlement Date/Time',     //
        'Authorization Amount',     //
        'Authorization Currency',   //
        'Submit Date/Time',         // timecreated
        'Reference Transaction ID', // enrol_authorize_refunds.transid
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
    $faultlog = '';
    $imported = 0;
    while (($data = fgetcsv($handle, 8192, ",")) !== FALSE) {
        if (count($data) != $numfields) {
            continue; // ignore empty lines
        }

        $transstatus = $data[$csvfields['Transaction Status']];
        $transtype = $data[$csvfields['Transaction Type']];
        $transid = $data[$csvfields['Transaction ID']];
        $settlementdatetime = strtotime($data[$csvfields['Settlement Date/Time']]);

        if ($transstatus == 'Approved Review' || $transstatus == 'Review Failed') {
            if ($order = get_record('enrol_authorize', 'transid', $transid)) {
                $order->status = ($transstatus == 'Approved Review') ? AN_STATUS_APPROVEDREVIEW : AN_STATUS_REVIEWFAILED;
                update_record('enrol_authorize', $order);
            }
            continue;
        }

        // We want only status=Settled Successfully and type=Authorization w/ Auto Capture
        if (! ($transstatus == 'Settled Successfully' && $transtype == 'Authorization w/ Auto Capture')) {
            $ignoredlines .= $transid . "\n";
            continue;
        }

        // TransactionId must match
        $order = get_record('enrol_authorize', 'transid', $transid);
        if (!$order) { // Not our business
            $ignoredlines .= $transid . "\n";
            continue;
        }

        // Authorized/Captured and Settled
        $order->status = AN_STATUS_AUTHCAPTURE;
        $order->settletime = $settlementdatetime;
        update_record('enrol_authorize', $order);

        if ($order->paymentmethod != AN_METHOD_ECHECK) {
            $ignoredlines .= $transid . "\n";
            continue; // We only interest in ECHECK
        }

        // Get course and context
        $course = get_record('course', 'id', $order->courseid);
        if (!$course) {
            $ignoredlines .= $transid . "\n";
            continue; // Could not find this course
        }
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        if (!$coursecontext) {
            $ignoredlines .= $transid . "\n";
            continue; // Could not find this course context
        }

        // Get user
        $user = get_record('user', 'id', $order->userid);
        if (!$user) {
            $ignoredlines .= $transid . "\n";
            continue; // Could not find this user
        }

        // If user wasn't enrolled, enrol now. Ignore otherwise. Because admin user might submit this file again.
        if ($role = get_default_course_role($course)) {
            $ra = get_record('role_assignments', 'roleid', $role->id, 'contextid', $coursecontext->id, 'userid', $user->id);
            if (empty($ra)) { // Not enrolled, so enrol
                $timestart = $timeend = 0;
                if ($course->enrolperiod) {
                    $timestart = time();
                    $timeend = $timestart + $course->enrolperiod;
                }
                if (role_assign($role->id, $user->id, 0, $coursecontext->id, $timestart, $timeend, 0, 'manual')) {
                    $imported++;
                    if (!empty($CFG->enrol_mailstudents)) {
                        $sendem[] = $order->id;
                    }
                }
                else {
                    $faultlog .= "Error while trying to enrol ".fullname($user)." in '$course->fullname' \n";
                }
            }
        }
    }
    fclose($handle);
    notice("Done... Total $imported record(s) has been imported.");

/// Send welcome messages to users
    if (!empty($sendem)) {
        send_welcome_messages($sendem);
    }
}

?>
