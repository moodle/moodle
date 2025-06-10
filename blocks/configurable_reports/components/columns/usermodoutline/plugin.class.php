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
 * Class plugin_usermodoutline
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_usermodoutline extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('usermodoutline', 'block_configurable_reports');
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
     * @param object $user
     * @param int $courseid
     * @param int $starttime
     * @param int $endtime
     * @return string
     */
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        // Data -> Plugin configuration data.
        // Row -> Complet user row c->id, c->fullname, etc...

        global $DB, $CFG;
        if ($cm = $DB->get_record('course_modules', ['id' => $data->cmid])) {
            $mod = $DB->get_record('modules', ['id' => $cm->module]);
            if ($instance = $DB->get_record("$mod->name", ['id' => $cm->instance])) {
                $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
                if (file_exists($libfile)) {
                    require_once($libfile);
                    $useroutline = $mod->name . "_user_outline";
                    if (function_exists($useroutline)) {
                        if ($course = $DB->get_record('course', ['id' => $this->report->courseid])) {
                            $result = $useroutline($course, $row, $mod, $instance);
                            if ($result) {
                                $returndata = '';
                                if (isset($result->info)) {
                                    $returndata .= $result->info . ' ';
                                }

                                if ((!isset($data->donotshowtime) || !$data->donotshowtime) && !empty($result->time)) {
                                    $returndata .= userdate($result->time);
                                }

                                return $returndata;
                            }
                        }
                    }
                }
            }
        }

        return '';
    }

}
