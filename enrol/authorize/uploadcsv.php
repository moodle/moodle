<?php

/// Load libraries
    require_once('../../config.php');
    require_once($CFG->libdir.'/uploadlib.php');
    require_once($CFG->dirroot.'/enrol/authorize/const.php');
    require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');
    require_once($CFG->libdir.'/eventslib.php');

/// Require capabilites
    require_login();
    require_capability('enrol/authorize:uploadcsv', get_context_instance(CONTEXT_SYSTEM));

/// Print header
    $struploadcsv = get_string('uploadcsv', 'enrol_authorize');
    $managebutton = "<form method='get' action='index.php'><div><input type='submit' value='".get_string('paymentmanagement', 'enrol_authorize')."' /></div></form>";

    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/enrol/authorize/uploadcsv.php'));
    $PAGE->navbar->add(get_string('paymentmanagement', 'enrol_authorize'), 'index.php');
    $PAGE->navbar->add($struploadcsv, 'uploadcsv.php');
    $PAGE->set_title($struploadcsv);
    $PAGE->set_cacheable(false);
    $PAGE->set_button($managebutton);
    echo $OUTPUT->header();

    $helpicon = new moodle_help_icon();
    $helpicon->text = $struploadcsv;
    $helpicon->page = 'authorize/uploadcsv';
    $helpicon->module = 'enrol';
    echo $OUTPUT->heading_with_help($helpicon);

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
    echo '<center><form method="post" enctype="multipart/form-data" action="uploadcsv.php"><div>
          <input type="hidden" name="MAX_FILE_SIZE" value="'.$maxuploadsize.'" />
          <input type="hidden" name="sesskey" value="'.sesskey().'" />';
          upload_print_form_fragment(1, array('csvfile'), array(get_string('file')));
    echo '<input type="submit" value="'.get_string('upload').'" />';
    echo '</div></form></center><br />';

/// Print footer
    echo $OUTPUT->footer();

?><?php

function authorize_process_csv($filename)
{
    global $CFG, $SITE, $DB;

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
        print_error('cannotopencsv');
    }
    $firstline = fgetcsv($handle, 8192, ",");
    $numfields = count($firstline);
    if ($numfields != 49 && $numfields != 70) {
        @fclose($handle);
        print_error('csvinvalidcolsnum');
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
        print_error('csvinvalidcols');
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
            if (($order = $DB->get_record('enrol_authorize', array('transid'=>$transid)))) {
                $order->status = ($transstatus == 'Approved Review') ? AN_STATUS_APPROVEDREVIEW : AN_STATUS_REVIEWFAILED;
                $DB->update_record('enrol_authorize', $order);
                $updated++; // Updated order status
            }
            continue;
        }

        if (!empty($reftransid) && is_numeric($reftransid) && 'Settled Successfully' == $transstatus && 'Credit' == $transtype) {
            if (($order = $DB->get_record('enrol_authorize', array('transid'=>$reftransid)))) {
                if (AN_METHOD_ECHECK == $order->paymentmethod) {
                    $refund = $DB->get_record('enrol_authorize_refunds', array('transid'=>$transid));
                    if ($refund) {
                        $refund->status = AN_STATUS_CREDIT;
                        $refund->settletime = $settlementdate;
                        $DB->update_record('enrol_authorize_refunds', $refund);
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
        $order = $DB->get_record('enrol_authorize', array('transid'=>$transid));
        if (!$order) {
            $ignored++;
            $ignoredlines .= $transid . ": Not our business\n";
            continue;
        }

        // Authorized/Captured and Settled
        $order->status = AN_STATUS_AUTHCAPTURE;
        $order->settletime = $settlementdate;
        $DB->update_record('enrol_authorize', $order);
        $updated++; // Updated order status and settlement date

        if ($order->paymentmethod != AN_METHOD_ECHECK) {
            $ignored++;
            $ignoredlines .= $transid . ": The method must be echeck\n";
            continue;
        }

        // Get course and context
        $course = $DB->get_record('course', array('id'=>$order->courseid));
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
        $user = $DB->get_record('user', array('id'=>$order->userid));
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

        $eventdata = new object();
        $eventdata->modulename        = 'moodle';
        $eventdata->userfrom          = $admin;
        $eventdata->userto            = $admin;
        $eventdata->subject           = "$SITE->fullname: Authorize.net CSV ERROR LOG";
        $eventdata->fullmessage       = $ignoredlines;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';			
        events_trigger('message_send', $eventdata);
    }

/// Send welcome messages to users
    if (!empty($sendem)) {
        send_welcome_messages($sendem);
    }

/// Show result
    notice("<b>Done...</b><br />Imported: $imported<br />Updated: $updated<br />Ignored: $ignored");
}


