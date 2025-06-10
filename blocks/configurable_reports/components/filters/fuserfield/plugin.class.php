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
 * Class plugin_fuserfield
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_fuserfield extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = true;
        $this->unique = false;
        $this->fullname = get_string('fuserfield', 'block_configurable_reports');
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
     * Execute
     *
     * @param string $finalelements
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
     * @return array|mixed|string|string[]
     */
    private function execute_sql($finalelements, $data) {
        $filterfuserfield = optional_param('filter_fuserfield_' . $data->field, 0, PARAM_BASE64);
        $filter = base64_decode($filterfuserfield);

        if ($filterfuserfield) {
            // For backwards compatibility with existing reports.
            $filtermatch = "FILTER_USERS";
            $finalelements = $this->sql_replace($filter, $filtermatch, $finalelements);

            $filtermatch = "FILTER_USERS_{$data->field}";
            $finalelements = $this->sql_replace($filter, $filtermatch, $finalelements);
        }

        return $finalelements;
    }

    /**
     * execute_users
     *
     * @param string $finalelements
     * @param object $data
     * @return array|int[]|mixed|string[]
     */
    private function execute_users($finalelements, $data) {
        global $remotedb;

        $filterfuserfield = optional_param('filter_fuserfield_' . $data->field, 0, PARAM_BASE64);
        if ($filterfuserfield) {
            $filter = base64_decode($filterfuserfield);

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
            $conditions = [];
            if ($formdata->excludedeletedusers) {
                $conditions['deleted'] = 0;
            }
            $userlist = array_keys($remotedb->get_records('user', $conditions));
        } else {
            $components = cr_unserialize($this->report->components);
            $conditions = $components['conditions'] ?? null;
            $userlist = $reportclass->elements_by_conditions($conditions);
        }
        if (!empty($userlist)) {
            if (strpos($formdata->field, 'profile_') === 0) {
                $conditions = ['shortname' => str_replace('profile_', '', $formdata->field)];
                if ($field = $remotedb->get_record('user_info_field', $conditions)) {
                    $selectname = format_string($field->name);
                    [$usql, $params] = $remotedb->get_in_or_equal($userlist);
                    $sql = "SELECT DISTINCT(data) as data FROM {user_info_data} WHERE fieldid = ? AND userid $usql";
                    $params = array_merge([$field->id], $params);

                    if ($infodata = $remotedb->get_records_sql($sql, $params)) {
                        $finalusersid = [];
                        foreach ($infodata as $d) {
                            $filteroptions[base64_encode(format_string($d->data))] = format_string($d->data);
                        }
                    }
                }
            } else {
                $selectname = get_string($formdata->field);

                [$usql, $params] = $remotedb->get_in_or_equal($userlist);
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
        $mform->setType('filter_fuserfield_' . $formdata->field, PARAM_BASE64);
    }

    /**
     * sql_replace
     *
     * @param string $filtersearchtext
     * @param string $filterstrmatch
     * @param string $finalelements
     * @return array|mixed|string|string[]
     */
    private function sql_replace(string $filtersearchtext, $filterstrmatch, $finalelements) {
        $operators = ['=', '<', '>', '<=', '>=', '~', 'in'];

        // TODO this function is 2 times in the code, should be refactored.

        if (preg_match("/%%$filterstrmatch:([^%]+)%%/i", $finalelements, $output)) {
            [$field, $operator] = preg_split('/:/', $output[1]);
            if (empty($operator)) {
                $operator = '~';
            } else if (!in_array($operator, $operators)) {
                throw new moodle_exception('nosuchoperator');
            }
            if ($operator === '~') {
                // TODO can be improved by more native PDO approach.
                $searchitem = trim(str_replace("'", "''", $filtersearchtext));
                $replace = " AND " . $field . " LIKE '%" . $searchitem . "%'";
            } else if ($operator === 'in') {
                $processeditems = [];

                // TODO can be improved by more native PDO approach.
                // Accept comma-separated values, allowing for '\,' as a literal comma.
                foreach (preg_split("/(?<!\\\\),/", $filtersearchtext) as $searchitem) {
                    // Strip leading/trailing whitespace and quotes (we'll add our own quotes later).
                    $searchitem = trim($searchitem);
                    $searchitem = trim($searchitem, '"\'');

                    // We can also safely remove escaped commas now.
                    $searchitem = str_replace('\\,', ',', $searchitem);

                    // Escape and quote strings...
                    if (!is_numeric($searchitem)) {
                        $searchitem = "'" . addslashes($searchitem) . "'";
                    }
                    $processeditems[] = "$field like $searchitem";
                }
                // Despite the name, by not actually using in() we can support wildcards, and maybe be more portable as well.
                $replace = " AND (" . implode(" OR ", $processeditems) . ")";
            } else {
                $replace = ' AND ' . $field . ' ' . $operator . ' ' . $filtersearchtext;
            }
            $finalelements = str_replace('%%' . $filterstrmatch . ':' . $output[1] . '%%', $replace, $finalelements);
        }

        return $finalelements;
    }

}
