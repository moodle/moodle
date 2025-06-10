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
 * @author     François Parlant <https://www.linkedin.com/in/francois-parlant/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_competencytemplates - Display the courses in which the competencies of a template are used
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_competencytemplates extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filtercompetencytemplates', 'block_configurable_reports');
        $this->reporttypes = ['courses', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filtercompetencytemplates_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @return array|string|string[]
     */
    public function execute($finalelements) {

        $filtercompetencytemplates = optional_param('filter_competencytemplates', 0, PARAM_INT);
        if (!$filtercompetencytemplates) {
            return $finalelements;
        }

        if ($this->report->type !== 'sql') {
            return [$filtercompetencytemplates];
        }

        if (preg_match("/%%FILTER_COMPETENCYTEMPLATES:([^%]+)%%/i", $finalelements, $output)) {
            $replace = ' AND ' . $output[1] . ' = ' . $filtercompetencytemplates;

            return str_replace('%%FILTER_COMPETENCYTEMPLATES:' . $output[1] . '%%', $replace, $finalelements);
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

            $competencytemplateslist = $reportclass->elements_by_conditions($conditions);
        } else {
            $sql = 'SELECT  ct.id, ct.shortname
                      FROM {competency_template} ct
                      ';
            $studentlist = $remotedb->get_records_sql($sql);
            foreach ($studentlist as $student) {
                $competencytemplateslist[] = $student->userid;
            }
        }

        $competencytemplatesoptions = [];
        $competencytemplatesoptions[0] = get_string('filter_all', 'block_configurable_reports');

        if (!empty($competencytemplateslist)) {

            $competencytemplates = $remotedb->get_records_sql($sql);

            foreach ($competencytemplates as $c) {
                $competencytemplatesoptions[$c->id] = $c->shortname;
            }
        }

        $elestr = get_string('competencytemplates', 'block_configurable_reports');
        $mform->addElement('select', 'filter_competencytemplates', $elestr, $competencytemplatesoptions);
        $mform->setType('filter_competencytemplates', PARAM_INT);
    }

}
