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
 * @package     block_use_stats
 * @category    blocks
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * this file provides with WS document requests to externalize report documents
 * from within an external management system
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');

class block_use_stats_external extends external_api {

    public static function get_user_stats_parameters() {

        return new external_function_parameters (
            array(
                'uidsource' => new external_value(PARAM_ALPHA, 'Source for user id '),
                'uid' => new external_value(PARAM_TEXT, 'User identifier'),
                'cidsource' => new external_value(PARAM_ALPHA, 'course id', VALUE_DEFAULT, 'idnumber', true),
                'cid' => new external_value(PARAM_TEXT, 'Course identifier', VALUE_DEFAULT, 0),
                'from' => new external_value(PARAM_INT, 'period start timestamp', VALUE_DEFAULT, 0, true),
                'to' => new external_value(PARAM_INT, 'period end timestamp', VALUE_DEFAULT, 0, true),
                'score' => new external_value(PARAM_INT, 'Get course score back', VALUE_DEFAULT, 1, true),
            )
        );
    }

    public static function get_user_stats($uidsource, $uid, $cidsource, $cid, $from, $to, $score = 0) {
        global $DB;

        $parameters = array(
            'uidsource' => $uidsource,
            'uid' => $uid
        );
        $user = self::validate_user_parameters($parameters);

        $parameters = array(
            'cidsource' => $cidsource,
            'cid' => $cid
        );
        $course = self::validate_course_parameters($parameters);

        if (empty($to)) {
            $to = time();
        }

        if (!empty($course)) {
            $logs = use_stats_extract_logs($from, $to, $user->id, $course);
        } else {
            $logs = use_stats_extract_logs($from, $to, $user->id);
        }

        $userres = new StdClass;
        $userres->id = $user->id;
        $userres->username = $user->username;
        $userres->idnumber = $user->idnumber;

        $queryres = new StdClass;
        $queryres->from = $from;
        $queryres->to = $to;

        $result = new StdClass;
        $result->user = $userres;
        $result->query = $queryres;
        $result->sessions = new StdClass;
        $result->courses = array();

        if (empty($logs)) {
            return $result;
        }

        $aggregate = use_stats_aggregate_logs($logs, 'module', 0, $from, $to);

        if (array_key_exists('course', $aggregate)) {

            // Scan sessions for stats.
            $maxsession = 0;
            $minsession = 1000000;
            $sum = 0;
            $count = 0;
            foreach ($aggregate['sessions'] as $s) {

                if ($s->elapsed > $maxsession) {
                    $maxsession = $s->elapsed;
                }
                if ($s->elapsed < $minsession) {
                    $minsession = $s->elapsed;
                }
                $sum += $s->elapsed;
                $count++;
            }

            $meansession = ($count) ? round($sum / $count) : 0;

            $sessionres = new Stdclass;
            $sessionres->sessions = count($aggregate['sessions']);
            $firstsession = array_shift($aggregate['sessions']);
            $sessionres->firstsession = $firstsession->sessionstart;
            $lastsession = array_pop($aggregate['sessions']);
            $sessionres->lastsession = $lastsession->sessionstart;
            $sessionres->sessionmax = $maxsession;
            $sessionres->sessionmin = $minsession;
            $sessionres->meansession = $meansession;
            $result->sessions = $sessionres;

            foreach (array_keys($aggregate['course']) as $courseid) {
                $rescourse = $DB->get_record('course', array('id' => $courseid));
                if ($courseid == 0 || $courseid == SITEID) {
                    continue;
                }

                $courseres = new StdClass;
                $courseres->id = $rescourse->id;
                $courseres->shortname = $rescourse->shortname;
                $courseres->idnumber = $rescourse->idnumber;
                $courseres->fullname = format_string($rescourse->fullname);

                $courseres->activitytime = $aggregate['activities'][$courseid]->elapsed;
                $courseres->coursetime = $aggregate['course'][$courseid]->elapsed;
                $courseres->coursetotal = $aggregate['coursetotal'][$courseid]->elapsed;
                $courseres->othertime = 0 + @$aggregate['other']->elapsed;
                $courseres->sitecoursetime = 0 + @$aggregate['course'][SITEID]->elapsed;

                if ($score) {
                    $gradeitem = $DB->get_record('grade_items', array('itemtype' => 'course', 'courseid' => $courseid));
                    $grade = $DB->get_record('grade_grades', array('itemid' => $gradeitem->id, 'userid' => $user->id));
                    if ($grade) {
                        $courseres->score = $grade->rawgrade;
                    } else {
                        $courseres->score = '-';
                    }
                }

                $result->courses[] = $courseres;
            }
        }

        return $result;
    }

    public static function get_user_stats_returns() {
        return new external_single_structure(
            array(
                'user' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'User id'),
                        'idnumber' => new external_value(PARAM_TEXT, 'User idnumber'),
                        'username' => new external_value(PARAM_TEXT, 'User username'),
                    )
                ),

                'query' => new external_single_structure(
                    array(
                        'from' => new external_value(PARAM_INT, 'From date'),
                        'to' => new external_value(PARAM_INT, 'To date'),
                    )
                ),

                'sessions' => new external_single_structure(
                    array(
                        'sessions' => new external_value(PARAM_INT, 'Number of sessions', VALUE_OPTIONAL, 0, true),
                        'firstsession' => new external_value(PARAM_INT, 'First session date', VALUE_OPTIONAL, 0, true),
                        'lastsession' => new external_value(PARAM_INT, 'Last session date', VALUE_OPTIONAL, 0, true),
                        'sessionmin' => new external_value(PARAM_INT, 'Min session duration', VALUE_OPTIONAL, 0, true),
                        'sessionmax' => new external_value(PARAM_INT, 'Max session duration', VALUE_OPTIONAL, 0, true),
                        'meansession' => new external_value(PARAM_INT, 'Mean session duration', VALUE_OPTIONAL, 0, true),
                    )
                ),

                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Course id'),
                            'idnumber' => new external_value(PARAM_TEXT, 'Course idnumber'),
                            'shortname' => new external_value(PARAM_TEXT, 'Course shortname'),
                            'fullname' => new external_value(PARAM_TEXT, 'Course fullname'),
                            'activitytime' => new external_value(PARAM_INT, 'Elapsed time in activities'),
                            'coursetime' => new external_value(PARAM_INT, 'Elapsed time in course outside activities'),
                            'coursetotal' => new external_value(PARAM_INT, 'Elapsed time in course (all times)'),
                            'othertime' => new external_value(PARAM_INT, 'Elapsed time in system areas'),
                            'sitecoursetime' => new external_value(PARAM_INT, 'Elapsed time in site course during session'),
                            'score' => new external_value(PARAM_TEXT, 'Final course grade', VALUE_OPTIONAL),
                       )
                    )
                ),
            )
        );
    }

    /* *************************** Bulk data ************************* */

    public static function get_users_stats_parameters() {

        $statsfields = 'elapsed,events,courseelapsed,courseevents,otherelapsed,otherevents';

        return new external_function_parameters (
            array(
                'uidsource' => new external_value(PARAM_ALPHA, 'The source for user identifier'),
                'uids' => new external_multiple_structure(
                     new external_value(PARAM_TEXT, 'an uid')
                 ),
                'cidsource' => new external_value(PARAM_ALPHA, 'course id', VALUE_DEFAULT, 'idnumber', true),
                'cid' => new external_value(PARAM_TEXT, 'Course identifier', VALUE_DEFAULT, 0),
                'from' => new external_value(PARAM_INT, 'period start timestamp', VALUE_DEFAULT, 0, true),
                'to' => new external_value(PARAM_INT, 'period end timestamp', VALUE_DEFAULT, 0, true),
                'score' => new external_value(PARAM_BOOL, 'Get course score bask', VALUE_DEFAULT, true, true),
            )
        );
    }

    public static function get_users_stats($uidsource, $uids ,$cidsource, $cid, $from, $to, $score) {

        $parameters = array(
            'cidsource' => $cidsource,
            'cid' => $cid
        );
        $course = self::validate_course_parameters($parameters);

        $result = array();

        foreach ($uids as $uid) {
            $parameters = array(
                'uidsource' => $uidsource,
                'uid' => $uid
            );
            $user = self::validate_user_parameters($parameters);

            $result[] = self::get_user_stats($uidsource, $uid, $cidsource, $cid, $from, $to, $score);
        }

        return $result;
    }

    public static function get_users_stats_returns() {
        return new external_multiple_structure(
            self::get_user_stats_returns()
        );
    }

    /* *************************** Common functions ************************ */

    protected static function validate_course_parameters($parameters) {
        global $DB;

        if (!in_array($parameters['cidsource'], array('', 'id', 'idnumber', 'shortname'))) {
            throw invalid_parameter_exception('course source not in expected range');
        }

        switch ($parameters['cidsource']) {
            case '':
                return null;
                break;

            case 'id':
                $course = $DB->get_record('course', array('id' => $parameters['cid']));
                if (!$course) {
                    throw new invalid_parameter_exception('Invalid course by id '.$parameters['cid']);
                }
                break;

            case 'idnumber':
                $course = $DB->get_record('course', array('idnumber' => $parameters['cid']));
                if (!$course) {
                    throw new invalid_parameter_exception('Invalid course by idnumber '.$parameters['cid']);
                }
                break;

            case 'shortname':
                $course = $DB->get_record('course', array('shortname' => $parameters['cid']));
                if (!$course) {
                    throw new invalid_parameter_exception('Invalid course by idnumber '.$parameters['cid']);
                }
        }

        return $course;
    }

    protected static function validate_user_parameters($parameters) {
        global $DB;

        if (!in_array($parameters['uidsource'], array('', 'id', 'username', 'idnumber', 'email'))) {
            throw invalid_parameter_exception('user source not in expected range');
        }

        switch ($parameters['uidsource']) {
            case '':
                return null;
                break;

            case 'id':
                $user = $DB->get_record('user', array('id' => $parameters['uid']));
                if (!$user) {
                    throw new invalid_parameter_exception('Invalid user by id '.$parameters['uid']);
                }
                break;

            case 'idnumber':
                $user = $DB->get_record('user', array('idnumber' => $parameters['uid']));
                if (!$user) {
                    throw new invalid_parameter_exception('Invalid user by idnumber '.$parameters['uid']);
                }
                break;

            case 'username':
                $user = $DB->get_record('user', array('username' => $parameters['uid']));
                if (!$user) {
                    throw new invalid_parameter_exception('Invalid user by username '.$parameters['uid']);
                }
                break;

            case 'email':
                $user = $DB->get_record('user', array('email' => $parameters['uid']));
                if (!$user) {
                    throw new invalid_parameter_exception('Invalid user by username '.$parameters['uid']);
                }
        }

        return $user;
    }
}