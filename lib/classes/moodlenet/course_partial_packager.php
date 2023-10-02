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

use backup;
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
class course_partial_packager extends resource_packager {

    /** @var array $partialsharingtasks List of partial sharing tasks. */
    private array $partialsharingtasks = [];

    /**
     * Constructor for course partial packager.
     *
     * @param stdClass $course The course to package
     * @param array $cmids List of course module id of selected activities.
     * @param int $userid The ID of the user performing the packaging
     */
    public function __construct(
        protected stdClass $course,
        protected array $cmids,
        protected int $userid,
    ) {
        parent::__construct($course, $userid);

        $this->controller = new backup_controller(
            backup::TYPE_1COURSE,
            $course->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $userid
        );

        $this->resourcefilename = $this->course->shortname;
    }

    /**
     * Package the resource identified by resource id into a new stored_file.
     *
     * @return stored_file
     */
    protected function package(): stored_file {
        $this->remove_unselected_activities();
        return parent::package();
    }

    /**
     * Remove unselected activities in the course backup.
     */
    protected function remove_unselected_activities(): void {
        foreach ($this->partialsharingtasks as $task) {
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
     * Get all backup settings available for override.
     *
     * @return array the associative array of taskclass => settings instances.
     */
    protected function get_all_task_settings(): array {
        $tasksettings = [];
        foreach ($this->controller->get_plan()->get_tasks() as $task) {
            $taskclass = get_class($task);
            $tasksettings[$taskclass] = $task->get_settings();
            if ($task instanceof backup_activity_task) {
                // Store partial sharing tasks.
                $this->partialsharingtasks[] = $task;
            }
        }
        return $tasksettings;
    }

}
