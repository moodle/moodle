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
 * mod_quiz data generator
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Quiz module PHPUnit data generator class
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_generator extends phpunit_module_generator {

    /**
     * Create new quiz module instance
     * @param array|stdClass $record
     * @param array $options (mostly course_module properties)
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG;
        require_once("$CFG->dirroot/mod/quiz/locallib.php");

        $this->instancecount++;
        $i = $this->instancecount;

        $record = (object)(array)$record;
        $options = (array)$options;

        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }
        if (!isset($record->name)) {
            $record->name = get_string('pluginname', 'quiz').' '.$i;
        }
        if (!isset($record->intro)) {
            $record->intro = 'Test quiz '.$i;
        }
        if (!isset($record->introformat)) {
            $record->introformat = FORMAT_MOODLE;
        }
        if (!isset($record->overduehandling)) {
            $record->overduehandling = 'autoabandon';
        }
        if (!isset($record->preferredbehavior)) {
            $record->preferredbehavior = 'deferredfeedback';
        }
        if (!isset($record->grade)) {
            $record->grade = 100;
        }
        if (!isset($record->quizpassword)) {
            $record->quizpassword = '';
        }
        if (!isset($record->feedbackboundarycount)) {
            $record->feedbackboundarycount = -1;
        }
        if (!isset($record->timeopen)) {
            $record->timeopen = 0;
        }
        if (!isset($record->timeclose)) {
            $record->timeclose = 0;
        }
        if (!isset($record->timelimit)) {
            $record->timelimit = 0;
        }
        if (!isset($record->questiondecimalpoints)) {
            $record->questiondecimalpoints = -2;
        }
        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }

        $record->coursemodule = $this->precreate_course_module($record->course, $options);
        $id = quiz_add_instance($record);
        return $this->post_add_instance($id, $record->coursemodule);
    }

}
