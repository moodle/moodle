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
 * Authorize.Net enrolment plugin - support for user self unenrolment.
 *
 * @package    enrol_authorize
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// Load libraries
require_once('../../config.php');
require_once($CFG->dirroot.'/enrol/authorize/const.php');
require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');
require_once($CFG->libdir.'/eventslib.php');
require_once('import_form.php');

/// Require capabilities
require_login();
require_capability('enrol/authorize:uploadcsv', context_system::instance());

/// Print header
$struploadcsv = get_string('uploadcsv', 'enrol_authorize');
$managebutton = "<form method='get' action='index.php'><input type='submit' value='".get_string('paymentmanagement', 'enrol_authorize')."' /></form>";

$form = new enrol_authorize_import_form();

$PAGE->set_url('/enrol/authorize/uploadcsv.php');
$PAGE->navbar->add(get_string('paymentmanagement', 'enrol_authorize'), 'index.php');
$PAGE->navbar->add($struploadcsv, 'uploadcsv.php');
$PAGE->set_title($struploadcsv);
$PAGE->set_cacheable(false);
$PAGE->set_button($managebutton);
echo $OUTPUT->header();
echo $OUTPUT->heading($struploadcsv);

/// Handle CSV file
if (!$form->get_data()) {
    $form->display();
} else {
    $filename = $CFG->tempdir . '/enrolauthorize/importedfile_'.time().'.csv';
    make_temp_directory('enrolauthorize');
    // Fix mac/dos newlines
    $text = $form->get_file_content('csvfile');
    $text = preg_replace('!\r\n?!', "\n", $text);
    $fp = fopen($filename, "w");
    fwrite($fp, $text);
    fclose($fp);
    authorize_process_csv($filename);
}

/// Print footer
echo $OUTPUT->footer();

function authorize_process_csv($filename) {
    global $CFG, $SITE, $DB;

    $plugin = enrol_get_plugin('authorize');

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
        $coursecontext = context_course::instance($course->id, IGNORE_MISSING);
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
                // Enrol user
                $pinstance = $DB->get_record('enrol', array('id'=>$order->instanceid));
                $plugin->enrol_user($pinstance, $user->id, $pinstance->roleid, $timestart, $timeend);

                $imported++;
                if ($plugin->get_config('enrol_mailstudents')) {
                    $sendem[] = $order->id;
                }
            }
        }
    }
    fclose($handle);

    /// Send email to admin
    if (!empty($ignoredlines)) {
        $admin = get_admin();

        $eventdata = new stdClass();
        $eventdata->modulename        = 'moodle';
        $eventdata->component         = 'enrol_authorize';
        $eventdata->name              = 'authorize_enrolment';
        $eventdata->userfrom          = $admin;
        $eventdata->userto            = $admin;
        $eventdata->subject           = format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID))).': Authorize.net CSV ERROR LOG';
        $eventdata->fullmessage       = $ignoredlines;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }

    /// Send welcome messages to users
    if (!empty($sendem)) {
        send_welcome_messages($sendem);
    }

    /// Show result
    notice("<b>Done...</b><br />Imported: $imported<br />Updated: $updated<br />Ignored: $ignored");
}
