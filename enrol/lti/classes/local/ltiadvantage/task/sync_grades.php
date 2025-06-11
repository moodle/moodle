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

namespace enrol_lti\local\ltiadvantage\task;

use core\task\scheduled_task;
use enrol_lti\helper;

/**
 * LTI Advantage task responsible for pushing grades to tool platforms.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_grades extends scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasksyncgrades', 'enrol_lti');
    }

    /**
     * Creates adhoc tasks (one per resource) to synchronize grades from the tool to any registered platforms.
     *
     * @return bool|void
     */
    public function execute() {

        if (!is_enabled_auth('lti')) {
            mtrace('Skipping task - ' . get_string('pluginnotenabled', 'auth', get_string('pluginname', 'auth_lti')));
            return true;
        }
        if (!enrol_is_enabled('lti')) {
            mtrace('Skipping task - ' . get_string('enrolisdisabled', 'enrol_lti'));
            return true;
        }

        $resources = helper::get_lti_tools([
            'status' => ENROL_INSTANCE_ENABLED,
            'gradesync' => 1,
            'ltiversion' => 'LTI-1p3'
        ]);
        if (empty($resources)) {
            mtrace('Skipping task - There are no resources with grade sync enabled.');
            return true;
        }

        foreach ($resources as $resource) {
            $task = new \enrol_lti\local\ltiadvantage\task\sync_tool_grades();
            $task->set_custom_data($resource);
            $task->set_component('enrol_lti');
            \core\task\manager::queue_adhoc_task($task, true);
        }

        mtrace('Spawned ' . count($resources) . ' adhoc tasks to sync grades.');
    }
}
