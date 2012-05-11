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
 * Defines the form for editing Quiz results block instances.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Form for editing Quiz results block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quiz_results_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $DB;

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        if (!$this->block->get_owning_quiz()) {
            $quizzes = $DB->get_records_menu('quiz', array('course' => $this->page->course->id), '', 'id, name');
            if(empty($quizzes)) {
                $mform->addElement('static', 'noquizzeswarning', get_string('config_select_quiz', 'block_quiz_results'),
                        get_string('config_no_quizzes_in_course', 'block_quiz_results'));
            } else {
                foreach($quizzes as $id => $name) {
                    $quizzes[$id] = strip_tags(format_string($name));
                }
                $mform->addElement('select', 'config_quizid', get_string('config_select_quiz', 'block_quiz_results'), $quizzes);
            }
        }

        $mform->addElement('text', 'config_showbest', get_string('config_show_best', 'block_quiz_results'), array('size' => 3));
        $mform->setDefault('config_showbest', 3);
        $mform->setType('config_showbest', PARAM_INT);

        $mform->addElement('text', 'config_showworst', get_string('config_show_worst', 'block_quiz_results'), array('size' => 3));
        $mform->setDefault('config_showworst', 0);
        $mform->setType('config_showworst', PARAM_INT);

        $mform->addElement('selectyesno', 'config_usegroups', get_string('config_use_groups', 'block_quiz_results'));

        $nameoptions = array(
            B_QUIZRESULTS_NAME_FORMAT_FULL => get_string('config_names_full', 'block_quiz_results'),
            B_QUIZRESULTS_NAME_FORMAT_ID => get_string('config_names_id', 'block_quiz_results'),
            B_QUIZRESULTS_NAME_FORMAT_ANON => get_string('config_names_anon', 'block_quiz_results')
        );
        $mform->addElement('select', 'config_nameformat', get_string('config_name_format', 'block_quiz_results'), $nameoptions);
        $mform->setDefault('config_nameformat', B_QUIZRESULTS_NAME_FORMAT_FULL);

        $gradeeoptions = array(
            B_QUIZRESULTS_GRADE_FORMAT_PCT => get_string('config_format_percentage', 'block_quiz_results'),
            B_QUIZRESULTS_GRADE_FORMAT_FRA => get_string('config_format_fraction', 'block_quiz_results'),
            B_QUIZRESULTS_GRADE_FORMAT_ABS => get_string('config_format_absolute', 'block_quiz_results')
        );
        $mform->addElement('select', 'config_gradeformat', get_string('config_grade_format', 'block_quiz_results'), $gradeeoptions);
        $mform->setDefault('config_gradeformat', B_QUIZRESULTS_GRADE_FORMAT_PCT);
    }
}