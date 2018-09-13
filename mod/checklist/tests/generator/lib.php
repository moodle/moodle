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
 * Generate a checklist activity
 *
 * @package   mod_checklist
 * @copyright 2014 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/mod/checklist/lib.php');

class mod_checklist_generator extends testing_module_generator {
    public function create_instance($record = null, array $options = null) {
        $record = (object)(array)$record;

        $defaultsettings = array(
            'timecreated' => time(),
            'timemodified' => time(),
            'useritemsallowed' => 1,
            'teacheredit' => CHECKLIST_MARKING_STUDENT,
            'duedatesoncalendar' => 0,
            'teachercomments' => 1,
            'maxgrade' => 100,
            'autopopulate' => CHECKLIST_AUTOPOPULATE_NO,
            'autoupdate' => CHECKLIST_AUTOUPDATE_YES,
            'completionpercent' => 0,
            'emailoncomplete' => 0,
            'lockteachermarks' => 0,
        );

        foreach ($defaultsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, (array)$options);
    }
}
