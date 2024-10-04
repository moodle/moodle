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

use backup_controller;
use backup_root_task;
use cm_info;
use core\context\user;
use stdClass;
use stored_file;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Base packager to prepare appropriate backup of a resource to share to MoodleNet.
 *
 * @package   core
 * @copyright 2023 Safat Shahin <safat.shahin@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class resource_packager {

    /**
     * @var string $resourcefilename The filename for the resource.
     */
    protected string $resourcefilename = 'resource';

    /**
     * @var stdClass $course The course which the resource belongs to.
     */
    protected stdClass $course;

    /**
     * @var cm_info $cminfo The course module which the resource belongs to.
     */
    protected cm_info $cminfo;

    /**
     * @var int $userid The ID of the user performing the packaging.
     */
    protected int $userid;

    /**
     * Constructor for the base packager.
     *
     * @param stdClass|cm_info $resource The resource object
     * @param int $userid The user id
     */
    public function __construct(
        stdClass|cm_info $resource,
        int $userid,
        string $resourcefilename,
    ) {
        if ($resource instanceof cm_info) {
            $this->cminfo = $resource;
            $this->course = $resource->get_course();
        } else {
            $this->course = $resource;
        }

        $this->userid = $userid;
        $this->resourcefilename = $resourcefilename;
    }

    /**
     * Get the backup controller for the course.
     *
     * @return backup_controller The backup controller instance that will be used to package the resource.
     */
    abstract protected function get_backup_controller(): backup_controller;

    /**
     * Prepare the backup file using appropriate setting overrides and return relevant information.
     *
     * @return stored_file
     */
    public function get_package(): stored_file {
        $controller = $this->get_backup_controller();
        $alltasksettings = $this->get_all_task_settings($controller);

        // Override relevant settings to remove user data when packaging to share to MoodleNet.
        $this->override_task_setting($alltasksettings, 'setting_root_users', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_role_assignments', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_blocks', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_comments', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_badges', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_userscompletion', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_logs', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_grade_histories', 0);
        $this->override_task_setting($alltasksettings, 'setting_root_groups', 0);

        $storedfile = $this->package($controller);

        $controller->destroy(); // We are done with the controller, destroy it.

        return $storedfile;
    }

    /**
     * Get all backup settings available for override.
     *
     * @return array the associative array of taskclass => settings instances.
     */
    protected function get_all_task_settings(backup_controller $controller): array {
        $tasksettings = [];
        foreach ($controller->get_plan()->get_tasks() as $task) {
            $taskclass = get_class($task);
            $tasksettings[$taskclass] = $task->get_settings();
        }
        return $tasksettings;
    }

    /**
     * Override a backup task setting with a given value.
     *
     * @param array $alltasksettings All task settings.
     * @param string $settingname The name of the setting to be overridden (task class name format).
     * @param int $settingvalue Value to be given to the setting.
     */
    protected function override_task_setting(array $alltasksettings, string $settingname, int $settingvalue): void {
        if (empty($rootsettings = $alltasksettings[backup_root_task::class])) {
            return;
        }

        foreach ($rootsettings as $setting) {
            $name = $setting->get_ui_name();
            if ($name == $settingname && $settingvalue != $setting->get_value()) {
                $setting->set_value($settingvalue);
                return;
            }
        }
    }

    /**
     * Package the resource identified by resource id into a new stored_file.
     *
     * @param backup_controller $controller The backup controller.
     * @return stored_file
     */
    protected function package(backup_controller $controller): stored_file {
        // Execute the backup and fetch the result.
        $controller->execute_plan();
        $result = $controller->get_results();

        if (!isset($result['backup_destination'])) {
            throw new \moodle_exception('Failed to package resource.');
        }

        $backupfile = $result['backup_destination'];

        if (!$backupfile->get_contenthash()) {
            throw new \moodle_exception('Failed to package resource (invalid file).');
        }

        // Create the location we want to copy this file to.
        $filerecord = [
            'contextid' => user::instance($this->userid)->id,
            'userid' => $this->userid,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => $this->resourcefilename . '_backup.mbz',
        ];

        // Create the local file based on the backup.
        $fs = get_file_storage();
        $file = $fs->create_file_from_storedfile($filerecord, $backupfile);

        // Delete the backup now it has been created in the file area.
        $backupfile->delete();

        return $file;
    }
}
