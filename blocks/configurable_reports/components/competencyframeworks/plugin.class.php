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
 * COMPETENCY FRAMEWORK FILTER A filter for configurable reports
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
 * Class plugin_competencyframeworks example https://gist.github.com/luukverhoeven/a50c614ef19e7bd4e0d824c12e1a09af.
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_competencyframeworks extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filtercompetencyframeworks', 'block_configurable_reports');
        $this->reporttypes = ['courses', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filtercompetencyframeworks_summary', 'block_configurable_reports');
    }

    /**
     * execute
     *
     * @param string $finalelements
     * @return array|string|string[]
     */
    public function execute($finalelements) {

        $filtercompetencyframeworks = optional_param('filter_competencyframeworks', 0, PARAM_INT);
        if (!$filtercompetencyframeworks) {
            return $finalelements;
        }

        if ($this->report->type !== 'sql') {
            return [$filtercompetencyframeworks];
        }

        if (preg_match("/%%FILTER_COMPETENCYFRAMEWORKS:([^%]+)%%/i", $finalelements, $output)) {
            $replace = ' AND ' . $output[1] . ' = ' . $filtercompetencyframeworks;

            return str_replace('%%FILTER_COMPETENCYFRAMEWORKS:' . $output[1] . '%%', $replace, $finalelements);
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

            $competencyframeworkslist = $reportclass->elements_by_conditions($conditions);
        } else {
            $sql = 'SELECT  cf.id, cf.shortname
                      FROM {competency_framework} cf
                      ';
            $studentlist = $remotedb->get_records_sql($sql);
            foreach ($studentlist as $student) {
                $competencyframeworkslist[] = $student->userid;
            }

        }

        $competencyframeworksoptions = [];
        $competencyframeworksoptions[0] = get_string('filter_all', 'block_configurable_reports');

        if (!empty($competencyframeworkslist)) {

            $competencyframeworks = $remotedb->get_records_sql($sql);

            foreach ($competencyframeworks as $c) {
                $competencyframeworksoptions[$c->id] = $c->shortname;
            }
        }

        $elestr = get_string('competencyframeworks', 'block_configurable_reports');
        $mform->addElement('select', 'filter_competencyframeworks', $elestr, $competencyframeworksoptions);
        $mform->setType('filter_competencyframeworks', PARAM_INT);
    }

}
