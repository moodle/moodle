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
 * Special setting for backup auto destination.
 *
 * @package    core
 * @subpackage admin
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_backup_auto_destination extends admin_setting_configdirectory {

    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        parent::__construct('backup/backup_auto_destination', new lang_string('saveto'), new lang_string('backupsavetohelp'), '');
    }

    /**
     * Check if the directory must be set, depending on backup/backup_auto_storage.
     *
     * Note: backup/backup_auto_storage must be specified BEFORE this setting otherwise
     * there will be conflicts if this validation happens before the other one.
     *
     * @param string $data Form data.
     * @return string Empty when no errors.
     */
    public function write_setting($data) {
        $storage = (int) get_config('backup', 'backup_auto_storage');
        if ($storage !== 0) {
            if (empty($data) || !file_exists($data) || !is_dir($data) || !is_writable($data) ) {
                // The directory must exist and be writable.
                return get_string('backuperrorinvaliddestination');
            }
        }
        return parent::write_setting($data);
    }
}
