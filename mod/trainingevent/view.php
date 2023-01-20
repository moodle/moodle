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
 * @package   mod_trainingevent
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot."/local/email/lib.php");
require_once($CFG->libdir."/gradelib.php");
require_once('lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->libdir.'/bennu/bennu.inc.php');

$id = required_param('id', PARAM_INT);    // Course Module ID, or.
$attending = optional_param('attending', null, PARAM_ALPHA);
$view = optional_param('view', 0, PARAM_INTEGER);
$waitingoption = optional_param('waiting', 0, PARAM_INTEGER);
$publish = optional_param('publish', 0, PARAM_INTEGER);
$dodownload = optional_param('dodownload', 0, PARAM_INTEGER);
$exportcalendar = optional_param('exportcalendar', null, PARAM_CLEAN);
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
            if (empty($location->isvirtual)) {
                $maxcapacity = $location->capacity;
            } else {
                $maxcapacity = 99999999999999999999;
            }
        }

        if (has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = key($userlevel);
        }
        $departmentid = $userhierarchylevel;

        // Get the CMID.
        $cmidinfo = $DB->get_record_sql("SELECT * FROM {course_modules}
                                         WHERE instance = :eventid
                                         AND module = ( SELECT id FROM {modules}
                                           WHERE name = 'trainingevent')", array('eventid' => $event->id));

        // What is the users approval level, if any?
        if (has_capability('block/iomad_company_admin:company_add', context_system::instance()) ||
            $manageruser = $DB->get_records('company_users', array('userid' => $USER->id, 'managertype' => 1))) {
            $myapprovallevel = "company";
        } else if ($manageruser = $DB->get_records('company_users', array('userid' => $USER->id, 'managertype' => 2))) {
            $myapprovallevel = "department";
        } else {
            $myapprovallevel = "none";
        }

        if (!empty($exportcalendar)) {
            if ($calendareventrec = $DB->get_record('event',['userid' => $USER->id,
                                                                         'courseid' => 0,
                                                                         'modulename' => 'trainingevent',
                                                                         'instance' => $event->id])) {
                $calendarevent = calendar_event::load($calendareventrec->id);
                $ical = new iCalendar;
                $ical->add_property('method', 'PUBLISH');
                $ical->add_property('prodid', '-//Moodle Pty Ltd//NONSGML Moodle Version ' . $CFG->version . '//EN');
                $ev = new iCalendar_event; // To export in ical format.
                $hostaddress = str_replace('http://', '', $CFG->wwwroot);
                $hostaddress = str_replace('https://', '', $hostaddress);

                $ev->add_property('uid', $calendarevent->id.'@'.$hostaddress);

                // Set iCal event summary from event name.
                $ev->add_property('summary', format_string($calendarevent->name, true, ['context' => $calendarevent->context]));

                // Format the description text.
                $description = format_text($calendarevent->description, $calendarevent->format, ['context' => $calendarevent->context]);
                // Then convert it to plain text, since it's the only format allowed for the event description property.
                // We use html_to_text in order to convert <br> and <p> tags to new line characters for descriptions in HTML format.
                $description = html_to_text($description, 0);
                $ev->add_property('description', $description);

                $ev->add_property('class', 'PUBLIC'); // PUBLIC / PRIVATE / CONFIDENTIAL
                $ev->add_property('last-modified', Bennu::timestamp_to_datetime($calendarevent->timemodified));

                if (!empty($calendarevent->location)) {
                    $ev->add_property('location', $calendarevent->location);
                }

                $ev->add_property('dtstamp', Bennu::timestamp_to_datetime()); // now
                if ($calendarevent->timeduration > 0) {
                    //dtend is better than duration, because it works in Microsoft Outlook and works better in Korganizer
                    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($calendarevent->timestart)); // when event starts.
                    $ev->add_property('dtend', Bennu::timestamp_to_datetime($calendarevent->timestart + $calendarevent->timeduration));
                } else if ($calendarevent->timeduration == 0) {
                    // When no duration is present, the event is instantaneous event, ex - Due date of a module.
                    // Moodle doesn't support all day events yet. See MDL-56227.
                    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($calendarevent->timestart));
                    $ev->add_property('dtend', Bennu::timestamp_to_datetime($calendarevent->timestart));
                } else {
                    // This can be used to represent all day events in future.
                    throw new coding_exception("Negative duration is not supported yet.");
                }
                if ($calendarevent->courseid != 0) {
                    $coursecontext = context_course::instance($calendarevent->courseid);
                    $ev->add_property('categories', format_string($course->shortname));
                }
                $ical->add_component($ev);

                $serialized = $ical->serialize();
                if(empty($serialized)) {
                    // TODO
                    die('bad serialization');
                }

                $filename = 'icalexport.ics';

                header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
                header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
                header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
                header('Pragma: no-cache');
                header('Accept-Ranges: none'); // Comment out if PDFs do not work...
                header('Content-disposition: attachment; filename='.$filename);
                header('Content-length: '.strlen($serialized));
                header('Content-type: text/calendar; charset=utf-8');

                echo $serialized;
                die;            
            }
        }

        if (!empty($attending)) {
            $companyid = iomad::get_my_companyid(context_system::instance());
            $usercompany = new company($companyid);
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
                        // Mark the user as attending.
                        $res = $DB->update_record('trainingevent_users', array('id'=>$record->id, 'trainingeventid' => $event->id, 'userid' => $USER->id, 'waitlisted'=>0));

                        // Is this an exclusive event?
                        if ($event->isexclusive) {
                            // Remove the user from any other waitinglists in this course which are exclusive.
                            if ($otherevents = $DB->get_records('trainingevent', ['course' => $event->course, 'isexclusive' => 1])) {
                                foreach ($otherevents as $otherevent) {
                                    $DB->delete_records('trainingevent_users', ['trainingeventid' => $otherevent->id, 'userid' => $USER->id, 'waitlisted' => 1]);
                                }
                            } 
                        }
                    } else {
                        $res = $DB->insert_record('trainingevent_users', array('trainingeventid' => $event->id, 'userid' => $USER->id));
                    }
                    if (empty($res)) {
                        print_error('error creating attendance record');
                    } else {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {
                            EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                                  'user' => $USER,
                                                                                  'classroom' => $location,
                                                                                  'company' => $usercompany,
                                                                                  'event' => $event));
                        }

                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\user_attending::create(array('context' => context_module::instance($id),
                                                                                             'userid' => $USER->id,
                                                                                             'objectid' => $event->id,
                                                                                             'courseid' => $event->course));
                        $moodleevent->trigger();

                        // Add to the users calendar.
                        $calendarevent = new stdClass();
                        $calendarevent->eventtype = 'user';
                        $calendarevent->type = CALENDAR_EVENT_TYPE_ACTION; // This is used for events we only want to display on the calendar, and are not needed on the block_myoverview.
                        $calendarevent->name = get_string('calendartitle', 'trainingevent', (object) ['coursename' => format_string($course->fullname), 'eventname' => format_string($event->name)]);
                        $calendarevent->description = format_module_intro('trainingevent', $event, $cmidinfo->id, false);
                        $calendarevent->format = FORMAT_HTML;
                        $eventlocation = format_string($location->name);
                        if (!empty($location->address)) {
                            $eventlocation .= ", " . format_string($location->address);
                        }
                        if (!empty($location->city)) {
                            $eventlocation .= ", " . format_string($location->city);
                        }
                        if (!empty($location->country)) {
                            $eventlocation .= ", " . format_string($location->country);
                        }
                        if (!empty($location->postcode)) {
                            $eventlocation .= ", " . format_string($location->postcode);
                        }
                        $calendarevent->location = $eventlocation; 
                        $calendarevent->courseid = 0;
                        $calendarevent->groupid = 0;
                        $calendarevent->userid = $USER->id;
                        $calendarevent->modulename = 'trainingevent';
                        $calendarevent->instance = $event->id;
                        $calendarevent->timestart = $event->startdatetime;
                        $calendarevent->visible = instance_is_visible('trainingevent', $event);
                        $calendarevent->timeduration = $event->enddatetime - $event->startdatetime;

                        calendar_event::create($calendarevent, false);

                        // Do we need to notify teachers?
                        if (!empty($event->emailteachers)) {
                            // Are we using groups?
                            $usergroups = groups_get_user_groups($course->id, $USER->id);
                            $userteachers = [];
                            foreach ($usergroups as $usergroup => $junk) {
                                $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                            } 
                            foreach ($userteachers as $userteacher) {

                                // Send an email as long as it hasn't already started.
                                if ($event->startdatetime > time()) {
                                    EmailTemplate::send('user_signed_up_for_event_teacher', array('course' => $course,
                                                                                                  'approveuser' => $USER,
                                                                                                  'user' => $userteacher,
                                                                                                  'classroom' => $location,
                                                                                                  'company' => $usercompany,
                                                                                                  'event' => $event));
                                }
                            }
                        }
                    }
                }
            } else if ('no' == $attending) {
                if ($attendingrecord = $DB->get_record('trainingevent_users', array('trainingeventid' => $event->id,
                                                                                    'userid' => $USER->id))) {
                    if (!$DB->delete_records('trainingevent_users', array('id' => $attendingrecord->id))) {
                        print_error('error removing attendance record');
                    } else {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {
                            if ($waitingoption) {
                                $emailtemplatename = "user_removed_from_event_waitlist";
                            } else {
                                $emailtemplatename = "user_removed_from_event";
                            }
                            EmailTemplate::send($emailtemplatename, array('course' => $course,
                                                                          'user' => $USER,
                                                                          'classroom' => $location,
                                                                          'company' => $usercompany,
                                                                          'event' => $event));
                        }

                        // Fire an event for this.
                        $moodleeventother = ['waitlisted' => $waitingoption];
                        $moodleevent = \mod_trainingevent\event\user_removed::create(['context' => context_module::instance($id),
                                                                                      'userid' => $USER->id,
                                                                                      'relateduserid' => $USER->id,
                                                                                      'objectid' => $event->id,
                                                                                      'courseid' => $event->course,
                                                                                      'other' => $moodleeventother]);
                        $moodleevent->trigger();

                        // Remove from the users calendar.
                        if ($calendareventrec = $DB->get_record('event',['userid' => $USER->id,
                                                                         'courseid' => 0,
                                                                         'modulename' => 'trainingevent',
                                                                         'instance' => $event->id])) {
                            $calendarevent = calendar_event::load($calendareventrec->id);
                            $calendarevent->delete(true);
                        }

                        // Do we need to notify teachers?
                        if (!empty($event->emailteachers)) {
                            // Are we using groups?
                            $usergroups = groups_get_user_groups($course->id, $USER->id);
                            $userteachers = [];
                            foreach ($usergroups as $usergroup => $junk) {
                                $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                            } 
                            foreach ($userteachers as $userteacher) {

                                // Send an email as long as it hasn't already started.
                                if ($event->startdatetime > time()) {
                                    EmailTemplate::send('user_removed_from_event_teacher', array('course' => $course,
                                                                                                 'approveuser' => $USER,
                                                                                                 'user' => $userteacher,
                                                                                                 'classroom' => $location,
                                                                                                 'company' => $usercompany,
                                                                                                 'event' => $event));
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!empty($booking)) {
            $companyid = iomad::get_my_companyid(context_system::instance());
            $usercompany = new company($companyid);
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
                        $DB->delete_records('trainingevent_users', array('trainingeventid' => $event->id, 'userid' => $USER->id, 'waitlisted' => 1));

                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);
                        // Get the list of managers we need to send an email to.
                        if ($event->approvaltype != 2 ) {
                            $mymanagers = $company->get_my_managers($USER->id, 2);
                        } else {
                            $mymanagers = $company->get_my_managers($USER->id, 1);
                        }
                        if (empty($mymanagers)) {
                            $mymanagers = $company->get_my_managers($USER->id, 1);
                        }

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {
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
                                                                                   'company' => $usercompany,
                                                                                   'classroom' => $location));
                        }

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
                    $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);
                    // Get the list of managers we need to send an email to.
                    if ($event->approvaltype != 2 ) {
                        $mymanagers = $company->get_my_managers($USER->id, 2);
                    } else {
                        $mymanagers = $company->get_my_managers($USER->id, 1);
                    }

                    // Send an email as long as it hasn't already started.
                    if ($event->startdatetime > time()) {
                        foreach ($mymanagers as $mymanager) {
                            if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                       'user' => $manageruser,
                                                                                       'approveuser' => $USER,
                                                                                       'classroom' => $location,
                                                                                       'company' => $usercompany,
                                                                                       'event' => $event));
                            }
                        }
                        EmailTemplate::send('course_classroom_approval_request', array('course' => $course,
                                                                               'user' => $USER,
                                                                               'classroom' => $location,
                                                                               'company' => $usercompany,
                                                                               'event' => $event));
                    }

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

                        // Get the user's company.
                        $usercompany = company::by_userid($user->id);

                        // Add to the chosen event.
                        if (!$DB->get_record('trainingevent_users', array('userid' => $userid,
                                                                          'trainingeventid' => $chosenevent->id,
                                                                          'waitlisted' => 0))) {
                            $DB->insert_record('trainingevent_users', array('userid' => $userid,
                                                                            'trainingeventid' => $chosenevent->id,
                                                                            'waitlisted' => 0));
                            $messagestring = get_string('usermovedsuccessfully', 'trainingevent');
                            $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $chosenevent->startdatetime);

                            // Send an email as long as it hasn't already started.
                            if ($event->startdatetime > time()) {
                                EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                                      'user' => $user,
                                                                                      'company' => $usercompany,
                                                                                      'classroom' => $location,
                                                                                      'event' => $chosenevent));
                            }

                            // Add to the users calendar.
                            $calendarevent = new stdClass();
                            $calendarevent->eventtype = 'user';
                            $calendarevent->type = CALENDAR_EVENT_TYPE_ACTION; // This is used for events we only want to display on the calendar, and are not needed on the block_myoverview.
                            $calendarevent->name = get_string('calendartitle', 'trainingevent', (object) ['coursename' => format_string($course->fullname), 'eventname' => format_string($event->name)]);
                            $calendarevent->description = format_module_intro('trainingevent', $event, $cmidinfo->id, false);
                            $calendarevent->format = FORMAT_HTML;
                            $eventlocation = format_string($location->name);
                            if (!empty($location->address)) {
                                $eventlocation .= ", " . format_string($location->address);
                            }
                            if (!empty($location->city)) {
                                $eventlocation .= ", " . format_string($location->city);
                            }
                            if (!empty($location->country)) {
                                $eventlocation .= ", " . format_string($location->country);
                            }
                            if (!empty($location->postcode)) {
                                $eventlocation .= ", " . format_string($location->postcode);
                            }
                            $calendarevent->location = $eventlocation; 
                            $calendarevent->courseid = 0;
                            $calendarevent->groupid = 0;
                            $calendarevent->userid = $user->id;
                            $calendarevent->modulename = 'trainingevent';
                            $calendarevent->instance = $event->id;
                            $calendarevent->timestart = $event->startdatetime;
                            $calendarevent->visible = instance_is_visible('trainingevent', $event);
                            $calendarevent->timeduration = $event->enddatetime - $event->startdatetime;

                            calendar_event::create($calendarevent, false);

                            // Do we need to notify teachers?
                            if (!empty($event->emailteachers)) {
                                // Send an email as long as it hasn't already started.
                                if ($event->startdatetime > time()) {

                                    // Are we using groups?
                                    $usergroups = groups_get_user_groups($course->id, $userid);
                                    $userteachers = [];
                                    foreach ($usergroups as $usergroup => $junk) {
                                        $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                                    } 
                                    foreach ($userteachers as $userteacher) {
                                        EmailTemplate::send('user_signed_up_for_event_teacher', array('course' => $course,
                                                                                                      'approveuser' => $user,
                                                                                                      'user' => $userteacher,
                                                                                                      'classroom' => $location,
                                                                                                      'company' => $usercompany,
                                                                                                      'event' => $event));
                                    }
                                }
                            }
                        }
                        // Remove from the current event.
                        $DB->delete_records('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id));
                        if ($event->approvaltype != 0) {
                            $DB->delete_records('block_iomad_approve_access', array('userid' => $userid,
                                                                                    'activityid' => $event->id));
                        }
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {
                            if ($waitingoption) {
                                $emailtemplatename = "user_removed_from_event_waitlist";
                            } else {
                                $emailtemplatename = "user_removed_from_event";
                            }
                            EmailTemplate::send($emailtemplatename, array('course' => $course,
                                                                          'user' => $user,
                                                                          'company' => $usercompany,
                                                                          'classroom' => $location,
                                                                          'event' => $event));
                        }

                        // Remove from the users calendar.
                        if ($calendareventrec = $DB->get_record('event', ['userid' => $user->id,
                                                                         'courseid' => 0,
                                                                         'modulename' => 'trainingevent',
                                                                         'instance' => $event->id])) {
                            $calendarevent = calendar_event::load($calendareventrec->id);
                            $calendarevent->delete(true);
                        }

                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\attendance_changed::create(array('context' => context_module::instance($id),
                                                                                                 'userid' => $USER->id,
                                                                                                 'relateduserid' => $user->id,
                                                                                                 'objectid' => $event->id,
                                                                                                 'courseid' => $event->course));
                        $moodleevent->trigger();
 
                        // Do we need to notify teachers?
                        if (!empty($event->emailteachers)) {
                            // Send an email as long as it hasn't already started.
                            if ($event->startdatetime > time()) {

                                // Are we using groups?
                                $usergroups = groups_get_user_groups($course->id, $userid);
                                $userteachers = [];
                                foreach ($usergroups as $usergroup => $junk) {
                                    $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                                } 
                                foreach ($userteachers as $userteacher) {
                                    EmailTemplate::send('user_removed_from_event_teacher', array('course' => $course,
                                                                                                 'approveuser' => $user,
                                                                                                 'user' => $userteacher,
                                                                                                 'classroom' => $location,
                                                                                                 'company' => $usercompany,
                                                                                                 'event' => $event));
                                }
                            }
                        }
                    } else if (($chosenevent->approvaltype == 3 || $chosenevent->approvaltype == 2)
                               && $myapprovallevel == "department") {

                        // Get the user's company.
                        $usercompany = company::by_userid($user->id);

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
                                $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $chosenevent->startdatetime);
                                $user = $DB->get_record('user', array('id' => $userid));

                                // Send an email as long as it hasn't already started.
                                if ($event->startdatetime > time()) {
                                    // Get the list of managers we need to send an email to.
                                    $mymanagers = $company->get_my_managers($user->id, 1);
                                    foreach ($mymanagers as $mymanager) {
                                        if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                            EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                                   'user' => $manageruser,
                                                                                                   'approveuser' => $user,
                                                                                                   'event' => $chosenevent,
                                                                                                   'company' => $usercompany,
                                                                                                   'classroom' => $location));
                                        }
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
                            $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);
                            $user = $DB->get_record('user', array('id' => $userid));

                            // Send an email as long as it hasn't already started.
                            if ($event->startdatetime > time()) {
                                // Get the list of managers we need to send an email to.
                                $mymanagers = $company->get_my_managers($USER->id, 1);
                                foreach ($mymanagers as $mymanager) {
                                    if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                        EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                               'user' => $manageruser,
                                                                                               'approveuser' => $user,
                                                                                               'classroom' => $location,
                                                                                               'company' => $usercompany,
                                                                                               'event' => $chosenevent));
                                    }
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
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {
                            if ($waitingoption) {
                                $emailtemplatename = "user_removed_from_event_waitlist";
                            } else {
                                $emailtemplatename = "user_removed_from_event";
                            }
                            EmailTemplate::send($emailtemplatename, array('course' => $course,
                                                                          'user' => $user,
                                                                          'classroom' => $location,
                                                                          'company' => $usercompany,
                                                                          'event' => $event));
                        }

                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\attendance_changed::create(array('context' => context_module::instance($id),
                                                                                                 'userid' => $USER->id,
                                                                                                 'relateduserid' => $user->id,
                                                                                                 'objectid' => $event->id,
                                                                                                 'courseid' => $event->course));
                        $moodleevent->trigger();

                        // Do we need to notify teachers?
                        if (!empty($event->emailteachers)) {

                            // Send an email as long as it hasn't already started.
                            if ($event->startdatetime > time()) {

                                // Are we using groups?
                                $usergroups = groups_get_user_groups($course->id, $userid);
                                $userteachers = [];
                                foreach ($usergroups as $usergroup => $junk) {
                                    $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                                } 
                                foreach ($userteachers as $userteacher) {
                                    EmailTemplate::send('user_removed_from_event_teacher', array('course' => $course,
                                                                                                 'approveuser' => $user,
                                                                                                 'user' => $userteacher,
                                                                                                 'classroom' => $location,
                                                                                                 'company' => $usercompany,
                                                                                                 'event' => $event));
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($action == 'delete' && !empty($userid)) {

            // Get the user's company.
            $usercompany = company::by_userid($userid);

            // Remove the userid from the event.
            if ($DB->delete_records('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id))) {
                $messagestring = get_string('userremovedsuccessfully', 'trainingevent');
                $user = $DB->get_record('user', array('id' => $userid));
                $course = $DB->get_record('course', array('id' => $event->course));
                $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);

                // Send an email as long as it hasn't already started.
                if ($event->startdatetime > time()) {
                    if ($waitingoption) {
                        $emailtemplatename = "user_removed_from_event_waitlist";
                    } else {
                        $emailtemplatename = "user_removed_from_event";
                    }
                    EmailTemplate::send($emailtemplatename, array('course' => $course,
                                                                  'user' => $user,
                                                                  'classroom' => $location,
                                                                  'company' => $usercompany,
                                                                  'event' => $event));
                }

                // Fire an event for this.
                $moodleeventother = ['waitlisted' => $waitingoption];
                $moodleevent = \mod_trainingevent\event\user_removed::create(['context' => context_module::instance($id),
                                                                              'userid' => $USER->id,
                                                                              'relateduserid' => $user->id,
                                                                              'objectid' => $event->id,
                                                                              'courseid' => $event->course,
                                                                              'other' => $moodleeventother]);
                $moodleevent->trigger();

                // Remove from the users calendar.
                if ($calendareventrec = $DB->get_record('event',['userid' => $user->id,
                                                                 'courseid' => 0,
                                                                 'modulename' => 'trainingevent',
                                                                 'instance' => $event->id])) {
                    $calendarevent = calendar_event::load($calendareventrec->id);
                    $calendarevent->delete(true);
                }

                // Do we need to notify teachers?
                if (!empty($event->emailteachers)) {

                    // Send an email as long as it hasn't already started.
                    if ($event->startdatetime > time()) {

                        // Are we using groups?
                        $usergroups = groups_get_user_groups($course->id, $userid);
                        $userteachers = [];
                        foreach ($usergroups as $usergroup => $junk) {
                            $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                        } 
                        foreach ($userteachers as $userteacher) {
                            EmailTemplate::send('user_removed_from_event_teacher', array('course' => $course,
                                                                                         'approveuser' => $user,
                                                                                         'user' => $userteacher,
                                                                                         'classroom' => $location,
                                                                                         'company' => $usercompany,
                                                                                         'event' => $event));
                        }
                    }
                }
            }
        }
        if ($action == 'add' && !empty($userid)) {
            // Get the user's company.
            $usercompany = company::by_userid($userid);

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
                        if (!empty($record->waitlisted)) {
                            $DB->set_field('trainingevent_users', 'waitlisted', 0, array('id' => $record->id));
                        }
                        else {
                            $DB->insert_record('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id, 'waitlisted' => 0));
                        }

                        $messagestring = get_string('useraddedsuccessfully', 'trainingevent');
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {
                            EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                                  'user' => $user,
                                                                                  'company' => $usercompany,
                                                                                  'classroom' => $location,
                                                                                  'event' => $event));
                        }

                        // Fire an event for this.
                        $moodleevent = \mod_trainingevent\event\user_added::create(array('context' => context_module::instance($id),
                                                                                         'userid' => $USER->id,
                                                                                         'relateduserid' => $user->id,
                                                                                         'objectid' => $event->id,
                                                                                         'courseid' => $event->course));
                        $moodleevent->trigger();

                        // Add to the users calendar.
                        $calendarevent = new stdClass();
                        $calendarevent->eventtype = 'user';
                        $calendarevent->type = CALENDAR_EVENT_TYPE_ACTION; // This is used for events we only want to display on the calendar, and are not needed on the block_myoverview.
                        $calendarevent->name = get_string('calendartitle', 'trainingevent', (object) ['coursename' => format_string($course->fullname), 'eventname' => format_string($event->name)]);
                        $calendarevent->description = format_module_intro('trainingevent', $event, $cmidinfo->id, false);
                        $calendarevent->format = FORMAT_HTML;
                        $eventlocation = format_string($location->name);
                        if (!empty($location->address)) {
                            $eventlocation .= ", " . format_string($location->address);
                        }
                        if (!empty($location->city)) {
                            $eventlocation .= ", " . format_string($location->city);
                        }
                        if (!empty($location->country)) {
                            $eventlocation .= ", " . format_string($location->country);
                        }
                        if (!empty($location->postcode)) {
                            $eventlocation .= ", " . format_string($location->postcode);
                        }
                        $calendarevent->location = $eventlocation; 
                        $calendarevent->courseid = 0;
                        $calendarevent->groupid = 0;
                        $calendarevent->userid = $user->id;
                        $calendarevent->modulename = 'trainingevent';
                        $calendarevent->instance = $event->id;
                        $calendarevent->timestart = $event->startdatetime;
                        $calendarevent->visible = instance_is_visible('trainingevent', $event);
                        $calendarevent->timeduration = $event->enddatetime - $event->startdatetime;

                        calendar_event::create($calendarevent, false);
                    }

                    // Do we need to notify teachers?
                    if (!empty($event->emailteachers)) {

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {

                            // Are we using groups?
                            $usergroups = groups_get_user_groups($course->id, $userid);
                            $userteachers = [];
                            foreach ($usergroups as $usergroup => $junk) {
                                $userteachers = $userteachers + get_enrolled_users($context, 'mod/trainingevent:viewattendees', $usergroup);
                            } 
                            foreach ($userteachers as $userteacher) {
                                EmailTemplate::send('user_signed_up_for_event_teacher', array('course' => $course,
                                                                                              'approveuser' => $user,
                                                                                              'user' => $userteacher,
                                                                                              'classroom' => $location,
                                                                                              'company' => $usercompany,
                                                                                              'event' => $event));
                            }
                        }
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
                            $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);
                            $user = $DB->get_record('user', array('id' => $userid));

                            // Send an email as long as it hasn't already started.
                            if ($event->startdatetime > time()) {
                                // Get the list of managers we need to send an email to.
                                $mymanagers = $company->get_my_managers($user->id, 1);
                                foreach ($mymanagers as $mymanager) {
                                    if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                        EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                               'user' => $manageruser,
                                                                                               'approveuser' => $user,
                                                                                               'event' => $event,
                                                                                               'company' => $usercompany,
                                                                                               'classroom' => $location));
                                    }
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
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);
                        $user = $DB->get_record('user', array('id' => $userid));

                        // Send an email as long as it hasn't already started.
                        if ($event->startdatetime > time()) {

                            // Get the list of managers we need to send an email to.
                            $mymanagers = $company->get_my_managers($USER->id, 1);
                            foreach ($mymanagers as $mymanager) {
                                if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                    EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                           'user' => $manageruser,
                                                                                           'approveuser' => $user,
                                                                                           'classroom' => $location,
                                                                                           'company' => $usercompany,
                                                                                           'event' => $event));
                                }
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
            $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);
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
        if (empty($location->isvirtual)) {
            $eventtable .= "<tr><th>" . get_string('address') . "</th><td>" . $location->address . "</td></tr>";
            $eventtable .= "<tr><th>" . get_string('city') . "</th><td>" . $location->city . "</td></tr>";
            $eventtable .= "<tr><th>" . get_string('postcode', 'block_iomad_commerce') . "</th><td>" .
                           $location->postcode . "</td></tr>";
            $eventtable .= "<tr><th>" . get_string('country') . "</th><td>" . $location->country . "</td></tr>";
        }
        $dateformat = "d F Y, g:ia";

        $eventtable .= "<tr><th>" . get_string('startdatetime', 'trainingevent') . "</th><td>" .
                        date($dateformat, $event->startdatetime) . "</td></tr>";
        $eventtable .= "<tr><th>" . get_string('enddatetime', 'trainingevent') . "</th><td>" .
                        date($dateformat, $event->enddatetime) . "</td></tr>";
        if ($attending) {
            $eventtable .= "<tr><th>" . get_string('exportcalendar', 'trainingevent') . "</th><td>";
            $exportlink = new moodle_url('/mod/trainingevent/view.php',
                                         array('id' => $id, 'exportcalendar' => 'yes'));
            $eventtable .=   "<a href='" . $exportlink . "' class='btn btn-secondary'>" . get_string('exportbutton', 'calendar') ."</a>";
            $eventtable .=   "</td></tr>";
        }
        if (empty($location->isvirtual) || !empty($event->coursecapacity)) {
            $eventtable .= "<tr><th>" . get_string('capacity', 'trainingevent') . "</th><td>" .
                            $attendancecount .get_string('of', 'trainingevent') . $maxcapacity . "</td></tr>";
        }
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
                echo html_writer::tag('h2', get_string('youareattending', 'trainingevent'));
                if (time() > $event->startdatetime) {
                    echo html_writer::tag('h2', get_string('eventhaspassed', 'mod_trainingevent'));
                } else if (!empty($event->lockdays) &&
                    time() + $event->lockdays*24*60*60 > $event->startdatetime) {
                    echo html_writer::tag('h2', get_string('eventislocked', 'mod_trainingevent'));
                } else {
                    echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                array('id' => $id, 'attending' => 'no')),
                                                get_string("unattend", 'mod_trainingevent'));
                    
                }
            } else {
                // Check if the event is still in the future.
                if (time() < $event->startdatetime) {
                    if ($numattending < $maxcapacity) {
                        if (!trainingevent_event_clashes($event, $USER->id)) {
                            $printbuttons = true;
                            if (time() > $event->startdatetime) {
                                echo html_writer::tag('h2', get_string('eventhaspassed', 'trainingevent'));
                                $printbuttons = false;
                            }
                            if (!empty($event->lockdays) &&
                                time() + $event->lockdays*24*60*60 > $event->startdatetime) {
                                echo html_writer::tag('h2', get_string('eventislocked', 'trainingevent'));
                                $printbuttons = false;
                            }
                            if ($printbuttons) {
                                if ($event->approvaltype == 0) {
                                   echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                                array('id' => $id,
                                                                      'attending' => 'yes')),
                                                                get_string("attend", 'trainingevent'));
                                } else if ($event->approvaltype != 4 ) {
                                    if (!$mybooking = $DB->get_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                                            'userid' => $USER->id))) {
        
                                        echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                                    array('id' => $id, 'booking' => 'yes')),
                                                                    get_string("request", 'trainingevent'));
                                    } else {
                                        if ($mybooking->tm_ok == 0 || $mybooking->manager_ok == 0) {
                                            echo html_writer::tag('h2', get_string('approvalrequested', 'mod_trainingevent'));
                                        } else {
                                            echo html_writer::tag('h2', get_string('approvaldenied', 'mod_trainingevent'));
                                                echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                                                            array('id' => $id, 'booking' => 'again')),
                                                                            get_string("requestagain", 'trainingevent'));
                                        }
                                    }
                                } else {
                                    echo html_writer::tag('h2', get_string('enrolledonly', 'trainingevent'));
                                }
                            }
                        } else {
                            echo html_writer::tag('h2', get_string('alreadyenrolled', 'trainingevent'));
                        }
                    } else {
                        if (!empty($event->haswaitinglist)) {
                            $printbuttons = true;
                            if (!empty($event->lockdays) &&
                                time() + $event->lockdays*24*60*60 > $event->startdatetime) {
                                echo html_writer::tag('h2', get_string('eventislocked', 'trainingevent'));
                                $printbuttons = false;
                            }
                            if ($printbuttons) {
                                if (!$DB->get_records('trainingevent_users', array('userid' =>$USER->id, 'trainingeventid' => $event->id, 'waitlisted' => 1))) {
                                    echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/view.php',
                                    array('id' => $id, 'attending' => 'yes', 'waiting' => 1)),
                                    get_string("waitlist", 'trainingevent'));
                                } else {
                                    echo html_writer::tag('h2', get_string('youarewaiting', 'trainingevent'));
                                }
                            }
                        } else {
                            echo html_writer::tag('h2', get_string('fullybooked', 'trainingevent'));
                        }
                    }
                } else {
                    echo html_writer::tag('h2', get_string('eventhaspassed', 'trainingevent'));
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
                    $userhierarchylevel = key($userlevel);
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
                    // Can't add someone to your own.
                    if ($courseevent->id == $event->id && empty($waitingoption) ) {
                        continue;
                    }
                    // is there space??
                    $currentcount = $DB->count_records('trainingevent_users',
                                                       ['trainingeventid' => $courseevent->id,
                                                       'waitlisted' => 0]);
                    if ($currentcount < $courseevent->coursecapacity) {
                        $courselocation = $DB->get_record('classroom', array('id' => $courseevent->classroomid));
                        $eventselect[$courseevent->id] = $courseevent->name . ' - ' . $courselocation->name.
                                                         ' '.date($dateformat, $courseevent->startdatetime);
                    }
                }

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
                    $attendancetable->head[] = get_string('grade', 'grades');
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
                                                                       ['userid' => $user->id,
                                                                        'id' => $id,
                                                                        'view' => 1,
                                                                        'waiting' => $waitingoption]),
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
                                                                                  ['userid' => $user->id,
                                                                                   'id' => $id,
                                                                                   'action' => 'delete',
                                                                                   'view' => 1,
                                                                                   'waiting' => $waitingoption]),
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
                                         <input type="submit" value="' . get_string('grade', 'grades') . '" />
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
                echo '<input type="submit" value="' . get_string('grade', 'grades') . '" />';
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
                $userhierarchylevel = key($userlevel);
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
            echo "\"".get_string('fullname')."\",\"". get_string('email')."\",\"".get_string('grade', 'grades')."\"\n";
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
