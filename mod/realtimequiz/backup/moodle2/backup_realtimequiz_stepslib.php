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
 * Backup a realtimequiz
 * @package mod_realtimequiz
 * @subpackage backup-moodle2
 * @copyright 2013 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_realtimequiz_activity_task
 */

/**
 * Define the complete realtimequiz structure for backup, with file and id annotations
 */
class backup_realtimequiz_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the backup structure
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws base_step_exception
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.

        $realtimequiz = new backup_nested_element('realtimequiz', ['id'], [
            'name', 'intro', 'introformat', 'timecreated', 'timemodified', 'questiontime',
        ]);

        $sessions = new backup_nested_element('sessions');
        $session = new backup_nested_element('session', ['id'], [
            'name', 'timestamp',
        ]);

        $questions = new backup_nested_element('questions');
        $question = new backup_nested_element('question', ['id'], [
            'questionnum', 'questiontext', 'questiontextformat', 'questiontime',
        ]);

        $answers = new backup_nested_element('answers');
        $answer = new backup_nested_element('answer', ['id'], [
            'answertext', 'correct',
        ]);

        $submissions = new backup_nested_element('submissions');
        $submission = new backup_nested_element('submission', ['id'], [
            'sessionid', 'userid', 'answerid',
        ]);

        // Build the tree.
        if ($userinfo) {
            // Sessions need to be backed up (& restored) before the questions (with the submissions within them).
            $realtimequiz->add_child($sessions);
            $sessions->add_child($session);
        }

        $realtimequiz->add_child($questions);
        $questions->add_child($question);

        $question->add_child($answers);
        $answers->add_child($answer);

        if ($userinfo) {
            $question->add_child($submissions);
            $submissions->add_child($submission);
        }

        // Define sources.

        $realtimequiz->set_source_table('realtimequiz', ['id' => backup::VAR_ACTIVITYID]);
        $question->set_source_table('realtimequiz_question', ['quizid' => backup::VAR_PARENTID]);
        $answer->set_source_table('realtimequiz_answer', ['questionid' => backup::VAR_PARENTID]);

        if ($userinfo) {
            $session->set_source_table('realtimequiz_session', ['quizid' => backup::VAR_PARENTID]);
            $submission->set_source_table('realtimequiz_submitted', ['questionid' => backup::VAR_PARENTID]);
        }

        // Define id annotations.
        if ($userinfo) {
            $submission->annotate_ids('user', 'userid');
        }

        // Define file annotations.
        $realtimequiz->annotate_files('mod_realtimequiz', 'intro', null); // This file area hasn't itemid.
        $question->annotate_files('mod_realtimequiz', 'question', 'id');

        // Return the root element (realtimequiz), wrapped into standard activity structure.
        return $this->prepare_activity_structure($realtimequiz);
    }

}
