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
 * Contains the mobile output class for the attendance
 *
 * @package   mod_attendance
 * @copyright 2018 Dan Marsden
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\output;

defined('MOODLE_INTERNAL') || die();
/**
 * Mobile output class for the attendance.
 *
 * @copyright 2018 Dan Marsden
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Subnet warning. - constants used to prevent warnings from showing multiple times.
     */
    const MESSAGE_SUBNET = 10;

    /**
     * Prevent shared warning. used to prevent warnings from showing multiple times.
     */
    const MESSAGE_PREVENTSHARED = 30;

    /**
     * Returns the initial page when viewing the activity for the mobile app.
     *
     * @param  array $args Arguments from tool_mobile_get_content WS
     * @return array HTML, javascript and other data
     */
    public static function mobile_view_activity($args) {
        global $OUTPUT, $DB, $USER, $USER, $CFG;

        require_once($CFG->dirroot.'/mod/attendance/locallib.php');

        $versionname = $args['appversioncode'] >= 3950 ? 'latest' : 'ionic3';
        $cmid = $args['cmid'];
        $courseid = $args['courseid'];
        $takenstatus = empty($args['status']) ? '' : $args['status'];
        $sessid = empty($args['sessid']) ? '' : $args['sessid'];
        $password = empty($args['studentpass']) ? '' : $args['studentpass'];

        // Capabilities check.
        $cm = get_coursemodule_from_id('attendance', $cmid);

        require_login($courseid, false , $cm, true, true);

        $context = \context_module::instance($cm->id);
        require_capability('mod/attendance:view', $context);

        $attendance    = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);
        $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        $config = get_config('attendance');

        $data = array(); // Data to pass to renderer.
        $data['cmid'] = $cmid;
        $data['courseid'] = $courseid;
        $data['attendance'] = $attendance;
        $data['timestamp'] = time(); // Used to prevent attendance session marking page to be cached.

        $data['attendancefunction'] = 'mobile_user_form';
        $isteacher = false;
        if (has_capability('mod/attendance:takeattendances', $context)) {
            $isteacher = true;
            $data['attendancefunction'] = 'mobile_teacher_form';
        }

        // Add stats for this use to output.
        $pageparams = new \mod_attendance_view_page_params();
        $pageparams->studentid = $USER->id;
        $pageparams->group = groups_get_activity_group($cm, true);
        $canseegroupsession = true;
        if (!empty($sessid) && (!empty($takenstatus) || $isteacher)) {
            $session = $DB->get_record('attendance_sessions', array('id' => $sessid));
            $pageparams->grouptype = $session->groupid;
            $pageparams->sessionid = $sessid;

            if ($isteacher && !empty($session->groupid)) {
                $allowedgroups = groups_get_activity_allowed_groups($cm);
                if (!array_key_exists($session->groupid, $allowedgroups)) {
                    $canseegroupsession = false;
                }
            }
        }
        $pageparams->mode = \mod_attendance_view_page_params::MODE_THIS_COURSE;
        $pageparams->view = 5; // Show all sessions for this course?

        $att = new \mod_attendance_structure($attendance, $cm, $course, $context, $pageparams);

        // Check if this teacher is allowed to view/mark this group session.

        if ($isteacher && $canseegroupsession) {
            $keys = array_keys($args);
            $userkeys = preg_grep("/status\d+/", $keys);
            if (!empty($userkeys)) { // If this is a post from the teacher form.
                // Build data to pass to take_from_form_data.
                $formdata = new \stdClass();
                foreach ($userkeys as $uk) {
                    $userid = str_replace('status', '', $uk);
                    $status = $args[$uk];
                    $formdata->{'remarks'.$userid} = '';
                    $formdata->{'user'.$userid} = $status;
                }
                $att->take_from_form_data($formdata);
                $data['showmessage'] = true;
                $data['messages'][]['string'] = 'attendancesuccess';
            }
        }

        // Get list of sessions based on site level settings. default = the next 24hrs and in last 6hrs.
        $timefrom = time() - $config->mobilesessionfrom;
        $timeto = time() + $config->mobilesessionto;

        $data['sessions'] = array();

        $sessions = $DB->get_records_select('attendance_sessions',
            'attendanceid = ? AND sessdate > ? AND sessdate < ? ORDER BY sessdate',
            array($attendance->id, $timefrom, $timeto));

        if (!empty($sessions)) {
            $userdata = new \attendance_user_data($att, $USER->id, true);
            foreach ($sessions as $sess) {
                if (!$isteacher && empty($userdata->sessionslog['c'.$sess->id])) {
                    // This session isn't viewable to this student - probably a group session.
                    continue;
                }

                // Check if this teacher is allowed to view this group session.
                if ($isteacher && !empty($sess->groupid)) {
                    $allowedgroups = groups_get_activity_allowed_groups($cm);
                    if (!array_key_exists($sess->groupid, $allowedgroups)) {
                        continue;
                    }
                }
                list($canmark, $reason) = attendance_can_student_mark($sess);
                if (!$isteacher && $reason == 'preventsharederror') {
                    $data['showmessage'] = true;
                    // Lang string to show as a message.
                    $data['messages'][self::MESSAGE_PREVENTSHARED]['string'] = 'preventsharederror';
                }

                if ($isteacher || $canmark) {
                    $html = array('time' => strip_tags(construct_session_full_date_time($sess->sessdate, $sess->duration)),
                        'groupname' => '');
                    if (!empty($sess->groupid)) {
                        // TODO In-efficient way to get group name - we should get all groups in one query.
                        $html['groupname'] = $DB->get_field('groups', 'name', array('id' => $sess->groupid));
                    }

                    // Check if Status already recorded.
                    if (!$isteacher && !empty($userdata->sessionslog['c'.$sess->id]->statusid)) {
                        $html['currentstatus'] = $userdata->statuses[$userdata->sessionslog['c'.$sess->id]->statusid]->description;
                    } else {
                        // Status has not been recorded - If student, check auto-assign and form data.
                        $html['sessid'] = $sess->id;

                        if (!$isteacher) {
                            if (!empty($sess->subnet) && !address_in_subnet(getremoteaddr(), $sess->subnet)) {
                                $data['showmessage'] = true;
                                // Lang string to show as a message.
                                $data['messages'][self::MESSAGE_SUBNET]['string'] = 'subnetwrong';
                                $html['sessid'] = null; // Unset sessid as we cannot record session on this ip.
                            } else if ($sess->autoassignstatus && empty($sess->studentpassword)) {
                                $statusid = attendance_session_get_highest_status($att, $sess);
                                if (empty($statusid)) {
                                    $data['showmessage'] = true;
                                    $data['messages'][]['string'] = 'attendance_no_status';
                                }
                                $take = new \stdClass();
                                $take->status = $statusid;
                                $take->sessid = $sess->id;
                                $success = $att->take_from_student($take);

                                if ($success) {
                                    $html['currentstatus'] = $userdata->statuses[$statusid]->description;
                                    $html['sessid'] = null; // Unset sessid as we have recorded session.
                                }
                            } else if ($sess->id == $sessid) {
                                if (!empty($sess->studentpassword) && $password != $sess->studentpassword) {
                                    // Password incorrect.
                                    $data['showmessage'] = true;
                                    $data['messages'][]['string'] = 'incorrectpasswordshort';
                                } else {
                                    $statuses = $att->get_statuses();
                                    // Check if user has access to all statuses.
                                    foreach ($statuses as $status) {
                                        if ($status->studentavailability === '0') {
                                            unset($statuses[$status->id]);
                                            continue;
                                        }
                                        if (!empty($status->studentavailability) &&
                                            time() > $sess->sessdate + ($status->studentavailability * 60)) {
                                            unset($statuses[$status->id]);
                                            continue;
                                        }
                                    }
                                    if ($sess->autoassignstatus) {
                                        // If this is an auto-assign, get the highest status available.
                                        $takenstatus = attendance_session_get_highest_status($att, $sess);
                                    }

                                    if (empty($statuses[$takenstatus])) {
                                        // This status has probably expired and is not available - they need to choose a new one.
                                        $data['showmessage'] = true;
                                        $data['messages'][]['string'] = 'invalidstatus';
                                    } else {
                                        $take = new \stdClass();
                                        $take->status = $takenstatus;
                                        $take->sessid = $sess->id;
                                        $success = $att->take_from_student($take);

                                        if ($success) {
                                            $html['currentstatus'] = $userdata->statuses[$takenstatus]->description;
                                            $html['sessid'] = null; // Unset sessid as we have recorded session.
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $data['sessions'][] = $html;
                }
            }
        }

        $summary = new \mod_attendance_summary($att->id, array($USER->id), $att->pageparams->startdate,
            $att->pageparams->enddate);
        $data['summary'] = $summary->get_all_sessions_summary_for($USER->id);

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template("mod_attendance/mobile_view_page_$versionname", $data),
                ],
            ],
            'javascript' => '',
            'otherdata' => ''
        ];
    }

    /**
     * Returns the form to take attendance for the mobile app.
     *
     * @param  array $args Arguments from tool_mobile_get_content WS
     * @return array HTML, javascript and other data
     */
    public static function mobile_user_form($args) {
        global $OUTPUT, $DB, $CFG;

        require_once($CFG->dirroot.'/mod/attendance/locallib.php');

        $args = (object) $args;
        $versionname = $args->appversioncode >= 3950 ? 'latest' : 'ionic3';
        $cmid = $args->cmid;
        $courseid = $args->courseid;
        $sessid = $args->sessid;

        // Capabilities check.
        $cm = get_coursemodule_from_id('attendance', $cmid);

        require_login($courseid, false , $cm, true, true);

        $context = \context_module::instance($cm->id);
        require_capability('mod/attendance:view', $context);

        $attendance    = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);
        $attforsession = $DB->get_record('attendance_sessions', array('id' => $sessid), '*', MUST_EXIST);
        $course        = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

        $pageparams = new \mod_attendance_sessions_page_params();
        $pageparams->sessionid = $sessid;
        $att = new \mod_attendance_structure($attendance, $cm, $course, $context, $pageparams);

        $data = array(); // Data to pass to renderer.
        $data['attendance'] = $attendance;
        $data['cmid'] = $cmid;
        $data['courseid'] = $courseid;
        $data['sessid'] = $sessid;
        $data['messages'] = array();
        $data['showmessage'] = false;
        $data['showstatuses'] = true;
        $data['showpassword'] = false;
        $data['statuses'] = array();
        $data['disabledduetotime'] = false;

        list($canmark, $reason) = attendance_can_student_mark($attforsession, false);
        // Check if subnet is set and if the user is in the allowed range.
        if (!$canmark) {
            $data['messages'][]['string'] = $reason; // Lang string to show as a message.
            $data['showstatuses'] = false; // Hide all statuses.
        } else if (!empty($attforsession->subnet) && !address_in_subnet(getremoteaddr(), $attforsession->subnet)) {
            $data['messages'][self::MESSAGE_SUBNET]['string'] = 'subnetwrong'; // Lang string to show as a message.
            $data['showstatuses'] = false; // Hide all statuses.
        } else if ($attforsession->autoassignstatus && empty($attforsession->studentpassword)) {
            // This shouldn't happen as the main function should handle this scenario.
            // Hide all status just in case the user manages to hit this page accidentally.
            $data['showstatuses'] = false; // Hide all statuses.
        } else {
            // Show user form for submitting a status.
            $statuses = $att->get_statuses();
            // Check if user has access to all statuses.
            foreach ($statuses as $status) {
                if ($status->studentavailability === '0') {
                    unset($statuses[$status->id]);
                    continue;
                }
                if (!empty($status->studentavailability) &&
                    time() > $attforsession->sessdate + ($status->studentavailability * 60)) {
                    unset($statuses[$status->id]);
                    continue;
                    $data['disabledduetotime'] = true;
                }
                $data['statuses'][] = array('stid' => $status->id, 'description' => $status->description);
            }
            if (empty($data['statuses'])) {
                $data['messages'][]['string'] = 'attendance_no_status';
                $data['showstatuses'] = false; // Hide all statuses.
            } else if (!empty($attforsession->studentpassword)) {
                $data['showpassword'] = true;
                if ($attforsession->autoassignstatus) {
                    // If this is an auto status - don't show the statuses, but show the form.
                    $data['statuses'] = array();
                }
            }
        }
        if (!empty($data['messages'])) {
            $data['showmessage'] = true;
        }

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template("mod_attendance/mobile_user_form_$versionname", $data),
                    'cache-view' => false
                ],
            ],
            'javascript' => '',
            'otherdata' => ''
        ];
    }

    /**
     * Returns the form to take attendance for the mobile app.
     *
     * @param  array $args Arguments from tool_mobile_get_content WS
     * @return array HTML, javascript and other data
     */
    public static function mobile_teacher_form($args) {
        global $OUTPUT, $DB, $CFG, $PAGE;

        require_once($CFG->dirroot.'/mod/attendance/locallib.php');

        $args = (object) $args;
        $versionname = $args->appversioncode >= 3950 ? 'latest' : 'ionic3';
        $cmid = $args->cmid;
        $courseid = $args->courseid;
        $sessid = $args->sessid;

        // Capabilities check.
        $cm = get_coursemodule_from_id('attendance', $cmid);

        require_login($courseid, false , $cm, true, true);

        $context = \context_module::instance($cm->id);
        require_capability('mod/attendance:takeattendances', $context);

        $attendance    = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);
        $course        = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

        $pageparams = new \mod_attendance_sessions_page_params();
        $pageparams->sessionid = $sessid;
        $att = new \mod_attendance_structure($attendance, $cm, $course, $context, $pageparams);

        $data = array(); // Data to pass to renderer.
        $data['attendance'] = $attendance;
        $data['cmid'] = $cmid;
        $data['courseid'] = $courseid;
        $data['sessid'] = $sessid;
        $data['messages'] = array();
        $data['showmessage'] = false;
        $data['statuses'] = array();
        $data['btnargs'] = ''; // Stores list of userid status args that should be added to form post.

        $statuses = $att->get_statuses();
        $otherdata = array();
        $existinglog = $DB->get_records('attendance_log',
            array('sessionid' => $sessid), '', 'studentid,statusid');
        foreach ($existinglog as $log) {
            if (!empty($log->statusid)) {
                $otherdata['status'.$log->studentid] = $log->statusid;
            }
        }

        foreach ($statuses as $status) {
            $data['statuses'][] = array('stid' => $status->id, 'acronym' => $status->acronym,
                'description' => $status->description);
        }

        $data['users'] = array();
        $data['selectall'] = '';
        $users = $att->get_users($att->get_session_info($sessid)->groupid, 0);
        foreach ($users as $user) {
            $userpicture = new \user_picture($user);
            $userpicture->size = 1; // Size f1.
            $profileimageurl = $userpicture->get_url($PAGE)->out(false);
            $data['users'][] = array('userid' => $user->id, 'fullname' => $user->fullname, 'profileimageurl' => $profileimageurl);
            // Generate args to use in submission button here.
            $data['btnargs'] .= ', status'. $user->id. ': CONTENT_OTHERDATA.status'. $user->id;
            // Really Hacky way to do a select-all. This really needs to be moved into a JS function within the app.
            $data['selectall'] .= "CONTENT_OTHERDATA.status".$user->id."=CONTENT_OTHERDATA.statusall;";
        }
        if (!empty($data['messages'])) {
            $data['showmessage'] = true;
        }

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template("mod_attendance/mobile_teacher_form_$versionname", $data),
                    'cache-view' => false
                ],
            ],
            'javascript' => '',
            'otherdata' => $otherdata
        ];
    }

}