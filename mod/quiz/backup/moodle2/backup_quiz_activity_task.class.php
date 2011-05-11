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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/backup/moodle2/backup_quiz_stepslib.php');


/**
 * quiz backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 *
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_quiz_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Generate the quiz.xml file containing all the quiz information
        // and annotating used questions
        $this->add_step(new backup_quiz_activity_structure_step('quiz_structure', 'quiz.xml'));

        // Note: Following  steps must be present
        // in all the activities using question banks (only quiz for now)
        // TODO: Specialise these step to a new subclass of backup_activity_task

        // Process all the annotated questions to calculate the question
        // categories needing to be included in backup for this activity
        // plus the categories belonging to the activity context itself
        $this->add_step(new backup_calculate_question_categories('activity_question_categories'));

        // Clean backup_temp_ids table from questions. We already
        // have used them to detect question_categories and aren't
        // needed anymore
        $this->add_step(new backup_delete_temp_questions('clean_temp_questions'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of quizzes
        $search="/(".$base."\/mod\/quiz\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@QUIZINDEX*$2@$', $content);

        // Link to quiz view by moduleid
        $search="/(".$base."\/mod\/quiz\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@QUIZVIEWBYID*$2@$', $content);

        // Link to quiz view by quizid
        $search="/(".$base."\/mod\/quiz\/view.php\?q\=)([0-9]+)/";
        $content= preg_replace($search, '$@QUIZVIEWBYQ*$2@$', $content);

        return $content;
    }
}
