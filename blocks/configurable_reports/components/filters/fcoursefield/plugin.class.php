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
 * Class plugin_fcoursefield
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_fcoursefield extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = true;
        $this->unique = true;
        $this->fullname = get_string('fcoursefield', 'block_configurable_reports');
        $this->reporttypes = ['courses'];
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
     * @return int[]|mixed|string[]
     */
    public function execute($finalelements, $data) {
        global $remotedb;
        $filterfcoursefield = optional_param('filter_fcoursefield_' . $data->field, 0, PARAM_RAW);
        if ($filterfcoursefield) {
            // Function addslashes is done in clean param.
            $filter = clean_param(base64_decode($filterfcoursefield), PARAM_TEXT);
            [$usql, $params] = $remotedb->get_in_or_equal($finalelements);
            $sql = "$data->field = ? AND id $usql";
            $params = array_merge([$filter], $params);
            if ($elements = $remotedb->get_records_select('course', $sql, $params)) {
                $finalelements = array_keys($elements);
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

        $columns = $remotedb->get_columns('course');
        $filteroptions = [];
        $filteroptions[''] = get_string('filter_all', 'block_configurable_reports');

        $coursecolumns = [];
        foreach ($columns as $c) {
            $coursecolumns[$c->name] = $c->name;
        }

        if (!isset($coursecolumns[$formdata->field])) {
            throw new moodle_exception('nosuchcolumn');
        }

        $reportclassname = 'report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        $components = cr_unserialize($this->report->components);
        $conditions = '';
        if (array_key_exists('conditions', $components)) {
            $conditions = $components['conditions'];
        }
        $courselist = $reportclass->elements_by_conditions($conditions);

        if (!empty($courselist)) {
            $sql = 'SELECT DISTINCT(' . $formdata->field . ') as ufield FROM {course} WHERE ' . $formdata->field .
                " <> '' ORDER BY ufield ASC";
            if ($rs = $remotedb->get_recordset_sql($sql, null)) {
                foreach ($rs as $u) {
                    $filteroptions[base64_encode($u->ufield)] = $u->ufield;
                }
                $rs->close();
            }
        }

        $mform->addElement('select', 'filter_fcoursefield_' . $formdata->field, get_string($formdata->field), $filteroptions);
        $mform->setType('filter_courses', PARAM_BASE64);
    }

}
