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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 * Based off of a template @ http://docs.moodle.org/en/Development:Backup_1.9_conversion_for_developers
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2011 Aparup Banerjee <aparup@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz conversion handler
 */
class moodle1_mod_quiz_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'quiz', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ',
                array(
                    'newfields' => array(
                        'showuserpicture'   => 0,
                        'questiondecimalpoints' => -2,
                        'introformat'   => 0,
                        'showblocks'    => 0,
                    )
                )
            ),
            new convert_path('quiz_question_instances', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/QUESTION_INSTANCES'),
            new convert_path('quiz_question_instance', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/QUESTION_INSTANCES/QUESTION_INSTANCE'),
            new convert_path('quiz_feedbacks', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/FEEDBACKS'),
            new convert_path('quiz_feedback', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/FEEDBACKS/FEEDBACK',
                array(
                    'newfields' => array(
                        'feedbacktextformat' => 0
                    )
                )
            )
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ
     * data available
     */
    public function process_quiz($data) {

        //quiz: SET sumgrades=0 WHERE sumgrades IS NULL
        if (is_null($data['sumgrades'])) {
            $data['sumgrades'] = 0;
            //@todo for user data: quiz_attempts SET sumgrades=0 WHERE sumgrades IS NULL
            //@todo for user data: quiz_grades.grade should be not be null , convert to default 0
        }

        /// Convert 1.9 quiz.timelimit from minutes to seconds.
        $data['timelimit'] *= 60;

        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid);
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/quiz_{$moduleid}/quiz.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'quiz', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('quiz', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }
    }

    public function on_quiz_question_instances_start() {
        $this->xmlwriter->begin_tag('question_instances');
    }

    public function on_quiz_question_instances_end() {
        $this->xmlwriter->end_tag('question_instances');
    }

    public function process_quiz_question_instance($data) {
        // quiz_question_instances.grade should be decimal(12,7) not null default 0
        // $data['grade'] and other precisions will be changed when inserted into db.

        $this->write_xml('question_instance', $data, array('/question_instance/id'));
    }

    public function on_quiz_feedbacks_start() {
        $this->xmlwriter->begin_tag('feedbacks');
    }

    public function on_quiz_feedbacks_end() {
        $this->xmlwriter->end_tag('feedbacks');
    }

    public function process_quiz_feedback($data) {
        //quiz_feedback.mingrade not null! defaults 0
        if (is_null($data['mingrade'])) {
            $data['mingrade'] = 0;
        }
        //quiz_feedback.maxgrade not null! defaults 0
        if (is_null($data['maxgrade'])) {
            $data['maxgrade'] = 0;
        }

        $this->write_xml('feedback', $data, array('/feedback/id'));
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'quiz' path
     */
    public function on_quiz_end() {
        // create 1 new <overrides> element (no data from 1.9)
        $this->write_xml('overrides', array());

        $this->xmlwriter->end_tag('quiz');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }
}
