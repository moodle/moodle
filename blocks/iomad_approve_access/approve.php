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
 * @package    Block Approve Enroll
 * @copyright  2011 onwards E-Learn Design Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir.'/formslib.php');
require_once('approve_form.php');
require_once($CFG->dirroot."/local/email/lib.php");

// Set up PAGE stuff.
require_login();

// Can I do this?
require_capability('block/iomad_approve_access:approve', context_system::instance());

$context = context_system::instance();
$PAGE->set_context($context);
$baseurl = new moodle_url('/blocks/iomad_approve_access/approve.php');
$PAGE->set_url($baseurl);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->set_pagelayout('standard');

// Set up some strings.
$strmanage = get_string('approveusers', 'block_iomad_approve_access');

$PAGE->set_title($strmanage);
$PAGE->set_heading($strmanage);

if (is_siteadmin($USER->id)) {
    $approvaltype = 'both';
} else {
    // What type of manager am I?
    if ($manager = $DB->get_record('companymanager', array('userid' => $USER->id))) {
        if (!empty($manager->departmentmanager)) {
            $approvaltype = 'manager';
        } else {
            $approvaltype = 'company';
        }
    } else {
        $approvaltype = 'none';
    }
}

// Display the page.
echo $OUTPUT->header();

if ($approvaltype == 'none') {
    echo get_string('noauthority', 'block_iomad_approve_access');
    $OUTPUT->footer();
    die;
}
// Set up the form.
$callform = new approve_form();
if ($data = $callform->get_data()) {

    foreach($data as $key=>$dataresult) {

        // Check if we have an approval passed to us.
        if (strpos($key, 'approve_') !== false) {
            $capturedresult = explode("_", $key);

            if ($result = $DB->get_record('block_iomad_approve_access', array('userid'=>$capturedresult[1],
                                                                              'activityid'=>$capturedresult[2]))) {
                $event = $DB->get_record('courseclassroom', array('id'=>$result->activityid));
                $senddenied = false;
                        
                if ($approvaltype == 'both' || $approvaltype == 'manager' ) {
                    if ($dataresult == 1) {
                        $result->manager_ok = 1;
                        add_to_log($event->id,
                                   'trainingevent',
                                   'User '.$result->userid.' department manager approved',
                                   'blocks/iomad_approve_access/approve.php',
                                   $event->id,
                                   $USER->id);
                        if ($event->approvaltype == 3) {
                            // Get the company managers for this user.
                            $usercompany = company::get_company_byuserid($result->userid);
                            $company = new company($usercompany->id);

                            // Add other details too.
                            $course = $DB->get_record('course', array('id' => $event->course));
                            $mymanagers = $company->get_my_managers($result->userid, 1);
                            $eventuser = $DB->get_record('user', array('id' => $result->userid));
                            $location = $DB->get_record('classroom',array('id'=>$event->classroomid));
                            $location->time = date('jS \of F Y \a\t h:i', $event->startdatetime);

                            // Send the emails.
                            foreach ($mymanagers as $mymanager) {
                                if ($manageruser = $DB->get_record('user', array('id'=>$mymanager->userid))) {
                                    EmailTemplate::send('course_classroom_approval', array('course'=>$course,
                                                                                           'user'=>$manageruser,
                                                                                           'approveuser'=>$eventuser,
                                                                                           'classroom'=>$location));
                                }
                            }
                        }
                    } else {
                        $result->manager_ok = 3;
                        $result->tm_ok = 3;
                        $senddenied = true;
                        add_to_log($event->id,
                                   'trainingevent',
                                   'User '.$result->userid.' department manager denied',
                                   'blocks/iomad_approve_access/approve.php',
                                   $event->id,
                                   $USER->id);
                    }
                }
                if ($approvaltype == 'both' || $approvaltype == 'company') {
                    if ($dataresult == 1) {
                        $result->tm_ok = 1;
                        $result->manager_ok = 1;
                        add_to_log($event->id,
                                   'trainingevent',
                                   'User '.$result->userid.' company manager approved',
                                   'blocks/iomad_approve_access/approve.php',
                                   $event->id,
                                   $USER->id);
                    } else {
                        $result->manager_ok = 3;
                        $result->tm_ok = 3;
                        $senddenied = true;
                        add_to_log($event->id,
                        'trainingevent',
                        'User '.$result->userid.' company manager ',
                        'blocks/iomad_approve_access/approve.php',
                        $event->id,
                        $USER->id);
                    }
                }
                // Do we need to email them?
                if ($event->approvaltype == 1 && $result->manager_ok == 1) {
                    $sendemail = true;
                } else if ($event->approvaltype == 2 && $result->tm_ok == 1) {
                    $sendemail = true;
                } else if ($event->approvaltype == 3 && $result->manager_ok == 1 && $result->tm_ok == 1) {
                    $sendemail = true;
                } else {
                    $sendemail = false;
                } 
                $DB->update_record('block_iomad_approve_access', $result, $bulk=false);
                if ($sendemail || $senddenied) {
                    $location = $DB->get_record('classroom',array('id'=>$event->classroomid));
                    $location->time = date('jS \of F Y \a\t h:i', $event->startdatetime);
                    $approveuser = $DB->get_record('user', array('id'=>$result->userid));
                    $approvecourse = $DB->get_record('course', array('id'=>$result->courseid));
                    if (!$senddenied) {
                        EmailTemplate::send('course_classroom_approved', array('course'=>$approvecourse,
                                                                               'user'=>$approveuser,
                                                                               'classroom'=>$location));
                        //  Update the attendance at the event.
                        approve_access_register_user($approveuser, $event);
                        add_to_log($event->id,
                                   'trainingevent',
                                   'User '.$result->userid.' added to training event',
                                   'blocks/iomad_approve_access/approve.php',
                                   $event->id,
                                   $USER->id);
                    } else {
                        EmailTemplate::send('course_classroom_denied', array('course'=>$approvecourse,
                                                                             'user'=>$approveuser,
                                                                             'classroom'=>$location));
                        add_to_log($event->id,
                                   'trainingevent',
                                   'User '.$result->userid.' approval denied for training event',
                                   'blocks/iomad_approve_access/approve.php',
                                   $event->id,
                                   $USER->id);
                    }
                }
            } else {
                echo "Update failed";
            }
        }
    }
    // Send them on their way as the form will have changed.
    redirect(new moodle_url('approve.php'));
}

// Display the form.
$callform->display();

echo $OUTPUT->footer();
