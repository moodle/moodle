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
use backup_controller;
use backup_root_task;
use cm_info;
use core\context\user;
use stored_file;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Packager to prepare appropriate backup of an activity to share to MoodleNet.
 *
 * @package   core
 * @copyright 2023 Raquel Ortega <raquel.ortega@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_packager {

    /** @var backup_controller $controller */
    protected $controller;

    /**
     * Constructor.
     *
     * @param cm_info $cminfo context module information about the resource being packaged.
     * @param int $userid The ID of the user performing the packaging.
     */
    public function __construct(
        protected cm_info $cminfo,
        protected int $userid,
    ) {
        // Check backup/restore support.
        if (!plugin_supports('mod', $cminfo->modname , FEATURE_BACKUP_MOODLE2)) {
            throw new \coding_exception("Cannot backup module $cminfo->modname. This module doesn't support the backup feature.");
        }

        $this->controller = new backup_controller(
            backup::TYPE_1ACTIVITY,
            $cminfo->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $userid
        );
    }

    /**
     * Destructor
     */
    public function __destruct() {
        $this->controller->destroy();
    }

    /**
     * Prepare the backup file using appropriate setting overrides and return relevant information.
     *
     * @return stored_file
     */
    public function get_package(): stored_file {
        $alltasksettings = $this->get_all_task_settings();

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

        return $this->package();
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
     * Package the activity identified by CMID into a new stored_file.
     *
     * @return stored_file
     * @throws \moodle_exception
     */
    protected function package(): stored_file {
        // Execute the backup and fetch the result.
        $this->controller->execute_plan();
        $result = $this->controller->get_results();

        if (!isset($result['backup_destination'])) {
            throw new \moodle_exception('Failed to package activity.');
        }

        $backupfile = $result['backup_destination'];

        if (!$backupfile->get_contenthash()) {
            throw new \moodle_exception('Failed to package activity (invalid file).');
        }

        // Create the location we want to copy this file to.
        $filerecord = [
            'contextid' => user::instance($this->userid)->id,
            'userid' => $this->userid,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => $this->cminfo->modname . '_backup.mbz',
        ];

        // Create the local file based on the backup.
        $fs = get_file_storage();
        $file = $fs->create_file_from_storedfile($filerecord, $backupfile);

        // Delete the backup now it has been created in the file area.
        $backupfile->delete();

        return $file;
    }
}
