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
 * @package    Block Iomad Approve Access
 * @copyright  2021 Derick Turner
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot."/local/email/lib.php");
require_once($CFG->dirroot."/blocks/iomad_approve_access/lib.php");

// Set up PAGE stuff.
require_login();

// Can I do this?
iomad::require_capability('block/iomad_approve_access:approve', context_system::instance());

$context = context_system::instance();
$PAGE->set_context($context);
$baseurl = new moodle_url('/blocks/iomad_approve_access/approve.php');
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('base');

// Set up some strings.
$strmanage = get_string('approveusers', 'block_iomad_approve_access');

$PAGE->set_title($strmanage);
$PAGE->set_heading($strmanage);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

if (is_siteadmin($USER->id)) {
    $approvaltype = 'both';
} else {
    // What type of manager am I?
    if ($companyuser = $DB->get_record('company_users', array('userid' => $USER->id, 'companyid' => $companyid))) {
        if ($companyuser->managertype == 2) {
            $approvaltype = 'manager';
        } else if ($companyuser->managertype == 1) {
            $approvaltype = 'company';
        } else {
            $approvaltype = 'none';
        }
    }
}

if ($approvaltype == 'none') {
    // Display the page.
    echo $OUTPUT->header();
    echo get_string('noauthority', 'block_iomad_approve_access');
    $OUTPUT->footer();
    die;
}
// Set up the form.
$callform = new \block_iomad_approve_access\forms\approve_form();
if ($data = $callform->get_data()) {
    foreach ($data as $key => $dataresult) {

        // Check if we have an approval passed to us.
        if (strpos($key, 'approve_') !== false) {
            $capturedresult = explode("_", $key);

            if ($result = $DB->get_record('block_iomad_approve_access', array('userid' => $capturedresult[1],
                                                                              'activityid' => $capturedresult[2]))) {
                $event = $DB->get_record('trainingevent', array('id' => $result->activityid));
                $senddenied = false;

                // Get the room info.
                $roominfo = $DB->get_record('classroom', array('id' => $event->classroomid));

                // Get the number of current attendees.
                $numattendees = $DB->count_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 0));

                // Is the event full?
                if ($numattendees >= $roominfo->capacity && $dataresult == 1) {
                    continue;
                }

                // Get the CMID.
                $cmidinfo = $DB->get_record_sql("SELECT * FROM {course_modules}
                                                 WHERE instance = :eventid
                                                 AND module = ( SELECT id FROM {modules}
                                                   WHERE name = 'trainingevent')", array('eventid' => $event->id));

                $userinfo = $DB->get_record('user', array('id' => $result->userid), 'firstname, lastname');

                if ($approvaltype == 'both' || $approvaltype == 'manager' ) {
                    if ($dataresult == 1) {
                        $result->manager_ok = 1;
                        $result->tm_ok = 0;

                        // Fire an event for this.
                        $moodleevent = \block_iomad_approve_access\event\manager_approved::create(array('context' => context_module::instance($cmidinfo->id),
                                                                                                        'userid' => $USER->id,
                                                                                                        'relateduserid' => $result->userid,
                                                                                                        'objectid' => $event->id,
                                                                                                        'courseid' => $event->course));
                        $moodleevent->trigger();

                        if ($event->approvaltype == 3) {
                            // Get the company managers for this user.
                            $usercompany = company::get_company_byuserid($result->userid);
                            $company = new company($usercompany->id);

                            // Add other details too.
                            $course = $DB->get_record('course', array('id' => $event->course));
                            $mymanagers = $company->get_my_managers($result->userid, 1);
                            $eventuser = $DB->get_record('user', array('id' => $result->userid));
                            $location = $DB->get_record('classroom', array('id' => $event->classroomid));
                            $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);

                            // Send the emails.
                            foreach ($mymanagers as $mymanager) {
                                if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                    EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                           'event' => $event,
                                                                                           'user' => $manageruser,
                                                                                           'approveuser' => $eventuser,
                                                                                           'company' => $company,
                                                                                           'classroom' => $location));
                                }
                            }
                        }
                    } else {
                        $result->manager_ok = 3;
                        $result->tm_ok = 3;
                        $senddenied = true;

                        // Fire an event for this.
                        $moodleevent = \block_iomad_approve_access\event\manager_denied::create(array('context' => context_module::instance($cmidinfo->id),
                                                                                                      'userid' => $USER->id,
                                                                                                      'relateduserid' => $result->userid,
                                                                                                      'objectid' => $event->id,
                                                                                                      'courseid' => $event->course));
                        $moodleevent->trigger();
                    }
                }
                if ($approvaltype == 'both' || $approvaltype == 'company') {
                    if ($dataresult == 1) {
                        $result->tm_ok = 1;
                        $result->manager_ok = 1;

                        // Fire an event for this.
                        $moodleevent = \block_iomad_approve_access\event\manager_approved::create(array('context' => context_module::instance($cmidinfo->id),
                                                                                                        'userid' => $USER->id,
                                                                                                        'relateduserid' => $result->userid,
                                                                                                        'objectid' => $event->id,
                                                                                                        'courseid' => $event->course));
                        $moodleevent->trigger();
                    } else {
                        $result->tm_ok = 3;
                        // If its an event which requires both approvals then pass it back to the department manager to argue.
                        if ($event->approvaltype == 3) {
                            if ($result->manager_ok != 3) {
                                $result->manager_ok = 0;
                            }
                        } else {
                            // Otherwise access is denied.
                            $result->manager_ok = 3;
                        }
                        if ($result->manager_ok == 3) {
                            $senddenied = true;
                        } else {
                            // Get the company managers for this user.
                            $usercompany = company::get_company_byuserid($result->userid);
                            $company = new company($usercompany->id);

                            // Add other details too.
                            $course = $DB->get_record('course', array('id' => $event->course));
                            $mymanagers = $company->get_my_managers($result->userid, 2);
                            if ($DB->get_record('company_users', array('userid' => $result->userid, 'managertype' => 2))) {
                                // This is a department manager.  Does he have a higher department manager?
                                $nodeptmanagers = true;
                                foreach ($mymanagers as $mymanager) {
                                    if ($DB->get_record('company_users', array('userid' => $mymanager->userid,
                                                                               'managertype' => 2))) {
                                        $nodeptmanagers = false;
                                        break;
                                    }
                                }
                                if ($nodeptmanagers) {
                                    $mymanagers = array();
                                }
                            }
                            if (!empty($mymanagers)) {
                                $eventuser = $DB->get_record('user', array('id' => $result->userid));
                                $location = $DB->get_record('classroom', array('id' => $event->classroomid));
                                $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);

                                // Send the emails.
                                foreach ($mymanagers as $mymanager) {
                                    if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                        EmailTemplate::send('course_classroom_manager_denied', array('course' => $course,
                                                                                               'event' => $event,
                                                                                               'user' => $USER,
                                                                                               'approveuser' => $eventuser,
                                                                                               'company' => $company,
                                                                                               'classroom' => $location));
                                    }
                                }
                            } else {
                                $result->manager_ok = 3;
                                $senddenied = true;
                            }
                        }

                        // Fire an event for this.
                        $moodleevent = \block_iomad_approve_access\event\manager_denied::create(array('context' => context_module::instance($cmidinfo->id),
                                                                                                      'userid' => $USER->id,
                                                                                                      'relateduserid' => $result->userid,
                                                                                                      'objectid' => $event->id,
                                                                                                      'courseid' => $event->course));
                        $moodleevent->trigger();
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
                $DB->update_record('block_iomad_approve_access', $result, $bulk = false);
                if ($sendemail || $senddenied) {
                    $location = $DB->get_record('classroom', array('id' => $event->classroomid));
                    $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                    $approveuser = $DB->get_record('user', array('id' => $result->userid));
                    $approvecourse = $DB->get_record('course', array('id' => $result->courseid));
                    if ($sendemail) {
                        $cancontinue = true;
                        if (!empty($event->coursecapacity)) {
                            $maxcapacity = $event->coursecapacity;
                        } else {
                            $maxcapacity = $location->capacity;
                        }
                        // Get the current count.
                        $attending = $DB->count_records('trainingevent_users', ['trainingeventid' => $event->id, 'waitlisted' => 0]);
                        if ($location->isvirtual || $attending < $maxcapacity) {
                           $waitlisted = 0;
                        
                        } else if ($event->haswaitlist) {
                            $waitlisted = 1;
                        } else {
                            $cancontinue = false;
                        }
                        if ($cancontinue) {
                            EmailTemplate::send('course_classroom_approved', array('course' => $approvecourse,
                                                                                   'event' => $event,
                                                                                   'user' => $approveuser,
                                                                                   'company' => $company,
                                                                                   'classroom' => $location));
                            //  Update the attendance at the event.
                            iomad_approve_access::register_user($approveuser, $event, $waitlisted);
    
                            // Fire an event for this.
                            $moodleevent = \block_iomad_approve_access\event\request_granted::create(array('context' => context_module::instance($cmidinfo->id),
                                                                                                           'userid' => $USER->id,
                                                                                                           'relateduserid' => $result->userid,
                                                                                                           'objectid' => $event->id,
                                                                                                           'courseid' => $event->course));
                            $moodleevent->trigger();
    
                            // Do we need to notify teachers?
                            if (!empty($event->emailteachers)) {
                                // Are we using groups?
                                $usergroups = groups_get_user_groups($course->id, $approveuser->id);
                                $userteachers = [];
                                foreach ($usergroups as $usergroup => $junk) {
                                    $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                                } 
                                foreach ($userteachers as $userteacher) {
                                    EmailTemplate::send('user_signed_up_for_event_teacher', array('course' => $course,
                                                                                                  'approveuser' => $approveuser,
                                                                                                  'user' => $userteacher,
                                                                                                  'classroom' => $location,
                                                                                                  'company' => $company,
                                                                                                  'event' => $event));
                                }
                            }
                        }
                    } else if ($senddenied) {
                        EmailTemplate::send('course_classroom_denied', array('course' => $approvecourse,
                                                                             'event' => $event,
                                                                             'user' => $approveuser,
                                                                             'company' => $company,
                                                                             'classroom' => $location));

                        // Fire an event for this.
                        $moodleevent = \block_iomad_approve_access\event\request_denied::create(array('context' => context_module::instance($cmidinfo->id),
                                                                                                      'userid' => $USER->id,
                                                                                                      'relateduserid' => $result->userid,
                                                                                                      'objectid' => $event->id,
                                                                                                      'courseid' => $event->course));
                        $moodleevent->trigger();
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

// Display the page.
echo $OUTPUT->header();

// Display the form.
$callform->display();

echo $OUTPUT->footer();
