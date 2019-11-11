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
 * Test output mail configuration page
 *
 * @copyright 2019 Victor Deniz <victor@moodle.com>, based on Michael Milette <michael.milette@tngconsulting.ca> code
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir.'/adminlib.php');

// This is an admin page.
admin_externalpage_setup('testoutgoingmailconf');

$headingtitle = get_string('testoutgoingmailconf', 'admin');
$homeurl = new moodle_url('/admin/category.php', array('category' => 'email'));
$returnurl = new moodle_url('/admin/testoutgoingconf.php');

$form = new core_admin\form\testoutgoingmailconf_form(null, ['returnurl' => $returnurl]);
if ($form->is_cancelled()) {
    redirect($homeurl);
}

// Display the page.
echo $OUTPUT->header();
echo $OUTPUT->heading($headingtitle);

$data = $form->get_data();
if ($data) {
    $emailuser = new stdClass();
    $emailuser->email = $data->recipient;
    $emailuser->id = -99;

    $subject = get_string('testoutgoingmailconf_subject', 'admin', $SITE->fullname);
    $messagetext = get_string('testoutgoingmailconf_message', 'admin');

    // Manage Moodle debugging options.
    $debuglevel = $CFG->debug;
    $debugdisplay = $CFG->debugdisplay;
    $debugsmtp = $CFG->debugsmtp ?? null; // This might not be set as it's optional.
    $CFG->debugdisplay = true;
    $CFG->debugsmtp = true;
    $CFG->debug = 15;

    // Send test email.
    ob_start();
    $success = email_to_user($emailuser, $USER, $subject, $messagetext);
    $smtplog = ob_get_contents();
    ob_end_clean();

    // Restore Moodle debugging options.
    $CFG->debug = $debuglevel;
    $CFG->debugdisplay = $debugdisplay;

    // Restore the debugsmtp config, if it was set originally.
    unset($CFG->debugsmtp);
    if (!is_null($debugsmtp)) {
        $CFG->debugsmtp = $debugsmtp;
    }

    if ($success) {
        $msgparams = new stdClass();
        $msgparams->fromemail = $USER->email;
        $msgparams->toemail = $emailuser->email;
        $msg = get_string('testoutgoingmailconf_sentmail', 'admin', $msgparams);
        $notificationtype = 'notifysuccess';
    } else {
        $notificationtype = 'notifyproblem';
        // No communication between Moodle and the SMTP server - no error output.
        if (trim($smtplog) == false) {
            $msg = get_string('testoutgoingmailconf_errorcommunications', 'admin');
        } else {
            $msg = $smtplog;
        }
    }

    // Show result.
    echo $OUTPUT->notification($msg, $notificationtype);
}

$form->display();
echo $OUTPUT->footer();
