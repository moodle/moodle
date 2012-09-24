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

defined('MOODLE_INTERNAL') || die();

/**
 * assign module PHPUnit data generator class
 *
 * @package mod_assign
 * @category phpunit
 * @copyright 2012 Paul Charsley
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_generator extends phpunit_module_generator {

    /**
     * Create new assign module instance
     * @param array|stdClass $record
     * @param array $options (mostly course_module properties)
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG;
        require_once("$CFG->dirroot/mod/assign/lib.php");

        $this->instancecount++;
        $i = $this->instancecount;

        $record = (object)(array)$record;
        $options = (array)$options;

        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }
        if (!isset($record->name)) {
            $record->name = get_string('pluginname', 'data').' '.$i;
        }
        if (!isset($record->intro)) {
            $record->intro = 'Test database '.$i;
        }
        if (!isset($record->introformat)) {
            $record->introformat = FORMAT_MOODLE;
        }
        if (!isset($record->alwaysshowdescription)) {
            $record->alwaysshowdescription = 1;
        }
        if (!isset($record->nosubmissions)) {
            $record->nosubmissions = 0;
        }
        if (!isset($record->submissiondrafts)) {
            $record->submissiondrafts = 1;
        }
        if (!isset($record->requiresubmissionstatement)) {
            $record->requiresubmissionstatement = 0;
        }
        if (!isset($record->sendnotifications)) {
            $record->sendnotifications = 0;
        }
        if (!isset($record->sendlatenotifications)) {
            $record->sendlatenotifications = 0;
        }
        if (!isset($record->duedate)) {
            $record->duedate = 0;
        }
        if (!isset($record->allowsubmissionsfromdate)) {
            $record->allowsubmissionsfromdate = 0;
        }
        if (!isset($record->assignsubmission_onlinetext_enabled)) {
            $record->assignsubmission_onlinetext_enabled = 0;
        }
        if (!isset($record->assignsubmission_file_enabled)) {
            $record->assignsubmission_file_enabled = 0;
        }
        if (!isset($record->assignsubmission_comments_enabled)) {
            $record->assignsubmission_comments_enabled = 0;
        }
        if (!isset($record->assignfeedback_comments_enabled)) {
            $record->assignfeedback_comments_enabled = 0;
        }
        if (!isset($record->assignfeedback_file_enabled)) {
            $record->assignfeedback_file_enabled = 0;
        }
        if (!isset($record->assignfeedback_offline_enabled)) {
            $record->assignfeedback_offline_enabled = 0;
        }
        if (!isset($record->grade)) {
            $record->grade = 100;
        }
        if (!isset($record->cutoffdate)) {
            $record->cutoffdate = 0;
        }
        if (!isset($record->teamsubmission)) {
            $record->teamsubmission = 0;
        }
        if (!isset($record->requireallteammemberssubmit)) {
            $record->requireallteammemberssubmit = 0;
        }
        if (!isset($record->teamsubmissiongroupingid)) {
            $record->teamsubmissiongroupingid = 0;
        }
        if (!isset($record->blindmarking)) {
            $record->blindmarking = 0;
        }
        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }

        $record->coursemodule = $this->precreate_course_module($record->course, $options);
        $id = assign_add_instance($record, null);
        return $this->post_add_instance($id, $record->coursemodule);
    }
}
