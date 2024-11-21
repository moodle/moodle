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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/externallib.php");
require_once("{$CFG->dirroot}/local/intelliboard/locallib.php");

class local_intelliboard_account_setup extends external_api
{
    public static function execute_parameters() {
        return new external_function_parameters([
            'params' => new external_single_structure([
                'accounttype' => new external_value(PARAM_TEXT, 'Account Type', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_TEXT, 'Email Of Account', VALUE_OPTIONAL),
                'fullname' =>  new external_value(PARAM_TEXT, 'Account Name', VALUE_OPTIONAL),
                'oragnization' => new external_value(PARAM_TEXT, 'Account Organization', VALUE_OPTIONAL),
                'phone' => new external_value(PARAM_TEXT, 'Account Phone', VALUE_OPTIONAL),
                'region' => new external_value(PARAM_TEXT, 'Account Region', VALUE_OPTIONAL),
                'usertype' => new external_value(PARAM_RAW, 'Account Usertypes', VALUE_OPTIONAL),
            ])
        ]);
    }


    public static function execute($params)
    {
        global $DB, $CFG;
        if (!get_config("local_intelliboard", "account_setup")) {
            self::validate_parameters(self::execute_parameters(), ['params' => $params]);

            $teacher_roles = get_config('local_intelliboard', 'filter10');
            $learner_roles = get_config('local_intelliboard', 'filter11');

            list($sql1, $params1) = intelliboard_filter_in_sql($teacher_roles, "roleid");
            list($sql2, $params2) = intelliboard_filter_in_sql($learner_roles, "roleid");

            $data = [];
            $data['task'] = 'help';
            $data['courses'] = $DB->count_records("course", ["visible" => 1]);
            $data['instructors'] = $DB->count_records_sql("SELECT COUNT(*) FROM {role_assignments} WHERE id > 0 $sql1", $params1);
            $data['learners'] = $DB->count_records_sql("SELECT COUNT(*) FROM {role_assignments} WHERE id > 0 $sql2", $params2);
            $data['admins'] = json_encode(intelli_lms_admins());
            $data['lms_url'] = $CFG->wwwroot;
            $data['account'] = json_encode($params);

            $intelliboard = intelliboard($data, 'help');
            if (isset($intelliboard->status) && $intelliboard->status == 'ok') {
                set_config("account_setup", true, "local_intelliboard");
                return ['result' => 'success'];
            }
        }
        return ['result' => 'false'];
    }


    public static function execute_returns()
    {
        return new external_single_structure([
            'result' => new external_value(PARAM_TEXT, 'Return result')
        ]);
    }
}
