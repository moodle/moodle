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

namespace core_backup\fixtures;

use base_setting;
use restore_generic_setting;
use backup_setting_ui_checkbox;
use core_backup\hook\after_restore_root_define_settings;

/**
 * Callback class to test after_restore_root_define_settings hook.
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_task_hook_callbacks {
    /**
     * Tests use of after_restore_root_define_settings hook.
     *
     * @param after_restore_root_define_settings $hook
     */
    public static function after_restore_root_define_settings(after_restore_root_define_settings $hook) {
        $task = $hook->task;
        $defaultvalue = true;
        $changeable = true;

        $somebox = new restore_generic_setting('extra_test', base_setting::IS_BOOLEAN, $defaultvalue);
        $somebox->set_ui(new backup_setting_ui_checkbox($somebox, 'Extra test'));
        $somebox->get_ui()->set_changeable($changeable);
        $task->add_setting($somebox);
    }
}
