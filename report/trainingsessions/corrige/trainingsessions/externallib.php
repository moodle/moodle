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
 * @package     report_trainingsessions
 * @category    report
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * this file provides with WS document requests to externalize report documents
 * from within an external management system
 */
defined('MOODLE_INTERNAL') || die;

class report_trainingsessions_external {

    public static function validate_report_parameters($configparamdefs, $inputs) {

        $status = self::validate_parameters($configparamdefs, $inputs);

        if (!in_array($inputs['reportformat'], array('csv', 'xls', 'pdf', 'json'))) {
            throw new invalid_parameter_exception('Unsupported output document format');;
        }

        if (!in_array($inputs['reportscope'], array('allcourses', 'currentcourse'))) {
            throw new invalid_parameter_exception('Unsupported report scope');;
        }

        $reportlayouts = array('onefulluserpersheet', 'onefulluserperfile', 'oneuserperrow', 'alluserssessionsinglefile', 'sessions');
        if (!in_array($input['reportlayout'], $reportlayouts)) {
            throw new invalid_parameter_exception('Unsupported report scope');;
        }

        switch ($inputs['courseidsource']) {
            case 'id': {
                if (!$DB->record_exists('course', array('id' => $inputs['courseid']))) {
                    throw new invalid_parameter_exception('Course not found by id: '.$inputs['courseid']);
                }
                break;
            }

            case 'shortname': {
                if (!$course = $DB->get_record('course', array('shortname' => $inputs['courseid']))) {
                    throw new invalid_parameter_exception('Course not found by shortname: '.$inputs['courseid']);
                }
                $status['courseid'] = $course->id;
                break;
            }

            case 'idnumber': {
                if (!$course = $DB->get_record('course', array('idnumber' => $inputs['courseid']))) {
                    throw new invalid_parameter_exception('Course not found by idnumber: '.$inputs['courseid']);
                }
                $status['courseid'] = $course->id;
                break;
            }
        }

        switch ($inputs['groupidsource']) {
            case 'id': {
                if (!$DB->record_exists('groups', array('id' => $inputs['groupid']))) {
                    throw new invalid_parameter_exception('Group not found by id: '.$inputs['groupid']);
                }
                break;
            }

            case 'name': {
                if (!$group = $DB->get_record('groups', array('name' => $inputs['groupid']))) {
                    throw new invalid_parameter_exception('Group not found by name: '.$inputs['groupid']);
                }
                $status['groupid'] = $group->id;
                break;
            }
        }

        switch ($inputs['useridsource']) {
            case 'id': {
                if (!$DB->record_exists('user', array('id' => $inputs['userid']))) {
                    throw new invalid_parameter_exception('User not found by id: '.$inputs['userid']);
                }
                break;
            }

            case 'username': {
                if (preg_match('/^(.*)§(.*)$/', $inputs['userid'])) {
                    list($username, $hostroot) = explode('§', $inputs['userid']);
                    $hostid = $DB->get_field('mnet_host', 'id', array('wwwroot' => $hostroot));
                } else {
                    $hostid = $CFG->mnet_localhost_id;
                }
                if (!$user = $DB->get_record('user', array('username' => $inputs['userid'], 'mnethostid' => $hostid))) {
                    throw new invalid_parameter_exception('User not found by username: '.$inputs['userid']);
                }
                $status['userid'] = $user->id;
                break;
            }

            case 'idnumber': {
                if (!$user = $DB->get_record('user', array('idnumber' => $inputs['userid']))) {
                    throw new invalid_parameter_exception('User not found by idnumber: '.$inputs['userid']);
                }
                $status['userid'] = $user->id;
                break;
            }

            case 'email': {
                if (!$user = $DB->get_record('user', array('email' => $inputs['userid']))) {
                    throw new invalid_parameter_exception('User not found by email: '.$inputs['userid']);
                }
                $status['userid'] = $user->id;
                break;
            }
        }

        return $status;
    }

    public static function get_report_url_parameters() {

        return new external_function_parameters (
            array(
                '$reportlayout' => new external_value(
                        PARAM_ALPHA,
                        'Report layout'),
                'reportscope' => new external_value(
                        PARAM_ALPHA,
                        'scope of data scanned for report'),
                'reportformat' => new external_value(
                        PARAM_ALPHA,
                        'document content format'),
                'from' => new external_value(
                        PARAM_INT,
                        'period start timestamp'),
                'to' => new external_value(
                        PARAM_INT,
                        'period end timestamp'),
                'courseidsource' => new external_value(
                        PARAM_ALPHA,
                        'The source for course id. Can be "id", "idnumber", "shortname"'),
                'courseid' => new external_value(
                        PARAM_TEXT,
                        'course target restriction (for group ranged reports)'),
                'groupidsource' => new external_value(
                        PARAM_ALPHA,
                        'The source for group id. Can be "id" or "name"'),
                'groupid' => new external_value(
                        PARAM_TEXT,
                        'group target restriction (for group ranged reports)'),
                'useridsource' => new external_value(
                        PARAM_ALPHA,
                        'The source for user id. Can be "id", "username", "idnumber" or "email"'),
                'userid' => new external_value(
                        PARAM_TEXT,
                        'user targetting restriction (for user range reports)'),
            )
        );
    }

    public function get_report_url($reportlayout, $reportscope, $reportformat, $from, $to, $courseidsource, $courseid = 0,
                               $groupidsource = '', $groupid = 0, $useridsource = '', $userid = 0) {
        global $CFG;

        // Ensure report format is always lowercase.
        $reportformat = strtolower($reportformat);

        // Validate parameters.
        $parameters = array('reportlayout' => $reportlayout,
            'reportscope' => $reportscope,
            'reportformat' => $reportformat,
            'from' => $from,
            'to' => $to,
            'courseidsource' => $courseidsource,
            'courseid' => $courseid,
            'groupidsource' => $groupidsource,
            'groupid' => $groupid,
            'useridsource' => $useridsource,
            'userid' => $userid);
        $params = self::validate_report_parameters(self::get_report_url_parameters(), $parameters);

        if (report_trainingsessions_supports_feature('export/ws')) {
            include_once($CFG->dirroot.'/report/trainingsessions/pro/locallib.php');
            return report_trainingsessions_export_report($reportlayout, $reportscope, $reportformat, $from, $to,
                                                         $params['courseid'], $params['groupid'], $params['userid']);
        } else {
            throw new moodle_exception('Feature not supported in this release.');
        }
    }

    public static function get_report_url_returns() {
        return new external_value(PARAM_URL, 'An Url to the file');
    }
}