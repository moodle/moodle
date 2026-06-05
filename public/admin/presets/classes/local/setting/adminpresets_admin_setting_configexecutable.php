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

namespace core_adminpresets\local\setting;

/**
 * Executable path setting for admin presets.
 *
 * @package   core_adminpresets
 * @copyright 2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_configexecutable extends adminpresets_admin_setting_configtext {
    /**
     * Saves the setting value only when $CFG->preventexecpath is unset
     * and the path is a valid executable.
     *
     * @param bool|string $name Setting name to use, or false to use the setting's own name.
     * @param mixed $value Setting value to store.
     * @return int|false config_log inserted id, or false if nothing was saved.
     */
    public function save_value($name = false, $value = null) {
        global $CFG;

        // When $CFG->preventexecpath is set, executable paths are managed through
        // config.php and must not be overwritten by admin presets.
        if (!empty($CFG->preventexecpath)) {
            return false;
        }

        // Resolve the value that would be written.
        $execpath = ($value !== null) ? $value : $this->value;

        // Validate non-empty paths: the target must be an existing, non-directory,
        // executable file.
        if (!empty($execpath)) {
            require_once($CFG->libdir . '/filelib.php');
            if (!file_exists($execpath) || is_dir($execpath) || !file_is_executable($execpath)) {
                return false;
            }
        }

        return parent::save_value($name, $value);
    }
}
