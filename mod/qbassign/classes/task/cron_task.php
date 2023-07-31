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

namespace mod_qbassign\task;
defined('MOODLE_INTERNAL') || die();

/**
 * A schedule task for qbassignment cron.
 *
 * @package   mod_qbassign
 * @copyright 2019 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'mod_qbassign');
    }

    /**
     * Run qbassignment cron.
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/mod/qbassign/locallib.php');
        \qbassign::cron();

        $plugins = \core_component::get_plugin_list('qbassignsubmission');

        foreach ($plugins as $name => $plugin) {
            $disabled = get_config('qbassignsubmission_' . $name, 'disabled');
            if (!$disabled) {
                $class = 'qbassign_submission_' . $name;
                require_once($CFG->dirroot . '/mod/qbassign/submission/' . $name . '/locallib.php');
                $class::cron();
            }
        }
        $plugins = \core_component::get_plugin_list('qbassignfeedback');

        foreach ($plugins as $name => $plugin) {
            $disabled = get_config('qbassignfeedback_' . $name, 'disabled');
            if (!$disabled) {
                $class = 'qbassign_feedback_' . $name;
                require_once($CFG->dirroot . '/mod/qbassign/feedback/' . $name . '/locallib.php');
                $class::cron();
            }
        }

        return true;
    }
}
