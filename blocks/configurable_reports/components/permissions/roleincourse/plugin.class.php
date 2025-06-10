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
 * Class plugin_roleincourse
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_roleincourse extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = true;
        $this->unique = false;
        $this->fullname = get_string('roleincourse', 'block_configurable_reports');
        $this->reporttypes = ['courses', 'sql', 'users', 'timeline', 'categories'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        global $DB;

        $rolename = $DB->get_field('role', 'shortname', ['id' => $data->roleid]);
        $coursename = $DB->get_field('course', 'fullname', ['id' => $this->report->courseid]);

        return $rolename . ' ' . $coursename;
    }

    /**
     * Execute
     *
     * @param int $userid
     * @param context $context
     * @param object $data
     * @return bool
     */
    public function execute($userid, $context, $data): bool {
        $roles = get_user_roles($context, $userid);
        if (!empty($roles)) {
            foreach ($roles as $rol) {
                if ((int) $rol->roleid === (int) $data->roleid) {
                    return true;
                }
            }
        }

        return false;
    }

}
