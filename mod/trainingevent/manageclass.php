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

$id = required_param('id', PARAM_INT);    // Course Module ID, or.
$attending = optional_param('attending', null, PARAM_ALPHA);
$view = optional_param('view', 0, PARAM_INTEGER);
$publish = optional_param('publish', 0, PARAM_INTEGER);
$dodownload = optional_param('dodownload', 0, PARAM_INTEGER);
$userid = optional_param('userid', 0, PARAM_INTEGER);
$usergrade = optional_param('usergrade', 0, PARAM_INTEGER);
$current = optional_param('current', 0, PARAM_INTEGER);
$chosen = optional_param('chosen', 0, PARAM_INTEGER);
$action = optional_param('action', null, PARAM_ALPHA);
$booking = optional_param('booking', null, PARAM_ALPHA);

// Get the database entry.
if (!$event = $DB->get_record('trainingevent', array('id' => $id))) {
    print_error('noinstance');
} else {
    if (!$location = $DB->get_record('classroom', array('id' => $event->classroomid))) {
        if (!empty($dodownload)) {
            die;
        }
        print_error('location not defined');
    } else {
        // Get the associated department id.
        $company = new company($location->companyid);
        $parentlevel = company::get_company_parentnode($company->id);
        $companydepartment = $parentlevel->id;

        if (!empty($attending)) {
            if ('yes' == $attending) {
                if (!$DB->get_record('trainingevent_users', array('trainingeventid' => $id, 'userid' => $USER->id))) {
                    if (!$DB->insert_record('trainingevent_users', array('trainingeventid' => $id, 'userid' => $USER->id))) {
                        print_error('error creating attendance record');
                    } else {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date('jS \of F Y \a\t h:i', $event->startdatetime);
                        EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                              'user' => $USER,
                                                                              'classroom' => $location));
                    }
                }
            } else if ('no' == $attending) {
                if ($attendingrecord = $DB->get_record('trainingevent_users', array('trainingeventid' => $id,
                                                                                    'userid' => $USER->id))) {
                    if (!$DB->delete_records('trainingevent_users', array('id' => $attendingrecord->id))) {
                        print_error('error removing attendance record');
                    } else {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date('jS \of F Y \a\t h:i', $event->startdatetime);
                        EmailTemplate::send('user_removed_from_event', array('course' => $course,
                                                                             'user' => $USER,
                                                                             'classroom' => $location));
                    }
                }
            }
        }
        if (!empty($booking)) {
            if ('yes' == $booking || 'again' == $booking) {
                if (!$DB->get_record('block_iomad_approve_access', array('activityid' => $id, 'userid' => $USER->id))) {
                    if (!$DB->insert_record('block_iomad_approve_access', array('activityid' => $id,
                                                                                'userid' => $USER->id,
                                                                                'courseid' => $event->course,
                                                                                'tm_ok' => 0,
                                                                                'manager_ok' => 0,
                                                                                'companyid' => $company->id))) {
                        print_error('error creating attendance record');
                    } else {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date('jS \of F Y \a\t h:i', $event->startdatetime);
                        // Get the list of managers we need to send an email to.
                        $mymanagers = $company->get_my_managers($USER->id, $event->approvaltype);
                        foreach ($mymanagers as $mymanager) {
                            if ($manageruser = $DB->get_record('user', array('id' => $mymanager->userid))) {
                                EmailTemplate::send('course_classroom_approval', array('course' => $course,
                                                                                       'user' => $manageruser,
                                                                                       'approveuser' => $USER,
                                                                                       'classroom' => $location));
                            }
                        }
                        EmailTemplate::send('course_classroom_approval_request', array('course' => $course,
                                                                               'user' => $USER,
                                                                               'classroom' => $location));
                    }
                } else {
                    $userbooking->tm_ok = 0;
                    $userbooking->manager_ok = 0;
                    $DB->update_record('block_iomad_approve_access', $userbooking);
                    if ($CFG->perficioemails) {
                        $course = $DB->get_record('course', array('id' => $event->course));
                        $location->time = date('jS \of F Y \a\t h:i', $event->startdatetime);
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
                                                                                       'classroom' => $location));
                            }
                        }
                        EmailTemplate::send('course_classroom_approval_request', array('course' => $course,
                                                                               'user' => $USER,
                                                                               'classroom' => $location));

                        add_to_log($course->id,
                                   'trainingevent',
                                   'User seeking approved access',
                                   'mod/trainingevent/manageclass.php',
                                   $event->id,
                                   $USER->id);
                    }
                }
            } else if ( 'no' == $booking) {
                if ($dereq = (array) $DB->get_record('block_iomad_approve_access', array('activityid' => $id,
                                                                                         'userid' => $USER->id))) {
                    $DB->delete_records('block_iomad_approve_access', $dereq);
                }
            }
        }
        if (!empty($chosen)) {
            // We are moving a user to another event  check there is space.
            if (!$chosenevent = $DB->get_record('trainingevent', array('id' => $chosen))) {
                print_error('chosen event is invalid');
            } else {
                $chosenlocation = $DB->get_record('classroom', array('id' => $chosenevent->classroomid));
                $alreadyattending = $DB->count_records('trainingevent_users', array('trainingeventid' => $chosenevent->id));
                if ($alreadyattending < $chosenlocation->capacity) {
                    // Add to the chosen event.
                    $user = $DB->get_record('user', array('id' => $userid));
                    $course = $DB->get_record('course', array('id' => $event->courseid));
                    if (!$DB->get_record('trainingevent_users', array('userid' => $userid,
                                                                      'trainingeventid' => $chosenevent->id))) {
                        $DB->insert_record('trainingevent_users', array('userid' => $userid,
                                                                        'trainingeventid' => $chosenevent->id));
                        $messagestring = get_string('usermovedsuccessfully', 'trainingevent');
                        EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                              'user' => $user,
                                                                              'classroom' => $location));
                    }
                    // Remove from the current event.
                    $DB->delete_records('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id));
                    EmailTemplate::send('user_removed_from_event', array('course' => $course,
                                                                         'user' => $user,
                                                                         'classroom' => $location));
                }
            }
        }
        if ($action == 'delete' && !empty($userid)) {
            // Remove the userid from the event.
            if ($DB->delete_records('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id))) {
                $messagestring = get_string('userremovedsuccessfully', 'trainingevent');
                $user = $DB->get_record('user', array('id' => $userid));
                $course = $DB->get_record('course', array('id' => $event->courseid));
                EmailTemplate::send('user_removed_from_event', array('course' => $course,
                                                                     'user' => $user,
                                                                     'classroom' => $location));
            }
        }
        if ($action == 'add' && !empty($userid)) {
            // Add to the chosen event.
            if (!$DB->get_record('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id))) {
                $DB->insert_record('trainingevent_users', array('userid' => $userid, 'trainingeventid' => $event->id));
                $messagestring = get_string('useraddedsuccessfully', 'trainingevent');
                $user = $DB->get_record('user', array('id' => $userid));
                $course = $DB->get_record('course', array('id' => $event->courseid));
                EmailTemplate::send('user_signed_up_for_event', array('course' => $course,
                                                                      'user' => $user,
                                                                      'classroom' => $location));
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
            grade_update('mod/trainingevent', $event->course, 'mod', 'trainingevent', $id, 0, $gradegrade, $gradeparams);
        }
        if ($attendance = (array) $DB->get_records('trainingevent_users', array('trainingeventid' => $id), null, 'userid')) {
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

        // Page stuff:.
        $url = new moodle_url('/course/view.php', array('id' => $event->course));
        $context = context_course::instance($event->course);
        require_login($event->course); // Adds to $PAGE, creates $OUTPUT.
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title($event->name);
        $PAGE->set_heading($SITE->fullname);

        // Are we sending out emails?
        if (!empty($publish)) {
            if (has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
                $userhierarchylevel = $parentlevel->id;
            } else {
                $userlevel = company::get_userlevel($USER);
                $userhierarchylevel = $userlevel->id;
            }
            $departmentid = $userhierarchylevel;

            echo $OUTPUT->header();
            echo "<h2>".get_string('sendingemails', 'trainingevent')."</h2>";
            $course = $DB->get_record('course', array('id' => $event->course));
            $course->url = new moodle_url('course/view.php', array('id' => $course->id));
            EmailTemplate::send_to_all_users_in_department($departmentid, 'advertise_classroom_based_course',
                                                           array('course' => $course, 'classroom' => $location));
            redirect("$CFG->wwwroot/mod/trainingevent/manageclass.php?id=$id", get_string('emailssent', 'trainingevent'));
            die;
        }

        $eventtable = "<h2>$event->name</h2>";
        if (!empty($messagestring)) {
            $eventtable .= "<p>$messagestring</p>";
        }
        $eventtable .= "<table><tr>";
        if (has_capability('mod/trainingevent:invite', $context)) {
            $eventtable .= "<td>".$OUTPUT->single_button("$CFG->wwwroot/mod/trainingevent/manageclass.php?id=$id&publish=1",
                            get_string('publish', 'trainingevent')). "</td>";
        }
        if (has_capability('mod/trainingevent:viewattendees', $context)) {
            $eventtable .= "<td>".$OUTPUT->single_button("$CFG->wwwroot/mod/trainingevent/manageclass.php?id=$id&view=1",
                            get_string('viewattendees', 'trainingevent'))."</td>";
        }
        if (has_capability('mod/trainingevent:invite', $context)) {
            $eventtable .= "<td>".$OUTPUT->single_button(new moodle_url("/mod/trainingevent/searchusers.php",
                                                                        array('eventid' => $id)),
                                                                        get_string('selectother',
                                                                        'trainingevent')).
                                                                        "</td>";
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
        $eventtable .= "<tr><th>" . get_string('capacity', 'trainingevent') .
                       "</th><td>" . $attendancecount . get_string('of', 'trainingevent') .
                       $location->capacity . "</td></tr>";
        $eventtable .= "</table>";
        $eventtable .= "<div>$event->intro</div>";

        if (!$dodownload) {
            echo $OUTPUT->header();
            echo $eventtable;

            // Output the buttons.
            if ($attending) {
                echo get_string('youareattending', 'trainingevent');
                if (time() < $event->startdatetime) {
                    echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/manageclass.php',
                                                array('id' => $id, 'attending' => 'no')),
                                                get_string("unattend", 'trainingevent'));
                } else {
                    echo get_string('eventhaspassed', 'trainingevent');
                }
            } else {
                if ($attending < $location->capacity) {
                    if (time() < $event->startdatetime) {
                        if (empty($event->approvaltype) || $event->approvaltype == 0) {
                            echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/manageclass.php',
                                                        array('id' => $id, 'attending' => 'yes')),
                                                        get_string("attend", 'trainingevent'));
                        } else if ($event->approvaltype != 4) {
                            if (!$DB->get_record('block_iomad_approve_access', array('activityid' => $event->id,
                                                                                     'userid' => $USER->id))) {
                                echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/manageclass.php',
                                                            array('id' => $id, 'booking' => 'yes')),
                                                            get_string("request", 'trainingevent'));
                            } else {
                                echo '<h2>'.get_string('approvalrequested', 'mod_trainingevent').'</h2>';
                                if (time() < $event->startdatetime) {
                                    echo $OUTPUT->single_button(new moodle_url('/mod/trainingevent/manageclass.php',
                                                                array('id' => $id, 'booking' => 'no')),
                                                                get_string("removerequest", 'trainingevent'));
                                } else {
                                    echo get_string('eventhaspassed', 'trainingevent');
                                }
                            }
                        } else {
                                echo "<h2>".get_string('enrolledonly', 'trainingevent')."</h2>";
                        }
                    } else {
                        echo get_string('eventhaspassed', 'trainingevent');
                    }
                } else {
                    if (time() < $event->startdatetime) {
                        echo get_string('fullybooked', 'trainingevent');
                    } else {
                        echo get_string('eventhaspassed', 'trainingevent');
                    }
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
                    $userlevel = company::get_userlevel($USER);
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
                // Get the list of other events in this course.
                $eventselect = array();
                $courseevents = $DB->get_records('trainingevent', array('course' => $event->course));
                foreach ($courseevents as $courseevent) {
                    $courselocation = $DB->get_record('classroom', array('id' => $courseevent->classroomid));
                    $eventselect[$courseevent->id] = $courselocation->name.' '.date($dateformat, $courseevent->startdatetime);
                }
                    // We have other possible.
                $attendancetable = new html_table();
                $attendancetable->width = '95%';
                if (has_capability('mod/trainingevent:grade', $context)) {
                    $attendancetable->head = array(get_string('fullname'),
                                                   get_string('email'),
                                                   get_string('event',
                                                   'trainingevent'),
                                                   get_string('action',
                                                   'trainingevent'),
                                                   get_string('grade'));
                    $attendancetable->align = array("left", "left", "center", "center", "center");
                } else {
                    $attendancetable->head = array(get_string('fullname'),
                                                   get_string('email'),
                                                   get_string('event', 'trainingevent'),
                                                   get_string('action', 'trainingevent'));
                    $attendancetable->align = array("left", "left", "center", "center");
                }

                if ($users = $DB->get_records_sql('SELECT userid AS id FROM {trainingevent_users}
                                                   WHERE trainingeventid='.$event->id.'
                                                   AND userid in ('.$allowedlist.')')) {
                    foreach ($users as $user) {
                        $select = new single_select(new moodle_url('/mod/trainingevent/manageclass.php',
                                                                    array('userid' => $user->id,
                                                                          'id' => $event->id,
                                                                          'view' => 1)),
                                                                    'chosen',
                                                                    $eventselect,
                                                                    $event->id);
                        $select->formid = 'chooseevent';
                        $eventselecthtml = html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_event_selector'));
                        $removebutton = $OUTPUT->single_button(new moodle_url('manageclass.php',
                                                                               array('userid' => $user->id,
                                                                                     'id' => $event->id,
                                                                                     'action' => 'delete',
                                                                                     'view' => 1 )),
                                                                               get_string("remove", 'trainingevent'));

                        $fulluserdata = $DB->get_record('user', array('id' => $user->id));
                        if (has_capability('mod/trainingevent:grade', $context)) {
                            $usergradeentry = grade_get_grades($event->course, 'mod', 'trainingevent', $id, $user->id);
                            $gradehtml = '<form action="manageclass.php" method="get">
                                         <input type="hidden" name="id" value="' . $id . '" />
                                         <input type="hidden" name="userid" value="'.$user->id.'" />
                                         <input type="hidden" name="action" value="grade" />
                                         <input type="hidden" name="view" value="1" />
                                         <input type="text" name="usergrade" id="id_usergrade" value="'.
                                         $usergradeentry->items[0]->grades[$user->id]->str_grade.'" />
                                         <input type="submit" value="' . get_string('grade') . '" />
                                         </form>';

                            $attendancetable->data[] = array($fulluserdata->firstname.' '.$fulluserdata->lastname,
                                                             $fulluserdata->email,
                                                             $eventselecthtml,
                                                             $removebutton,
                                                             $gradehtml);
                        } else {
                            $attendancetable->data[] = array($fulluserdata->firstname.' '.$fulluserdata->lastname,
                                                             $fulluserdata->email,
                                                             $eventselecthtml,
                                                             $removebutton);
                        }
                    }
                }
                echo "<h3>".get_string('attendance', 'local_report_attendance')."</h3>";
                echo $OUTPUT->single_button($CFG->wwwroot."/mod/trainingevent/manageclass.php?id=".$id."&dodownload=1",
                                            get_string("downloadcsv", 'local_report_attendance'));
                echo html_writer::table($attendancetable);
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
                $userlevel = company::get_userlevel($USER);
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
            echo "\"$event->name, $location->name, $location->address, $location->city, ".
                 "$location->country, $location->postcode\"\n";
            echo "\"".get_string('fullname')."\",\"". get_string('email')."\",\"".get_string('grade')."\"\n";
            if ($users = $DB->get_records_sql('SELECT userid AS id FROM {trainingevent_users}
                                               WHERE trainingeventid='.$event->id.'
                                               AND userid in ('.$allowedlist.')')) {
                foreach ($users as $user) {
                    $fulluserdata = $DB->get_record('user', array('id' => $user->id));
                    $usergradeentry = grade_get_grades($event->course, 'mod', 'trainingevent', $id, $user->id);
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
