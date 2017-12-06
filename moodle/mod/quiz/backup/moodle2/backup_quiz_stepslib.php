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
 * @package    mod_quiz
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Define all the backup steps that will be used by the backup_quiz_activity_task
 *
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_quiz_activity_structure_step extends backup_questions_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $quiz = new backup_nested_element('quiz', array('id'), array(
            'name', 'intro', 'introformat', 'timeopen', 'timeclose', 'timelimit',
            'overduehandling', 'graceperiod', 'preferredbehaviour', 'canredoquestions', 'attempts_number',
            'attemptonlast', 'grademethod', 'decimalpoints', 'questiondecimalpoints',
            'reviewattempt', 'reviewcorrectness', 'reviewmarks',
            'reviewspecificfeedback', 'reviewgeneralfeedback',
            'reviewrightanswer', 'reviewoverallfeedback',
            'questionsperpage', 'navmethod', 'shuffleanswers',
            'sumgrades', 'grade', 'timecreated',
            'timemodified', 'password', 'subnet', 'browsersecurity',
            'delay1', 'delay2', 'showuserpicture', 'showblocks', 'completionattemptsexhausted', 'completionpass',
            'allowofflineattempts'));

        // Define elements for access rule subplugin settings.
        $this->add_subplugin_structure('quizaccess', $quiz, true);

        $qinstances = new backup_nested_element('question_instances');

        $qinstance = new backup_nested_element('question_instance', array('id'), array(
            'slot', 'page', 'requireprevious', 'questionid', 'maxmark'));

        $sections = new backup_nested_element('sections');

        $section = new backup_nested_element('section', array('id'), array(
            'firstslot', 'heading', 'shufflequestions'));

        $feedbacks = new backup_nested_element('feedbacks');

        $feedback = new backup_nested_element('feedback', array('id'), array(
            'feedbacktext', 'feedbacktextformat', 'mingrade', 'maxgrade'));

        $overrides = new backup_nested_element('overrides');

        $override = new backup_nested_element('override', array('id'), array(
            'userid', 'groupid', 'timeopen', 'timeclose',
            'timelimit', 'attempts', 'password'));

        $grades = new backup_nested_element('grades');

        $grade = new backup_nested_element('grade', array('id'), array(
            'userid', 'gradeval', 'timemodified'));

        $attempts = new backup_nested_element('attempts');

        $attempt = new backup_nested_element('attempt', array('id'), array(
            'userid', 'attemptnum', 'uniqueid', 'layout', 'currentpage', 'preview',
            'state', 'timestart', 'timefinish', 'timemodified', 'timemodifiedoffline', 'timecheckstate', 'sumgrades'));

        // This module is using questions, so produce the related question states and sessions
        // attaching them to the $attempt element based in 'uniqueid' matching.
        $this->add_question_usages($attempt, 'uniqueid');

        // Define elements for access rule subplugin attempt data.
        $this->add_subplugin_structure('quizaccess', $attempt, true);

        // Build the tree.
        $quiz->add_child($qinstances);
        $qinstances->add_child($qinstance);

        $quiz->add_child($sections);
        $sections->add_child($section);

        $quiz->add_child($feedbacks);
        $feedbacks->add_child($feedback);

        $quiz->add_child($overrides);
        $overrides->add_child($override);

        $quiz->add_child($grades);
        $grades->add_child($grade);

        $quiz->add_child($attempts);
        $attempts->add_child($attempt);

        // Define sources.
        $quiz->set_source_table('quiz', array('id' => backup::VAR_ACTIVITYID));

        $qinstance->set_source_table('quiz_slots',
                array('quizid' => backup::VAR_PARENTID));

        $section->set_source_table('quiz_sections',
                array('quizid' => backup::VAR_PARENTID));

        $feedback->set_source_table('quiz_feedback',
                array('quizid' => backup::VAR_PARENTID));

        // Quiz overrides to backup are different depending of user info.
        $overrideparams = array('quiz' => backup::VAR_PARENTID);
        if (!$userinfo) { //  Without userinfo, skip user overrides.
            $overrideparams['userid'] = backup_helper::is_sqlparam(null);

        }
        $override->set_source_table('quiz_overrides', $overrideparams);

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $grade->set_source_table('quiz_grades', array('quiz' => backup::VAR_PARENTID));
            $attempt->set_source_sql('
                    SELECT *
                    FROM {quiz_attempts}
                    WHERE quiz = :quiz AND preview = 0',
                    array('quiz' => backup::VAR_PARENTID));
        }

        // Define source alias.
        $quiz->set_source_alias('attempts', 'attempts_number');
        $grade->set_source_alias('grade', 'gradeval');
        $attempt->set_source_alias('attempt', 'attemptnum');

        // Define id annotations.
        $qinstance->annotate_ids('question', 'questionid');
        $override->annotate_ids('user', 'userid');
        $override->annotate_ids('group', 'groupid');
        $grade->annotate_ids('user', 'userid');
        $attempt->annotate_ids('user', 'userid');

        // Define file annotations.
        $quiz->annotate_files('mod_quiz', 'intro', null); // This file area hasn't itemid.
        $feedback->annotate_files('mod_quiz', 'feedback', 'id');

        // Return the root element (quiz), wrapped into standard activity structure.
        return $this->prepare_activity_structure($quiz);
    }
}
