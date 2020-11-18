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
 * trainingevent module
 *
 * @package    mod
 * @subpackage trainingevent
 * @copyright  2003 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot."/local/email/lib.php");
require_once($CFG->libdir."/gradelib.php");
require_once('lib.php');

$id = required_param('id', PARAM_INT);    // Course Module ID, or.
$attending = optional_param('attending', null, PARAM_ALPHA);
$view = optional_param('view', 0, PARAM_INTEGER);
$waitingoption = optional_param('waiting', 0, PARAM_INTEGER);
$publish = optional_param('publish', 0, PARAM_INTEGER);
$dodownload = optional_param('dodownload', 0, PARAM_INTEGER);
$userid = optional_param('userid', 0, PARAM_INTEGER);
$usergrade = optional_param('usergrade', 0, PARAM_INTEGER);
$current = optional_param('current', 0, PARAM_INTEGER);
$chosen = optional_param('chosenevent', 0, PARAM_INTEGER);
$action = optional_param('action', null, PARAM_ALPHA);
$booking = optional_param('booking', null, PARAM_ALPHA);

if (! $cm = get_coursemodule_from_id('trainingevent', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error('coursemisconf');
}

require_course_login($course, false, $cm);

// Get the database entry.
if (!$event = $DB->get_record('trainingevent', array('id' => $cm->instance))) {
    print_error('noinstance');
} else {
    if (!$location = $DB->get_record('classroom', array('id' => $event->classroomid))) {
        if (!empty($dodownload)) {
            die;
        }
        print_error('location not defined');
    } else {

        // Page stuff.
        $url = new moodle_url('/course/view.php', array('id' => $event->course));
        $context = context_course::instance($event->course);
        require_login($event->course); // Adds to $PAGE, creates $OUTPUT.
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title($event->name);
        $PAGE->set_heading($SITE->fullname);

        // Get the associated department id.
        $company = new company($location->companyid);
        $parentlevel = company::get_company_parentnode($company->id);
        $companydepartment = $parentlevel->id;
        if (!empty($event->coursecapacity)) {
            $maxcapacity = $event->coursecapacity;
        } else {
            $maxcapacity = $location->capacity;
        }

        if (has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }
        $departmentid = $userhierarchylevel;

        // Get the CMID.
        $cmidinfo = $DB->get_record_sql("SELECT * FROM {course_modules}
                                         WHERE instance = :eventid
                                         AND module = ( SELECT id FROM {modules}
                                           WHERE name = 'trainingevent')", array('eventid' => $event->id));

        // What is the users approval level, if any?
        if ($manageruser = $DB->get_record('company_users', array('userid' => $USER->id))) {
            if ($manageruser->managertype == 2) {
                $myapprovallevel = "department";
            } else if ($manageruser->managertype == 1) {
                $myapprovallevel = "company";
            } else {
                $myapprovallevel = "none";
            }
        } else if (has_capability('block/iomad_company_admin:company_add', context_system::instance())) {
            $myapprovallevel = "company";
        } else {
            $myapprovallevel = "none";
        }

        if (!empty($attending)) {
            if ('yes' == $attending) {
                $record = $DB->get_record('trainingevent_users', array('trainingeventid' => $event->id, 'userid' => $USER->id));

                if ($waitingoption) {
                    if (!($record && $record->waitlisted)) {
                        if ($record) {
                            $DB->update_record('trainingevent_users', array('id'=>$record->id, 'trainingeventid' => $event->id, 'userid' => $USER->id, 'waitlisted'=>1));
                        } else {
                            $DB->insert_record('trainingevent_users', array('trainingeventid' => $event->id, 'userid' => $USER->id, 'waitlisted'=>1));
                        }
                    }

                } else if (!($record && !$record->waitlisted)) {
                    if ($record && $record->waitlisted) {
                        $res = $DB->update_record('trainingevent_users', array('id'=>$record->id, 'trainingeventid' => $event->id, 'userid' => $USER->id, 'waitlisted'=>0));
                    } else {
                        $res = $DB->insert_record('trainingevent_users', array('trainingeventid' => $event->id, 'userid' => $USER->id));
                    }
                    if (empty($res)) {
                        print_error('error creating attendance record');
                    } else {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                        EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                                  'user' => $USER,
                                                                                  'classroom' => $location,
                                                                                  'event' => $event));
                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\user_attending::create(array('context' => context_module::instance($id),
                                                                                             'userid' => $USER->id,
                                                                                             'objectid' => $event->id,
                                                                                             'courseid' => $event->course));
                        $moodleevent->trigger();
                    }
                }
            } else if ('no' == $attending) {
                if ($attendingrecord = $DB->get_record('trainingevent_users', array('trainingeventid' => $event->id,
                                                                                    'userid' => $USER->id))) {
                    if (!$DB->delete_records('trainingevent_users', array('id' => $attendingrecord->id))) {
                        print_error('error removing attendance record');
                    } else {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                        EmailTemplate::send('user_removed_from_event', array('course' => $course,
                                                                                 'user' => $USER,
                                                                                 'classroom' => $location,
                                                                                 'event' => $event));
                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\user_removed::create(array('context' => context_module::instance($id),
                                                                                           'userid' => $USER->id,
                                                                                           'relateduserid' => $USER->id,
                                                                                           'objectid' => $event->id,
                                                                                           'courseid' => $event->course));
                        $moodleevent->trigger();
                    }
                }
            }
        }
        if (!empty($booking)) {
            if ('yes' == $booking  || 'again' == $booking) {
                if (!$userbooking = $DB->get_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                        'userid' => $USER->id))) {
                    if (!$DB->insert_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                'userid' => $USER->id,
                                                                                'courseid' => $event->course,
                                                                                'tm_ok' => 0,
                                                                                'manager_ok' => 0,
                                                                                'companyid' => $company->id))) {
                        print_error('error creating attendance record');
                    } else {
                        // theoretically should be a transaction with requesting approval but it's pretty easy to fix this glitch if it happens
                        $db->delete_records('trainingevent_users', array('trainingeventid' => $event->id, 'userid' => $USER->id, 'waitlist' => 1));

                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                        // Get the list of managers we need to send an email to.
                        if ($event->approvaltype != 2 ) {
                            $mymanagers = $company->get_my_managers($USER->id, 2);
                        } else {
                            $mymanagers = $company->get_my_managers($USER->id, 1);
                        }
                        if (empty($mymanagers)) {
                            $mymanagers = $company->get_my_managers($USER->id, 1);
                        }
                        foreach ($mymanagers as $mymanager) {
                            if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                       'user' => $manageruser,
                                                                                       'approveuser' => $USER,
                                                                                       'event' => $event,
                                                                                       'classroom' => $location));
                            }
                        }
                        EmailTemplate::send('course_classroom_approval_request', array('course' => $course,
                                                                               'user' => $USER,
                                                                               'event' => $event,
                                                                               'classroom' => $location));

                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\attendance_requested::create(array('context' => context_module::instance($id),
                                                                                                   'userid' => $USER->id,
                                                                                                   'objectid' => $event->id,
                                                                                                   'courseid' => $event->course));
                        $moodleevent->trigger();
                    }
                } else {
                    $userbooking->tm_ok = 0;
                    $userbooking->manager_ok = 0;
                    $DB->update_record('block_iomad_approve_access', $userbooking);
                    $course = $DB->get_record('course', array('id' => $event->course));
                    $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                    // Get the list of managers we need to send an email to.
                    if ($event->approvaltype != 2 ) {
                        $mymanagers = $company->get_my_managers($USER->id, 2);
                    } else {
                        $mymanagers = $company->get_my_managers($USER->id, 1);
                    }
                    foreach ($mymanagers as $mymanager) {
                        if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                            EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                   'user' => $manageruser,
                                                                                   'approveuser' => $USER,
                                                                                   'classroom' => $location,
                                                                                   'event' => $event));
                        }
                    }
                    EmailTemplate::send('course_classroom_approval_request', array('course' => $course,
                                                                           'user' => $USER,
                                                                           'classroom' => $location,
                                                                           'event' => $event));

                    // Fire an event for this.
                    $moodleevent = \mod_trainingevent\event\attendance_requested::create(array('context' => context_module::instance($id),
                                                                                               'userid' => $USER->id,
                                                                                               'objectid' => $event->id,
                                                                                               'courseid' => $event->course));
                    $moodleevent->trigger();
                }
            } else if ( 'no' == $booking) {
                if ($dereq = (array) $DB->get_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                         'userid' => $USER->id))) {
                    $DB->delete_records('block_iomad_approve_access', $dereq);

                    // Fire an event for this.
                    $moodleevent = \mod_trainingevent\event\attendance_withdrawn::create(array('context' => context_module::instance($id),
                                                                                               'userid' => $USER->id,
                                                                                               'objectid' => $event->id,
                                                                                               'courseid' => $event->course));
                    $moodleevent->trigger();
                }
            }
        }
        if (!empty($chosen) && $chosen != $event->id) {
            // We are moving a user to another event  check there is space.
            if (!$chosenevent = $DB->get_record('trainingevent', array('id' => $chosen))) {
                print_error('chosen event is invalid');
            } else {
                // Get the CMID.
                $chosencmidinfo = $DB->get_record_sql("SELECT * FROM {course_modules}
                                                 WHERE instance = :eventid
                                                 AND module = ( SELECT id FROM {modules}
                                                  WHERE name = 'trainingevent')", array('eventid' => $event->id));

                $chosenlocation = $DB->get_record('classroom', array('id' => $chosenevent->classroomid));
                $alreadyattending = $DB->count_records('trainingevent_users', array('trainingeventid' => $chosenevent->id, 'waitlisted' => 0));
                $user = $DB->get_record('user', array('id' => $userid));
                $course = $DB->get_record('course', array('id' => $event->course));
                if ($alreadyattending < $chosenlocation->capacity) {
                    // What kind of event is this?
                    if ($chosenevent->approvaltype == 0 || $chosenevent->approvaltype == 4 || $myapprovallevel == "company" ||
                        ($chosenevent->approvaltype == 1 && $myapprovallevel == "department")) {
                        // Add to the chosen event.
                        if (!$DB->get_record('trainingevent_users', array('userid' => $userid,
                                                                          'trainingeventid' => $chosenevent->id,
                                                                          'waitlisted' => 0))) {
                            $DB->insert_record('trainingevent_users', array('userid' => $userid,
                                                                            'trainingeventid' => $chosenevent->id,
                                                                            'waitlisted' => 0));
                            $messagestring = get_string('usermovedsuccessfully', 'trainingevent');
                            $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $chosenevent->startdatetime);
                            EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                                  'user' => $user,
                                                                                  'classroom' => $location,
                                                                                  'event' => $chosenevent));
                        }
                        // Remove from the current event.
                        $DB->delete_records('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id));
                        if ($event->approvaltype != 0) {
                            $DB->delete_records('block_iomad_approve_access', array('userid' => $userid,
                                                                                    'activityid' => $event->id));
                        }
                        $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                        EmailTemplate::send('user_removed_from_event', array('course' => $course,
                                                                             'user' => $user,
                                                                             'classroom' => $location,
                                                                             'event' => $event));
                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\attendance_changed::create(array('context' => context_module::instance($id),
                                                                                                 'userid' => $USER->id,
                                                                                                 'relateduserid' => $user->id,
                                                                                                 'objectid' => $event->id,
                                                                                                 'courseid' => $event->course));
                        $moodleevent->trigger();
                    } else if (($chosenevent->approvaltype == 3 || $chosenevent->approvaltype == 2)
                               && $myapprovallevel == "department") {
                        // More levels of approval are required.
                        if (!$userbooking = $DB->get_record('block_iomad_approve_access', array('activityid' => $chosenevent->id,
                                                                                                'userid' => $user->id))) {
                            if (!$DB->insert_record('block_iomad_approve_access', array('activityid' => $chosenevent->id,
                                                                                        'userid' => $user->id,
                                                                                        'courseid' => $chosenevent->course,
                                                                                        'tm_ok' => 0,
                                                                                        'manager_ok' => 1,
                                                                                        'companyid' => $company->id))) {
                                print_error('error creating attendance record');
                            } else {
                                $course = $DB->get_record('course', array('id' => $event->course));
                                $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $chosenevent->startdatetime);
                                $user = $DB->get_record('user', array('id' => $userid));
                                // Get the list of managers we need to send an email to.
                                $mymanagers = $company->get_my_managers($user->id, 1);
                                foreach ($mymanagers as $mymanager) {
                                    if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                        EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                               'user' => $manageruser,
                                                                                               'approveuser' => $user,
                                                                                               'event' => $chosenevent,
                                                                                               'classroom' => $location));
                                    }
                                }
                                // Fire an event for this.
                                $moodleevent = \block_iomad_approve_access\event\manager_approved::create(array('context' => context_module::instance($id),
                                                                                                                'userid' => $USER->id,
                                                                                                                'relateduserid' => $user->id,
                                                                                                                'objectid' => $event->id,
                                                                                                                'courseid' => $event->course));
                                $moodleevent->trigger();
                            }
                        } else {
                            $userbooking->tm_ok = 0;
                            $userbooking->manager_ok = 1;
                            $DB->update_record('block_iomad_approve_access', $userbooking);
                            $course = $DB->get_record('course', array('id' => $event->course));
                            $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                            $user = $DB->get_record('user', array('id' => $userid));
                            // Get the list of managers we need to send an email to.
                            $mymanagers = $company->get_my_managers($USER->id, 1);
                            foreach ($mymanagers as $mymanager) {
                                if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                    EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                           'user' => $manageruser,
                                                                                           'approveuser' => $user,
                                                                                           'classroom' => $location,
                                                                                           'event' => $chosenevent));
                                }
                            }

                            // Fire an event for this.
                            $moodleevent = \block_iomad_approve_access\event\manager_approved::create(array('context' => context_module::instance($id),
                                                                                                            'userid' => $USER->id,
                                                                                                            'relateduserid' => $user->id,
                                                                                                            'objectid' => $event->id,
                                                                                                            'courseid' => $event->course));
                            $moodleevent->trigger();
                        }
                        // Remove from the current event.
                        $DB->delete_records('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id));
                        if ($event->approvaltype != 0) {
                            $DB->delete_records('block_iomad_approve_access', array('userid' => $userid,
                                                                                    'activityid' => $event->id));
                        }
                        $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                        EmailTemplate::send('user_removed_from_event', array('course' => $course,
                                                                             'user' => $user,
                                                                             'classroom' => $location,
                                                                             'event' => $event));

                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\attendance_changed::create(array('context' => context_module::instance($id),
                                                                                                 'userid' => $USER->id,
                                                                                                 'relateduserid' => $user->id,
                                                                                                 'objectid' => $event->id,
                                                                                                 'courseid' => $event->course));
                        $moodleevent->trigger();
                    }
                }
            }
        }
        if ($action == 'delete' && !empty($userid)) {
            // Remove the userid from the event.
            if ($DB->delete_records('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id))) {
                $messagestring = get_string('userremovedsuccessfully', 'trainingevent');
                $user = $DB->get_record('user', array('id' => $userid));
                $course = $DB->get_record('course', array('id' => $event->course));
                $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                EmailTemplate::send('user_removed_from_event', array('course' => $course,
                                                                     'user' => $user,
                                                                     'classroom' => $location,
                                                                     'event' => $event));
                // Fire an event for this.
                $moodleevent = \mod_trainingevent\event\user_removed::create(array('context' => context_module::instance($id),
                                                                                   'userid' => $USER->id,
                                                                                   'relateduserid' => $user->id,
                                                                                   'objectid' => $event->id,
                                                                                   'courseid' => $event->course));
                $moodleevent->trigger();
            }
        }
        if ($action == 'add' && !empty($userid)) {
            $chosenlocation = $DB->get_record('classroom', array('id' => $event->classroomid));
            $alreadyattending = $DB->count_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 0));
            $user = $DB->get_record('user', array('id' => $userid));
            $course = $DB->get_record('course', array('id' => $event->course));

            $record = $DB->get_record('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id));

            $waitlist = $alreadyattending >= $maxcapacity;
            if ($alreadyattending < $maxcapacity) {
                // What kind of event is this?
                if ($event->approvaltype == 0 || $event->approvaltype == 4 || $myapprovallevel == "company" ||
                    ($event->approvaltype == 1 && $myapprovallevel == "department")) {
                    // Add to the chosen event.
                    if (!($record && $record->waitlisted == 0)) {
                        if ($record->waitlisted) {
                            $DB->set_field('trainingevent_users', 'waitlisted', 0, array('id' => $record->id));
                        }
                        else {
                            $DB->insert_record('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id, 'waitlisted' => 0));
                        }

                        $messagestring = get_string('useraddedsuccessfully', 'trainingevent');
                        $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                        EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                              'user' => $user,
                                                                              'classroom' => $location,
                                                                              'event' => $event));

                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\user_added::create(array('context' => context_module::instance($id),
                                                                                         'userid' => $USER->id,
                                                                                         'relateduserid' => $user->id,
                                                                                         'objectid' => $event->id,
                                                                                         'courseid' => $event->course));
                        $moodleevent->trigger();
                    }
                } else if (($event->approvaltype == 3 || $event->approvaltype == 2)&& $myapprovallevel == "department") {
                    // More levels of approval are required.
                    if (!$userbooking = $DB->get_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                            'userid' => $user->id))) {
                        if (!$DB->insert_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                    'userid' => $user->id,
                                                                                    'courseid' => $event->course,
                                                                                    'tm_ok' => 0,
                                                                                    'manager_ok' => 1,
                                                                                    'companyid' => $company->id))) {
                            print_error('error creating attendance record');
                        } else {
                            $course = $DB->get_record('course', array('id' => $event->course));
                            $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                            $user = $DB->get_record('user', array('id' => $userid));
                            // Get the list of managers we need to send an email to.
                            $mymanagers = $company->get_my_managers($user->id, 1);
                            foreach ($mymanagers as $mymanager) {
                                if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                    EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                           'user' => $manageruser,
                                                                                           'approveuser' => $user,
                                                                                           'event' => $event,
                                                                                           'classroom' => $location));
                                }
                            }
                            // Fire an event for this.
                            $moodleevent = \block_iomad_approve_access\event\manager_approved::create(array('context' => context_module::instance($id),
                                                                                                            'userid' => $USER->id,
                                                                                                            'relateduserid' => $user->id,
                                                                                                            'objectid' => $event->id,
                                                                                                            'courseid' => $event->course));
                            $moodleevent->trigger();
                        }
                    } else {
                        $userbooking->tm_ok = 0;
                        $userbooking->manager_ok = 1;
                        $DB->update_record('block_iomad_approve_access', $userbooking);
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
                        $user = $DB->get_record('user', array('id' => $userid));
                        // Get the list of managers we need to send an email to.
                        $mymanagers = $company->get_my_managers($USER->id, 1);
                        foreach ($mymanagers as $mymanager) {
                            if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                       'user' => $manageruser,
                                                                                       'approveuser' => $user,
                                                                                       'classroom' => $location,
                                                                                       'event' => $event));
                            }
                        }
                        // Fire an event for this.
                        $moodleevent = \block_iomad_approve_access\event\manager_approved::create(array('context' => context_module::instance($id),
                                                                                                        'userid' => $USER->id,
                                                                                                        'relateduserid' => $user->id,
                                                                                                        'objectid' => $event->id,
                                                                                                        'courseid' => $event->course));
                        $moodleevent->trigger();
                    }
                }
            }
        }
        if ($action == 'reset') {
            if (has_capability('mod/trainingevent:resetattendees', $context)) {
                $DB->delete_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 0));
            }
        }
        if ($action == 'grade' && !empty($userid)) {
            // Grade the user.
            $gradegrade->userid = $userid;
            $gradegrade->rawgrade = $usergrade;
            $gradegrade->finalgrade = $usergrade;
            $gradegrade->usermodified = $USER->id;
            $gradegrade->timemodified = time();
            $gradeparams['gradetype'] = GRADE_TYPE_VALUE;
            $gradeparams['grademax']  = 100;
            $gradeparams['grademin']  = 0;
            $gradeparams['reset'] = false;
            grade_update('mod/trainingevent', $event->course, 'mod', 'trainingevent', $event->id, 0, $gradegrade, $gradeparams);
        }

        if ($attendance = (array) $DB->get_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 0), null, 'userid')) {
            $attendancecount = count($attendance);
            if (array_key_exists($USER->id, $attendance)) {
                $attending = true;
            } else {
                $attending = false;
            }
        } else {
            $attendancecount = 0;
            $attending = false;
        }

        // Are we sending out emails?
        if (!empty($publish)) {

            echo $OUTPUT->header();

            // Check the userid is valid.
            if (!company::check_valid_user($company->id, $USER->id, $departmentid)) {
                print_error('invaliduserdepartment', 'block_iomad_company_management');
            }

            echo "<h2>".get_string('sendingemails', 'trainingevent')."</h2>";
            $course = $DB->get_record('course', array('id' => $event->course));
            $course->url = new moodle_url('course/view.php', array('id' => $course->id));
            $location->time = date($CFG->iomad_date_format . ' \a\t h:i', $event->startdatetime);
            if ($waitingoption) {
                $waiting = (array) $DB->get_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 1));
                $waitinglist = array_map(function($training_user) {return array('user' => $training_user->userid);}, $waiting);

                EmailTemplate::send('advertise_classroom_based_course',
                                    array('course' => $course,
                                    'classroom' => $location,
                                    'event' => $event),
                                    $waitinglist);
            } else {
                EmailTemplate::send_to_all_users_in_department($departmentid,
                                                            'advertise_classroom_based_course',
                                                            array('course' => $course,
                                                            'classroom' => $location,
                                                            'event' => $event));
                redirect("$CFG->wwwroot/mod/trainingevent/view.php?id=$id", get_string('emailssent', 'trainingevent'));
            }
            die;
        }

        // Get the current number booked on it.
        $numattending = $DB->count_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 0));

        $eventtable = "<h2>$event->name</h2>";
        if (!empty($messagestring)) {
            $eventtable .= "<p>$messagestring</p>";
        }
        $eventtable .= "<table><tr>";
        if (has_capability('mod/trainingevent:invite', $context)) {
            $eventtable .= "<td>".$OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/mod/trainingevent/view.php',
                                                         array('id' => $id,
                                                               'publish' => 1)),
                                                         get_string('publish', 'trainingevent')). "</td>";
        }
        if (has_capability('mod/trainingevent:invite', $context) && !empty($event->haswaitinglist)) {
            $eventtable .= "<td>".$OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/mod/trainingevent/view.php',
                                                         array('id' => $id,
                                                               'publish' => 1,
                                                               'waiting' => 1)),
                                                         get_string('publishwaitlist', 'trainingevent')). "</td>";
        }
        if (has_capability('mod/trainingevent:viewattendees', $context)) {
            $eventtable .= "<td>".$OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/mod/trainingevent/view.php',
                                                         array('id' => $id,
                                                               'view' => 1)),
                                                         get_string('viewattendees', 'trainingevent'))."</td>";
        }
        if (has_capability('mod/trainingevent:viewattendees', $context) && !empty($event->haswaitinglist)) {
            $eventtable .= "<td>".$OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/mod/trainingevent/view.php',
                                                         array('id' => $id,
                                                               'view' => 1,
                                                               'waiting' => 1)),
                                                         get_string('viewwaitlist', 'trainingevent'))."</td>";
        }
        if (has_capability('mod/trainingevent:add', $context) && $numattending < $maxcapacity
                            && time() < $event->startdatetime) {
            $eventtable .= "<td>".$OUTPUT->single_button(new moodle_url("/mod/trainingevent/searchusers.php",
                                                                        array('eventid' => $event->id)),
                                                                        get_string('selectother',
                                                                        'trainingevent')). "</td>";
        }
        $eventtable .= "</tr></table>";
        $eventtable .= "<table>";
        $eventtable .= "<tr><th>" . get_string('location', 'trainingevent') . "</th><td>" . $location->name . "</td></tr>";
        $eventtable .= "<tr><th>" . get_string('address') . "</th><td>" . $location->address . "</td></tr>";
        $eventtable .= "<tr><th>" . get_string('city') . "</th><td>" . $location->city . "</td></tr>";
        $eventtable .= "<tr><th>" . get_string('postcode', 'block_iomad_commerce') . "</th><td>" .
                       $location->postcode . "</td></tr>";
        $eventtable .= "<tr><th>" . get_string('country') . "</th><td>" . $location->country . "</td></tr>";
        $dateformat = "d F Y, g:ia";

        $eventtable .= "<tr><th>" . get_string('startdatetime', 'trainingevent') . "</th><td>" .
                        date($dateformat, $event->startdatetime) . "</td></tr>";
        $eventtable .= "<tr><th>" . get_string('enddatetime', 'trainingevent') . "</th><td>" .
                        date($dateformat, $event->enddatetime) . "</td></tr>";
        $eventtable .= "<tr><th>" . get_string('capacity', 'trainingevent') . "</th><td>" .
                        $attendancecount .get_string('of', 'trainingevent') . $maxcapacity . "</td></tr>";
        $eventtable .= "</table>";
        $eventtable .= "<div>$event->intro</div>";

        if (!$dodownload) {
            echo $OUTPUT->header();

            // Check the userid is valid.
            if (!company::check_valid_user($company->id, $USER->id, $departmentid)) {
                print_error('invaliduserdepartment', 'block_iomad_company_management');
            }

            echo $eventtable;

            // Output the buttons.
            if ($attending) {
                echo get_string('youareattending', 'trainingevent');
                if (time() < $event->startdatetime) {
                    echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                array('id' => $id, 'attending' => 'no')),
                                                get_string("unattend", 'trainingevent'));
                } else {
                    echo get_string('eventhaspassed', 'trainingevent');
                }
            } else {

                if (time() < $event->startdatetime) {
                    if ($numattending < $maxcapacity) {
                        if (!trainingevent_event_clashes($event, $USER->id)) {
                            if ($event->approvaltype == 0) {
                               echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                            array('id' => $id,
                                                                  'attending' => 'yes')),
                                                            get_string("attend", 'trainingevent'));
                            }
                        } else if ($event->approvaltype != 4 ) {
                            if (!$mybooking = $DB->get_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                                    'userid' => $USER->id))) {

                                echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                            array('id' => $id, 'booking' => 'yes')),
                                                            get_string("request", 'trainingevent'));
                            } else {
                                if ($mybooking->tm_ok == 0 || $mybooking->manager_ok == 0) {
                                    echo '<h2>'.get_string('approvalrequested', 'mod_trainingevent').'</h2>';
                                    if (time() < $event->startdatetime) {
                                        echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                                    array('id' => $id, 'booking' => 'no')),
                                                                    get_string("removerequest", 'trainingevent'));
                                    } else {
                                        echo get_string('eventhaspassed', 'trainingevent');
                                    }
                                } else {
                                    echo '<h2>'.get_string('approvaldenied', 'mod_trainingevent').'</h2>';
                                    if (time() < $event->startdatetime) {
                                        echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                                    array('id' => $id, 'booking' => 'again')),
                                                                    get_string("requestagain", 'trainingevent'));
                                    } else {
                                        echo get_string('eventhaspassed', 'trainingevent');
                                    }
                                }
                            }
                        } else {
                            echo "<h2>".get_string('enrolledonly', 'trainingevent')."</h2>";
                        }
                    } else {
                        if (!empty($event->haswaitinglist)) {
                            if (!$DB->get_records('trainingevent_users', array('userid' =>$USER->id, 'trainingeventid' => $event->id, 'waitlisted' => 1))) {
                                echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                array('id' => $id, 'attending' => 'yes', 'waiting' => 1)),
                                get_string("waitlist", 'trainingevent'));
                            } else {
                                echo get_string('youarewaiting', 'trainingevent');
                            }
                        } else {
                            echo get_string('fullybooked', 'trainingevent');
                        }
                    }
                } else {
                    echo get_string('eventhaspassed', 'trainingevent');
                }
            }

            // Output the attendees.
            if (!empty($view) && has_capability('mod/trainingevent:viewattendees', $context)) {
                // Get the associated department id.
                $company = new company($location->companyid);
                $parentlevel = company::get_company_parentnode($company->id);
                $companydepartment = $parentlevel->id;

                if (has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
                    $userhierarchylevel = $parentlevel->id;
                } else {
                    $userlevel = $company->get_userlevel($USER);
                    $userhierarchylevel = $userlevel->id;
                }
                $departmentid = $userhierarchylevel;

                $allowedusers = company::get_recursive_department_users($departmentid);
                $allowedlist = '0';
                foreach ($allowedusers as $alloweduser) {
                    if ($allowedlist == '0') {
                        $allowedlist = $alloweduser->userid;
                    } else {
                        $allowedlist .= ', '.$alloweduser->userid;
                    }
                }
                // Get the list of other events in this course.
                $eventselect = array();
                $courseevents = $DB->get_records('trainingevent', array('course' => $event->course));
                foreach ($courseevents as $courseevent) {
                    $courselocation = $DB->get_record('classroom', array('id' => $courseevent->classroomid));
                    $eventselect[$courseevent->id] = $courseevent->name . ' - ' . $courselocation->name.
                                                     ' '.date($dateformat, $courseevent->startdatetime);
                }
                    // We have other possible.
                $attendancetable = new html_table();
                $attendancetable->width = '95%';
                $attendancetable->head = array(get_string('fullname'), get_string('email'));
                $attendancetable->align = array("left", "left");
                if (has_capability('mod/trainingevent:add', $context)) {
                    $attendancetable->head[] = get_string('event', 'trainingevent');
                    $attendancetable->head[] = get_string('action', 'trainingevent');
                    $attendancetable->align[] = "center";
                    $attendancetable->align[] = "center";
                }
                if (has_capability('mod/trainingevent:grade', $context) && $waitingoption == 0) {
                    $attendancetable->head[] = get_string('grade');
                    $attendancetable->align[] = "center";
                }

                if ($users = $DB->get_records_sql('SELECT userid AS id FROM {trainingevent_users}
                                                   WHERE trainingeventid='.$event->id.'
                                                   AND userid IN ('.$allowedlist.')
                                                   AND waitlisted=:waitlisted', 
                                                   array('waitlisted' => $waitingoption)
                                                   )) {
                    foreach ($users as $user) {
                        $fulluserdata = $DB->get_record('user', array('id' => $user->id));
                        $userrow = array($fulluserdata->firstname.' '.$fulluserdata->lastname, $fulluserdata->email);
                        if (has_capability('mod/trainingevent:add', $context)) {
                            $select = new single_select(new moodle_url('/mod/trainingevent/view.php',
                                                                       array('userid' => $user->id,
                                                                             'id' => $id,
                                                                             'view' => 1)),
                                                                       'chosenevent',
                                                                       $eventselect,
                                                                       $event->id);
                            $select->formid = 'chooseevent'.$user->id;
                            $eventselecthtml = html_writer::tag('div',
                                                                $OUTPUT->render($select),
                                                                array('id' => 'iomad_event_selector'));
                            $actionhtml = "";
                            if ($waitingoption && $numattending < $maxcapacity) {
                                $actionhtml = $OUTPUT->single_button(new moodle_url('view.php',
                                                                                     array('userid' => $user->id,
                                                                                           'id' => $id,
                                                                                           'action' => 'add',
                                                                                        'view' => 1 )),
                                                                                     get_string("add"));
                                $actionhtml .= "&nbsp";
                            }
                            $actionhtml .= $OUTPUT->single_button(new moodle_url('view.php',
                                                                                  array('userid' => $user->id,
                                                                                        'id' => $id,
                                                                                        'action' => 'delete',
                                                                                        'view' => 1 )),
                                                                                  get_string("remove", 'trainingevent'));
                            $userrow[] = $eventselecthtml;
                            $userrow[] = $actionhtml;
                        }

                        if (has_capability('mod/trainingevent:grade', $context) && $waitingoption == 0) {
                            $usergradeentry = grade_get_grades($event->course, 'mod', 'trainingevent', $event->id, $user->id);
                            $gradehtml = '<form action="view.php" method="get">
                                         <input type="hidden" name="id" value="' . $id . '" />
                                         <input type="hidden" name="userid" value="'.$user->id.'" />
                                         <input type="hidden" name="action" value="grade" />
                                         <input type="hidden" name="view" value="1" />
                                         <input type="text" name="usergrade" id="id_usergrade"
                                                value="'.$usergradeentry->items[0]->grades[$user->id]->str_grade.'" />
                                         <input type="submit" value="' . get_string('grade') . '" />
                                         </form>';

                            $userrow[] = $gradehtml;
                        }
                        $attendancetable->data[] = $userrow;
                    }
                }
                echo "<h3>".get_string('attendance', 'local_report_attendance')."</h3>";
                if (!$waitingoption) {
                    echo $OUTPUT->single_button($CFG->wwwroot."/mod/trainingevent/view.php?id=".$id."&dodownload=1",
                                                get_string("downloadcsv",
                                                'local_report_attendance'));
                    if (has_capability('mod/trainingevent:resetattendees', $context)) {
                        echo $OUTPUT->single_button("$CFG->wwwroot/mod/trainingevent/view.php?id=$id&action=reset",
                                                                        get_string('resetattending', 'trainingevent'))."</td>";
                    }
                }
                echo html_writer::start_tag('div', array('id' => 'trainingeventattendancetable'));
                echo html_writer::table($attendancetable);
                echo html_writer::end_tag('div');
            }
            if (has_capability('mod/trainingevent:grade', $context)) {
                echo '<input type="submit" value="' . get_string('grade') . '" />';
            }
            echo $OUTPUT->footer();
        } else {
            // Get the associated department id.
            $company = new company($location->companyid);
            $parentlevel = company::get_company_parentnode($company->id);
            $companydepartment = $parentlevel->id;

            if (has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
                $userhierarchylevel = $parentlevel->id;
            } else {
                $userlevel = $company->get_userlevel($USER);
                $userhierarchylevel = $userlevel->id;
            }
            $departmentid = $userhierarchylevel;

            $allowedusers = company::get_recursive_department_users($departmentid);
            $allowedlist = "";
            foreach ($allowedusers as $alloweduser) {
                if (empty($allowedlist)) {
                    $allowedlist = $alloweduser->userid;
                } else {
                    $allowedlist .= ', '.$alloweduser->userid;
                }
            }

            // Output everything to a file.
            header("Content-Type: application/download\n");
            header("Content-Disposition: attachment; filename=\"".$event->name.".csv\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");
            echo "\"$event->name, $location->name, $location->address, $location->city, $location->country, $location->postcode\"\n";
            echo "\"".get_string('fullname')."\",\"". get_string('email')."\",\"".get_string('grade')."\"\n";
            if ($users = $DB->get_records_sql('SELECT userid AS id
                                               FROM {trainingevent_users}
                                               WHERE trainingeventid='.$event->id.'
                                               AND userid IN ('.$allowedlist.') AND waitlisted=0')) {
                foreach ($users as $user) {
                    $fulluserdata = $DB->get_record('user', array('id' => $user->id));
                    $usergradeentry = grade_get_grades($event->course, 'mod', 'trainingevent', $event->id, $user->id);
                    if (!empty($usergradeentry->items[0]->grades[$user->id]->str_grade)) {
                        $user->grade = $usergradeentry->items[0]->grades[$user->id]->str_grade;
                    } else {
                        $user->grade = "";
                    }
                    echo "\"$fulluserdata->firstname $fulluserdata->lastname\", \"$fulluserdata->email\", \"".$user->grade."\"\n";
                }
            }
            exit;
        }
    }
}


