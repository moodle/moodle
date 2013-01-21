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
 * Quiz module test data generator class
 *
 * @package mod_quiz
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_generator extends testing_module_generator {

    /**
     * Create new quiz module instance.
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
        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }

        $alwaysvisible = mod_quiz_display_options::DURING | mod_quiz_display_options::IMMEDIATELY_AFTER |
                mod_quiz_display_options::LATER_WHILE_OPEN | mod_quiz_display_options::AFTER_CLOSE;

        $defaultquizsettings = array(
            'name'                   => get_string('pluginname', 'quiz').' '.$i,
            'intro'                  => 'Test quiz ' . $i,
            'introformat'            => FORMAT_MOODLE,
            'timeopen'               => 0,
            'timeclose'              => 0,
            'preferredbehaviour'     => 'deferredfeedback',
            'attempts'               => 0,
            'attemptonlast'          => 0,
            'grademethod'            => QUIZ_GRADEHIGHEST,
            'decimalpoints'          => 2,
            'questiondecimalpoints'  => -1,
            'reviewattempt'          => $alwaysvisible,
            'reviewcorrectness'      => $alwaysvisible,
            'reviewmarks'            => $alwaysvisible,
            'reviewspecificfeedback' => $alwaysvisible,
            'reviewgeneralfeedback'  => $alwaysvisible,
            'reviewrightanswer'      => $alwaysvisible,
            'reviewoverallfeedback'  => $alwaysvisible,
            'questionsperpage'       => 1,
            'shufflequestions'       => 0,
            'shuffleanswers'         => 1,
            'questions'              => '',
            'sumgrades'              => 0,
            'grade'                  => 0,
            'timecreated'            => time(),
            'timemodified'           => time(),
            'timelimit'              => 0,
            'overduehandling'        => 'autoabandon',
            'graceperiod'            => 86400,
            'quizpassword'           => '',
            'subnet'                 => '',
            'browsersecurity'        => '',
            'delay1'                 => 0,
            'delay2'                 => 0,
            'showuserpicture'        => 0,
            'showblocks'             => 0,
            'navmethod'              => QUIZ_NAVMETHOD_FREE,
        );

        foreach ($defaultquizsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        $record->coursemodule = $this->precreate_course_module($record->course, $options);
        $id = quiz_add_instance($record);
        return $this->post_add_instance($id, $record->coursemodule);
    }
}
