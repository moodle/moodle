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
 * Class plugin_usermodactions
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_usermodactions extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('usermodactions', 'block_configurable_reports');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = ['users'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        global $DB;
        // Should be a better way to do this.
        if ($cm = $DB->get_record('course_modules', ['id' => $data->cmid])) {
            $modname = $DB->get_field('modules', 'name', ['id' => $cm->module]);
            if ($name = $DB->get_field("$modname", 'name', ['id' => $cm->instance])) {
                return $data->columname . ' (' . $name . ')';
            }
        }

        return $data->columname;
    }

    /**
     * Execute
     *
     * @param object $data
     * @param object $row
     * @return string
     */
    public function execute($data, $row) {
        global $DB, $CFG;

        // Data -> Plugin configuration data.
        // Row -> Complet user row c->id, c->fullname, etc...
        require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");

        [$uselegacyreader, $useinternalreader, $logtable] = cr_logging_info();

        $sql = '';
        if ($uselegacyreader) {
            $sql = "SELECT COUNT('x') AS numviews
                      FROM {course_modules} cm
                           JOIN {log} l ON l.cmid = cm.id
                     WHERE cm.id = $data->cmid
                           AND l.userid = $row->id
                           AND l.action LIKE 'view%'
                  GROUP BY cm.id";
        } else if ($useinternalreader) {
            $sql = "SELECT COUNT('x') AS numviews
                      FROM {" . $logtable . "}
                     WHERE contextinstanceid = $data->cmid
                           AND contextlevel = " . CONTEXT_MODULE . "
                           AND userid = $row->id
                           AND crud = 'r'
                  GROUP BY contextinstanceid";
        }

        if (!empty($sql) && $views = $DB->get_record_sql($sql)) {
            return $views->numviews;
        }

        return 0;
    }

}
