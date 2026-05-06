<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Day selection control for automated backups.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class special_backupdays extends \core_admin\setting\setting\configmulticheckbox2 {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct(
            'backup_auto_weekdays',
            get_string('automatedbackupschedule', 'backup'),
            get_string('automatedbackupschedulehelp', 'backup'),
            [],
            null,
        );
        $this->plugin = 'backup';
    }

    /**
     * Load the available choices for the select box
     *
     * @return bool Always returns true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = [];
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        foreach ($days as $day) {
            $this->choices[$day] = get_string($day, 'calendar');
        }
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_backupdays::class, \admin_setting_special_backupdays::class);
