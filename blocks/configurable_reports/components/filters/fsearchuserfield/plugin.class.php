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
 * Configurable Reports A Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_fsearchuserfield
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_fsearchuserfield extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = true;
        $this->unique = true;
        $this->fullname = get_string('fsearchuserfield', 'block_configurable_reports');
        $this->reporttypes = ['users', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return $data->field;
    }

    /**
     * execute
     *
     * @param string|array $finalelements
     * @param object $data
     * @return array|int[]|mixed|string|string[]
     */
    public function execute($finalelements, $data) {
        if ($this->report->type === 'sql') {
            return $this->execute_sql($finalelements, $data);
        }

        return $this->execute_users($finalelements, $data);
    }

    /**
     * execute_sql
     *
     * @param string $finalelements
     * @param object $data
     * @return array|string|string[]
     */
    private function execute_sql($finalelements, object $data) {
        $filterfuserfield = optional_param('filter_fuserfield_' . $data->field, 0, PARAM_RAW);
        $filter = clean_param(base64_decode($filterfuserfield), PARAM_TEXT);

        if ($filterfuserfield && preg_match("/%%FILTER_USERS:([^%]+)%%/i", $finalelements, $output)) {
            $replace = ' AND ' . $output[1] . ' LIKE ' . "'%$filter%'";

            return str_replace('%%FILTER_USERS:' . $output[1] . '%%', $replace, $finalelements);
        }

        return $finalelements;
    }

    /**
     * execute_users
     *
     * @param array $finalelements
     * @param object $data
     * @return array|int[]|mixed|string[]
     */
    private function execute_users(array $finalelements, object $data): array {
        global $remotedb;

        $filterfuserfield = optional_param('filter_fuserfield_' . $data->field, 0, PARAM_RAW);

        if ($filterfuserfield) {
            // Function addslashes is done in clean param.
            $filter = clean_param(base64_decode($filterfuserfield), PARAM_TEXT);

            if (strpos($data->field, 'profile_') === 0) {
                $conditions = ['shortname' => str_replace('profile_', '', $data->field)];
                if ($fieldid = $remotedb->get_field('user_info_field', 'id', $conditions)) {
                    [$usql, $params] = $remotedb->get_in_or_equal($finalelements);
                    $sql = "fieldid = ? AND data LIKE ? AND userid $usql";
                    $params = array_merge([$fieldid, "%$filter%"], $params);

                    if ($infodata = $remotedb->get_records_select('user_info_data', $sql, $params)) {
                        $finalusersid = [];
                        foreach ($infodata as $d) {
                            $finalusersid[] = $d->userid;
                        }

                        return $finalusersid;
                    }
                }

            } else {

                [$usql, $params] = $remotedb->get_in_or_equal($finalelements);
                $sql = "$data->field LIKE ? AND id $usql";
                $params = array_merge(["%$filter%"], $params);
                if ($elements = $remotedb->get_records_select('user', $sql, $params)) {
                    $finalelements = array_keys($elements);
                }
            }
        }

        return $finalelements;
    }

    /**
     * Print filter
     *
     * @param MoodleQuickForm $mform
     * @param bool|object $formdata
     * @return void
     */
    public function print_filter(MoodleQuickForm $mform, $formdata = false): void {
        global $remotedb;

        $columns = $remotedb->get_columns('user');
        $filteroptions = [];
        $filteroptions[''] = get_string('filter_all', 'block_configurable_reports');

        $usercolumns = [];
        foreach ($columns as $c) {
            $usercolumns[$c->name] = $c->name;
        }

        if ($profile = $remotedb->get_records('user_info_field')) {
            foreach ($profile as $p) {
                $usercolumns['profile_' . $p->shortname] = $p->name;
            }
        }

        if (!isset($usercolumns[$formdata->field])) {
            throw new moodle_exception('nosuchcolumn');
        }

        $reportclassname = 'report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        if ($this->report->type === 'sql') {
            $userlist = array_keys($remotedb->get_records('user'));
        } else {
            $components = cr_unserialize($this->report->components);
            $conditions = $components['conditions'] ?? null;
            $userlist = $reportclass->elements_by_conditions($conditions);
        }

        if (!empty($userlist)) {

            if (strpos($formdata->field, 'profile_') === 0) {
                $conditions = ['shortname' => str_replace('profile_', '', $formdata->field)];
                if ($field = $remotedb->get_record('user_info_field', $conditions)) {
                    $selectname = $field->name;

                    [$usql, $params] = $remotedb->get_in_or_equal($userlist);
                    $sql = "SELECT DISTINCT(data) as data FROM {user_info_data} WHERE fieldid = ? AND userid $usql";
                    $params = array_merge([$field->id], $params);

                    if ($infodata = $remotedb->get_records_sql($sql, $params)) {
                        foreach ($infodata as $d) {
                            $filteroptions[base64_encode($d->data)] = $d->data;
                        }
                    }
                }

            } else {
                $selectname = get_string($formdata->field);

                [$usql, $params] = $remotedb->get_in_or_equal($userlist);
                $columns = $remotedb->get_columns('user');

                if (!array_key_exists($formdata->field, $columns)) {
                    throw new moodle_exception('nosuchcolumn', 'error', '', null, "The column '{$formdata->field}' does not exist in the user table.");
                }

                $sql = "SELECT DISTINCT(" . $formdata->field . ") as ufield FROM {user} WHERE id $usql ORDER BY ufield ASC";
                if ($rs = $remotedb->get_recordset_sql($sql, $params)) {
                    foreach ($rs as $u) {
                        $filteroptions[base64_encode($u->ufield)] = $u->ufield;
                    }
                    $rs->close();
                }
            }
        }

        $mform->addElement('select', 'filter_fuserfield_' . $formdata->field, $selectname, $filteroptions);
        $mform->setType('filter_fuserfield_' . $formdata->field, PARAM_INT);
    }

}
