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

/**
 * Backup steps for mod_plugnmeet are defined here.
 *
 * @package     mod_plugnmeet
 * @category    backup
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_plugnmeet_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $plugnmeet = new backup_nested_element('plugnmeet', array('id'), array(
            'course', 'name', 'roomid', 'welcomemessage', 'maxparticipants', 'roommetadata', 'intro', 'introformat', 'available', 'deadline'));

        // Define sources.
        $plugnmeet->set_source_table('plugnmeet', ['id' => backup::VAR_ACTIVITYID]);

        // Define file annotations.
        $plugnmeet->annotate_files('mod_plugnmeet', 'intro', null);

        return $this->prepare_activity_structure($plugnmeet);
    }
}
