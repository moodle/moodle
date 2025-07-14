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
 * core_notes data generator.
 *
 * @package    core_notes
 * @category   test
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * core_notes data generator class.
 *
 * @package    core_notes
 * @category   test
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_notes_generator extends component_generator_base {

    /**
     * @var number of created instances
     */
    protected $instancecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->instancecount = 0;
    }

    /**
     * Create a new note.
     *
     * @param array|stdClass $record
     * @throws coding_exception
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null) {
        global $CFG, $USER;
        require_once("$CFG->dirroot/notes/lib.php");

        $this->instancecount++;
        $i = $this->instancecount;
        $record = (object)(array)$record;

        if (empty($record->courseid)) {
            throw new coding_exception('Module generator requires $record->courseid.');
        }
        if (empty($record->userid)) {
            throw new coding_exception('Module generator requires $record->userid.');
        }
        if (!isset($record->module)) {
            $record->module = 'notes';
        }
        if (!isset($record->groupid)) {
            $record->groupid = 0;
        }
        if (!isset($record->moduleid)) {
            $record->moduleid = 0;
        }
        if (!isset($record->coursemoduleid)) {
            $record->coursemoduleid = 0;
        }
        if (!isset($record->subject)) {
            $record->subject = '';
        }
        if (!isset($record->summary)) {
            $record->summary = null;
        }
        if (!isset($record->content)) {
            $record->content = "This is test generated note - $i .";
        }
        if (!isset($record->uniquehash)) {
            $record->uniquehash = '';
        }
        if (!isset($record->rating)) {
            $record->rating = 0;
        }
        if (!isset($record->format)) {
            $record->format = FORMAT_PLAIN;
        }
        if (!isset($record->summaryformat)) {
            $record->summaryformat = FORMAT_MOODLE;
        }
        if (!isset($record->attachment)) {
            $record->attachment = null;
        }
        if (!isset($record->publishstate)) {
            $record->publishstate = NOTES_STATE_SITE;
        }
        if (!isset($record->lastmodified)) {
            $record->lastmodified = time();
        }
        if (!isset($record->created)) {
            $record->created = time();
        }
        if (!isset($record->usermodified)) {
            $record->usermodified = $USER->id;
        }

        note_save($record);
        return $record;
    }

}

