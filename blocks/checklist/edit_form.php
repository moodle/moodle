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
 * Instance settings form
 *
 * @package   block_checklist
 * @copyright 2010 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_checklist_edit_form extends block_edit_form {
    /**
     * @param MoodleQuickForm $mform
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        global $DB, $COURSE;

        if ($COURSE->format !== 'site') {
            $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
            $mform->addElement('selectyesno', 'config_checklistoverview', get_string('checklistoverview', 'block_checklist'));
            $options = array();
            $checklists = $DB->get_records('checklist', array('course' => $COURSE->id));
            foreach ($checklists as $checklist) {
                $options[$checklist->id] = s($checklist->name);
            }
            $mform->addElement('select', 'config_checklistid', get_string('choosechecklist', 'block_checklist'), $options);
            $mform->disabledIf('config_checklistid', 'config_checklistoverview', 'eq', 1);

            $options = array(0 => get_string('allparticipants'));
            $groups = $DB->get_records('groups', array('courseid' => $COURSE->id));
            foreach ($groups as $group) {
                $options[$group->id] = s($group->name);
            }
            $mform->addElement('select', 'config_groupid', get_string('choosegroup', 'block_checklist'), $options);
            $mform->disabledIf('config_groupid', 'config_checklistoverview', 'eq', 1);
        }
    }
}
