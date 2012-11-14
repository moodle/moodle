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
 * mod_lti data generator
 *
 * @package    mod_lti
 * @category   phpunit
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Assignment module PHPUnit data generator class
 *
 * @package    mod_lti
 * @category   phpunit
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lti_generator extends phpunit_module_generator {

    /**
     * Create new lti module instance
     *
     * @param array|stdClass $record
     * @param array $options
     * @throws coding_exception
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG;

        require_once("$CFG->dirroot/mod/lti/lib.php");

        $this->instancecount++;
        $i = $this->instancecount;

        $record  = (object) (array) $record;
        $options = (array) $options;

        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }
        if (!isset($record->name)) {
            $record->name = get_string('pluginname', 'lti').' '.$i;
        }
        if (!isset($record->intro)) {
            $record->intro = 'Test lti '.$i;
        }
        if (!isset($record->introformat)) {
            $record->introformat = FORMAT_MOODLE;
        }
        if (!isset($record->toolurl)) {
            $record->toolurl = 'http://www.imsglobal.org/developers/LTI/test/v1p1/tool.php';
        }
        if (!isset($record->resourcekey)) {
            $record->resourcekey = '12345';
        }
        if (!isset($record->password)) {
            $record->password = 'secret';
        }
        if (!isset($record->grade)) {
            $record->grade = 100;
        }
        if (!isset($record->instructorchoicesendname)) {
            $record->instructorchoicesendname = 1;
        }
        if (!isset($record->instructorchoicesendemailaddr)) {
            $record->instructorchoicesendemailaddr = 1;
        }
        if (!isset($record->instructorchoiceacceptgrades)) {
            $record->instructorchoiceacceptgrades = 1;
        }
        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }
        $record->coursemodule = $this->precreate_course_module($record->course, $options);
        $id = lti_add_instance($record, null);
        return $this->post_add_instance($id, $record->coursemodule);
    }
}