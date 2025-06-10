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
 * Class plugin_usersincohorts
 *
 * @package          block_configurable_reports
 * @author           Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_usersincohorts extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('usersincohorts', 'block_configurable_reports');
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
        return get_string('usersincohorts_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param object $data
     * @return array|int[]|string[]
     */
    public function execute($data) {
        global $DB;
        // Data -> Plugin configuration data.
        if ($data->cohorts) {
            [$insql, $params] = $DB->get_in_or_equal($data->cohorts);

            $sql = "SELECT u.id
            FROM {user} u JOIN {cohort_members} c ON c.userid = u.id
            WHERE c.cohortid $insql ";

            return array_keys($DB->get_records_sql($sql, $params));
        }

        return [];
    }

}
