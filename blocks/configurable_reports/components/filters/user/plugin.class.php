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
 * Class plugin_user
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_user extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filteruser', 'block_configurable_reports');
        $this->reporttypes = ['courses', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filteruser_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @return array|string|string[]
     */
    public function execute($finalelements) {
        $filteruser = optional_param('filter_user', 0, PARAM_INT);
        if (!$filteruser) {
            return $finalelements;
        }

        if ($this->report->type !== 'sql') {
            return [$filteruser];
        } else {
            if (preg_match("/%%FILTER_COURSEUSER:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filteruser;

                return str_replace('%%FILTER_COURSEUSER:' . $output[1] . '%%', $replace, $finalelements);
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

        global $remotedb, $COURSE, $PAGE, $CFG;

        $reportclassname = 'report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        if ($this->report->type !== 'sql') {
            $components = cr_unserialize($this->report->components);
            $conditions = $components['conditions'];
            $userlist = $reportclass->elements_by_conditions($conditions);
        } else {
            $coursecontext = context_course::instance($COURSE->id);
            $userlist = array_keys(get_users_by_capability($coursecontext, 'moodle/user:viewdetails'));
        }

        $useroptions = [];
        $useroptions[0] = get_string('filter_all', 'block_configurable_reports');

        if (!empty($userlist)) {
            if (has_capability('moodle/site:viewfullnames', $PAGE->context)) {
                $nameformat = $CFG->alternativefullnameformat;
            } else {
                $nameformat = $CFG->fullnamedisplay;
            }

            if ($nameformat === 'language') {
                $nameformat = get_string('fullnamedisplay');
            }

            $sort = implode(',', order_in_string(get_all_user_name_fields(), $nameformat));

            [$usql, $params] = $remotedb->get_in_or_equal($userlist);
            $users = $remotedb->get_records_select('user', "id " . $usql, $params, $sort, 'id,' . get_all_user_name_fields(true));

            foreach ($users as $c) {
                $useroptions[$c->id] = fullname($c);
            }
        }

        $mform->addElement('select', 'filter_user', get_string('user'), $useroptions);
        $mform->setType('filter_user', PARAM_INT);
    }

}
