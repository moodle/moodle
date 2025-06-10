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
 * Class plugin_coursemodules
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_coursemodules extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filtercoursemodules', 'block_configurable_reports');
        $this->reporttypes = ['courses', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filtercoursemodules_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @return array|mixed|string|string[]
     */
    public function execute($finalelements) {
        global $remotedb;

        $filtercoursemoduleid = optional_param('filter_coursemodules', 0, PARAM_INT);
        if (!$filtercoursemoduleid) {
            return $finalelements;
        }

        if ($this->report->type !== 'sql') {
            return [$filtercoursemoduleid];
        }

        if (preg_match("/%%FILTER_COURSEMODULEID:([^%]+)%%/i", $finalelements, $output)) {
            $replace = ' AND ' . $output[1] . ' = ' . $filtercoursemoduleid;
            $finalelements = str_replace('%%FILTER_COURSEMODULEID:' . $output[1] . '%%', $replace, $finalelements);
        }

        if (preg_match("/%%FILTER_COURSEMODULEFIELDS:([^%]+)%%/i", $finalelements, $output)) {
            $replace = ' ' . $output[1] . ' ';
            $finalelements = str_replace('%%FILTER_COURSEMODULEFIELDS:' . $output[1] . '%%', $replace, $finalelements);
        }

        if (preg_match("/%%FILTER_COURSEMODULE:([^%]+)%%/i", $finalelements, $output)) {
            $module = $remotedb->get_record('modules', ['id' => $filtercoursemoduleid]);
            $replace = ' JOIN {' . $module->name . '} AS m ON m.id = ' . $output[1] . ' ';
            $finalelements = str_replace('%%FILTER_COURSEMODULE:' . $output[1] . '%%', $replace, $finalelements);
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

        $reportclassname = 'report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        if ($this->report->type !== 'sql') {
            $components = cr_unserialize($this->report->components);
            $conditions = $components['conditions'];

            $coursemodulelist = $reportclass->elements_by_conditions($conditions);
        } else {
            $coursemodulelist = array_keys($remotedb->get_records('modules'));
        }

        $courseoptions = [];
        $courseoptions[0] = get_string('filter_all', 'block_configurable_reports');

        if (!empty($coursemodulelist)) {
            [$usql, $params] = $remotedb->get_in_or_equal($coursemodulelist);
            $coursemodules = $remotedb->get_records_select('modules', "id $usql", $params);

            foreach ($coursemodules as $c) {
                $courseoptions[$c->id] = format_string(get_string('pluginname', $c->name) . ' = ' . $c->name);
            }
        }

        $elestr = get_string('filtercoursemodules', 'block_configurable_reports');
        $mform->addElement('select', 'filter_coursemodules', $elestr, $courseoptions);
        $mform->setType('filter_coursemodules', PARAM_INT);
    }

}
