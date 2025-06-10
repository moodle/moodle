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
 * Class plugin_enrolledstudents
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_enrolledstudents extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filterenrolledstudents', 'block_configurable_reports');
        $this->reporttypes = ['courses', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filterenrolledstudents_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @return array|string|string[]
     */
    public function execute($finalelements) {
        $filterenrolledstudents = optional_param('filter_enrolledstudents', 0, PARAM_INT);
        if (!$filterenrolledstudents) {
            return $finalelements;
        }

        if ($this->report->type !== 'sql') {
            return [$filterenrolledstudents];
        }

        if (preg_match("/%%FILTER_COURSEENROLLEDSTUDENTS:([^%]+)%%/i", $finalelements, $output)) {
            $replace = ' AND ' . $output[1] . ' = ' . $filterenrolledstudents;

            return str_replace('%%FILTER_COURSEENROLLEDSTUDENTS:' . $output[1] . '%%', $replace, $finalelements);
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
        global $remotedb, $COURSE, $PAGE, $CFG;

        $reportclassname = 'report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        if ($this->report->type !== 'sql') {
            $components = cr_unserialize($this->report->components);
            $conditions = $components['conditions'];

            $enrolledstudentslist = $reportclass->elements_by_conditions($conditions);
        } else {
            $sql = 'SELECT ra.userid
                      FROM {role_assignments} ra
                      JOIN {context} ctx ON ra.contextid = ctx.id AND ctx.contextlevel = 50
                     WHERE ra.roleid = 5 AND ctx.instanceid = ?';

            $studentlist = $remotedb->get_records_sql($sql, [$COURSE->id]);
            foreach ($studentlist as $student) {
                $enrolledstudentslist[] = $student->userid;
            }
        }

        $enrolledstudentsoptions = [];
        $enrolledstudentsoptions[0] = get_string('filter_all', 'block_configurable_reports');

        if (!empty($enrolledstudentslist)) {
            if (has_capability('moodle/site:viewfullnames', $PAGE->context)) {
                $nameformat = $CFG->alternativefullnameformat;
            } else {
                $nameformat = $CFG->fullnamedisplay;
            }

            if ($nameformat === 'language') {
                $nameformat = get_string('fullnamedisplay');
            }

            $sort = implode(',', order_in_string(get_all_user_name_fields(), $nameformat));

            [$usql, $params] = $remotedb->get_in_or_equal($enrolledstudentslist);
            $enrolledstudents =
                $remotedb->get_records_select('user', "id " . $usql, $params, $sort, 'id,' . get_all_user_name_fields(true));

            foreach ($enrolledstudents as $c) {
                $enrolledstudentsoptions[$c->id] = fullname($c);
            }
        }

        $elestr = get_string('student', 'block_configurable_reports');
        $mform->addElement('select', 'filter_enrolledstudents', $elestr, $enrolledstudentsoptions);
        $mform->setType('filter_enrolledstudents', PARAM_INT);
    }

}
