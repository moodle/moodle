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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_cuserfield
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_cuserfield extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('cuserfield', 'block_configurable_reports');
        $this->reporttypes = ['users'];
        $this->form = true;
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        global $DB;

        if (strpos($data->field, 'profile_') === 0) {
            $name = $DB->get_field('user_info_field', 'name', ['shortname' => str_replace('profile_', '', $data->field)]);

            return $name . ' ' . $data->operator . ' ' . $data->value;
        }

        return get_string($data->field) . ' ' . $data->operator . ' ' . $data->value;

    }

    /**
     * Execute
     *
     * @param object $data
     * @param object $user
     * @return array|int[]|string[]
     */
    public function execute($data, $user) {
        global $DB;
        // Data -> Plugin configuration data.
        // TODO - Use $DB->sql_like().
        $ilike = " LIKE ";

        if (strpos($data->field, 'profile_') === 0) {

            if ($data->value == "%%CURRENTUSER%%") {
                $pfname = str_replace('profile_', '', $data->field);
                $data->value = $user->profile[$pfname];
            }

            if ($fieldid = $DB->get_field('user_info_field', 'id', ['shortname' => str_replace('profile_', '', $data->field)])) {
                switch ($data->operator) {
                    case 'LIKE % %':
                        $sql = "fieldid = $fieldid AND data $ilike ?";
                        $params = ["%$data->value%"];
                        break;
                    default:
                        $sql = "fieldid = $fieldid AND data $data->operator ?";
                        $params = [$data->value];
                }

                if ($infodata = $DB->get_records_select('user_info_data', $sql, $params)) {
                    $finalusersid = [];
                    foreach ($infodata as $d) {
                        $finalusersid[] = $d->userid;
                    }

                    return $finalusersid;
                }
            }
        } else {
            if ($data->value === "%%CURRENTUSER%%") {
                $data->value = $user->{$data->field};
            }

            switch ($data->operator) {
                case 'LIKE % %':
                    $sql = "$data->field $ilike ?";
                    $params = ["%$data->value%"];
                    break;
                default:
                    $sql = "$data->field $data->operator ?";
                    $params = [$data->value];
            }

            $users = $DB->get_records_select('user', $sql, $params);
            if ($users) {
                return array_keys($users);
            }
        }

        return [];
    }

}
