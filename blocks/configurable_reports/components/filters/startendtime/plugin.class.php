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
 * Class plugin_startendtime
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_startendtime extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('startendtime', 'block_configurable_reports');
        $this->reporttypes = ['sql', 'timeline', 'users', 'courses'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filterstartendtime_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @return array|string|string[]
     */
    public function execute($finalelements) {
        global $CFG;

        if ($this->report->type !== 'sql') {
            return $finalelements;
        }

        if ($CFG->version < 2011120100) {
            $filterstarttime = optional_param('filter_starttime', 0, PARAM_RAW);
            $filterendtime = optional_param('filter_endtime', 0, PARAM_RAW);
        } else {
            $filterstarttime = optional_param_array('filter_starttime', 0, PARAM_RAW);
            $filterendtime = optional_param_array('filter_endtime', 0, PARAM_RAW);
        }

        if (!$filterstarttime || !$filterendtime) {
            return $finalelements;
        }

        $filterstarttime = make_timestamp(
            $filterstarttime['year'],
            $filterstarttime['month'],
            $filterstarttime['day'],
            $filterstarttime['hour'],
            $filterstarttime['minute']
        );
        $filterendtime = make_timestamp(
            $filterendtime['year'],
            $filterendtime['month'],
            $filterendtime['day'],
            $filterendtime['hour'],
            $filterendtime['minute']
        );

        $operators = ['<', '>', '<=', '>='];

        if (preg_match("/%%FILTER_STARTTIME:([^%]+)%%/i", $finalelements, $output)) {
            [$field, $operator] = preg_split('/:/', $output[1]);
            if (!in_array($operator, $operators)) {
                throw new moodle_exception('nosuchoperator');
            }
            $replace = ' AND ' . $field . ' ' . $operator . ' ' . $filterstarttime;
            $finalelements = str_replace('%%FILTER_STARTTIME:' . $output[1] . '%%', $replace, $finalelements);
        }

        if (preg_match("/%%FILTER_ENDTIME:([^%]+)%%/i", $finalelements, $output)) {
            [$field, $operator] = preg_split('/:/', $output[1]);
            if (!in_array($operator, $operators)) {
                throw new moodle_exception('nosuchoperator');
            }
            $replace = ' AND ' . $field . ' ' . $operator . ' ' . $filterendtime;
            $finalelements = str_replace('%%FILTER_ENDTIME:' . $output[1] . '%%', $replace, $finalelements);
        }

        $finalelements = str_replace('%%STARTTIME%%', $filterstarttime, $finalelements);
        $finalelements = str_replace('%%ENDTIME%%', $filterendtime, $finalelements);

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

        $mform->addElement('date_time_selector', 'filter_starttime', get_string('starttime', 'block_configurable_reports'));
        $mform->setDefault('filter_starttime', time() - 3600 * 24);
        $mform->addElement('date_time_selector', 'filter_endtime', get_string('endtime', 'block_configurable_reports'));
        $mform->setDefault('filter_endtime', time() + 3600 * 24);
    }

}
