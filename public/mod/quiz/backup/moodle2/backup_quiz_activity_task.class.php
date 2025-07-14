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
 * Defines backup_quiz_activity_task class
 *
 * @package     mod_quiz
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/backup/moodle2/backup_quiz_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the Quiz instance
 *
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_quiz_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines backup steps to store the instance data and required questions
     */
    protected function define_my_steps() {
        // Generate the quiz.xml file containing all the quiz information
        // and annotating used questions.
        $this->add_step(new backup_quiz_activity_structure_step('quiz_structure', 'quiz.xml'));

        // Note: Following  steps must be present
        // in all the activities using question banks (only quiz for now)
        // TODO: Specialise these step to a new subclass of backup_activity_task.

        // Process all the annotated questions to calculate the question
        // categories needing to be included in backup for this activity
        // plus the categories belonging to the activity context itself.
        $this->add_step(new backup_calculate_question_categories('activity_question_categories'));

        // Clean backup_temp_ids table from questions. We already
        // have used them to detect question_categories and aren't
        // needed anymore.
        $this->add_step(new backup_delete_temp_questions('clean_temp_questions'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of quizzes.
        $search="/(".$base."\/mod\/quiz\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@QUIZINDEX*$2@$', $content);

        // Link to quiz view by moduleid.
        $search="/(".$base."\/mod\/quiz\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@QUIZVIEWBYID*$2@$', $content);

        // Link to quiz view by quizid.
        $search="/(".$base."\/mod\/quiz\/view.php\?q\=)([0-9]+)/";
        $content= preg_replace($search, '$@QUIZVIEWBYQ*$2@$', $content);

        return $content;
    }
}
