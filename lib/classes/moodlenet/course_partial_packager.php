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

namespace core\moodlenet;

use backup_activity_task;
use backup_controller;
use stdClass;
use stored_file;

/**
 * Packager to prepare appropriate backup of a number of activities in a course to share to MoodleNet.
 *
 * @package    core
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_partial_packager extends course_packager {

    /**
     * @var int[] $cmids List of course module ids of selected activities.
     */
    protected array $cmids;

    /**
     * Constructor for course partial packager.
     *
     * @param stdClass $course The course to package
     * @param array $cmids List of course module id of selected activities.
     * @param int $userid The ID of the user performing the packaging
     */
    public function __construct(
        stdClass $course,
        array $cmids,
        int $userid,
    ) {
        $this->cmids = $cmids;
        parent::__construct($course, $userid);
    }

    /**
     * Package the resource identified by resource id into a new stored_file.
     *
     * @param backup_controller $controller The backup controller.
     * @return stored_file
     */
    protected function package(backup_controller $controller): stored_file {
        $this->remove_unselected_activities($controller);
        return parent::package($controller);
    }

    /**
     * Remove unselected activities in the course backup.
     *
     * @param backup_controller $controller The backup controller.
     */
    protected function remove_unselected_activities(backup_controller $controller): void {
        foreach ($this->get_all_activity_tasks($controller) as $task) {
            foreach ($task->get_settings() as $setting) {
                if (in_array($task->get_moduleid(), $this->cmids) &&
                    str_contains($setting->get_name(), '_included') !== false) {
                    $setting->set_value(1);
                } else {
                    $setting->set_value(0);
                }
            }
        }
    }

    /**
     * Get all the activity tasks in the controller.
     *
     * @param backup_controller $controller The backup controller.
     * @return backup_activity_task[] Array of activity tasks.
     */
    protected function get_all_activity_tasks(backup_controller $controller): array {
        $tasks = [];
        foreach ($controller->get_plan()->get_tasks() as $task) {
            if (! $task instanceof backup_activity_task) { // Only for activity tasks.
                continue;
            }
            $tasks[] = $task;
        }
        return $tasks;
    }
}
