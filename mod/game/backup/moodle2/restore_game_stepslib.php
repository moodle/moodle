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
 * Define all the restore steps that will be used by the restore_game_activity_task
 *
 * @package mod_game
 * @subpackage backup-moodle2
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/game/locallib.php');

/**
 * Structure step to restore one game activity
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_game_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the neeeded structures.
     *
     * @copyright 2007 Vasilis Daloukas
     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('game', '/activity/game');
        $paths[] = new restore_path_element('game_export_html', '/activity/game/game_export_htmls/game_export_html');
        $paths[] = new restore_path_element('game_export_javame', '/activity/game/game_export_htmls/game_export_javame');
        $paths[] = new restore_path_element(
            'game_bookquiz_question', '/activity/game/game_bookquiz_questions/game_bookquiz_question');
        if ($userinfo) {
            $paths[] = new restore_path_element('game_grade', '/activity/game/game_grades/game_grade');
            $paths[] = new restore_path_element('game_repetition', '/activity/game/game_repetiotions/game_repetition');
            $paths[] = new restore_path_element('game_attempt', '/activity/game/game_attempts/game_attempt');
            $paths[] = new restore_path_element('game_query', '/activity/game/game_attempts/game_attempt/game_queries/game_query');

            // The games.
            $paths[] = new restore_path_element('game_bookquiz', '/activity/game/game_attempts/game_attempt/game_bookquiz');
            $paths[] = new restore_path_element('game_bookquiz_chapter',
                '/activity/game/game_attempts/game_attempt/game_bookquiz_chapters/game_bookquiz_chapter');
            $paths[] = new restore_path_element('game_cross', '/activity/game/game_attempts/game_attempt/game_cross');
            $paths[] = new restore_path_element('game_cryptex', '/activity/game/game_attempts/game_attempt/game_cryptex');
            $paths[] = new restore_path_element('game_hangman', '/activity/game/game_attempts/game_attempt/game_hangman');
            $paths[] = new restore_path_element('game_hiddenpicture',
            '/activity/game/game_attempts/game_attempt/game_hiddenpicture');
            $paths[] = new restore_path_element('game_millionaire', '/activity/game/game_attempts/game_attempt/game_millionaire');
            $paths[] = new restore_path_element('game_snake', '/activity/game/game_attempts/game_attempt/game_snake');
            $paths[] = new restore_path_element('game_sudoku', '/activity/game/game_attempts/game_attempt/game_sudoku');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restores the game table.
     *
     * @param stdClass $data
     */
    protected function process_game($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the game record.
        $newitemid = $DB->insert_record('game', $data);

        $this->set_mapping('game', $oldid, $newitemid);

        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restores the game_export_html table.
     *
     * @param stdClass $data
     */
    protected function process_game_export_html($data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game');
        if ($data->id != 0) {
            $DB->insert_record('game_export_html', $data);
        }
    }

    /**
     * Restores the game_export_javame table.
     *
     * @param stdClass $data
     */
    protected function process_game_export_javame( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game');
        if ($data->id != 0) {
            $DB->insert_record('game_export_javame', $data);
        }
    }

    /**
     * Restores the game_grades table.
     *
     * @param stdClass $data
     */
    protected function process_game_grade( $data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->gameid = $this->get_new_parentid('game');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $DB->insert_record('game_grades', $data);
    }

    /**
     * Restores the game_repetitions table.
     *
     * @param stdClass $data
     */
    protected function process_game_repetition( $data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->gameid = $this->get_new_parentid('game');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->questionid = $this->get_mappingid('question', $data->questionid);
        $data->glossaryentrydid = $this->get_mappingid('glossary_entries', $data->questionid);

        $DB->insert_record('game_repetitions', $data);
    }

    /**
     * Restores the game_attempts table.
     *
     * @param stdClass $data
     */
    protected function process_game_attempt( $data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->gameid = $this->get_new_parentid('game');
        $data->userid = $this->get_mappingid('user', $data->userid);

        if (!isset( $data->timestart)) {
            $data->timestart = 0;
        }
        if (!isset( $data->timefinish)) {
            $data->timefinish = 0;
        }
        if (!isset( $data->timelastattempt)) {
            $data->timelastattempt = 0;
        }

        $data->timestart = $this->apply_date_offset($data->timestart);
        $data->timefinish = $this->apply_date_offset($data->timefinish);
        $data->timelastattempt = $this->apply_date_offset($data->timelastattempt);

        $newitemid = $DB->insert_record('game_attempts', $data);
        $this->set_mapping('game_attempt', $oldid, $newitemid);
    }

    /**
     * Restores the game_queries table.
     *
     * @param stdClass $data
     */
    protected function process_game_query( $data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->gameid = $this->get_new_parentid('game');
        $data->attemptid = $this->get_new_parentid('game_attempt');

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->questionid = $this->get_mappingid('question', $data->questionid);
        $data->glossaryentryid = $this->get_mappingid('glossary_entry', $data->glossaryentryid);
        $data->answerid = $this->get_mappingid('question_answers', $data->answerid);

        $newitemid = $DB->insert_record('game_queries', $data);

        $this->set_mapping('game_queries', $oldid, $newitemid);
    }

    /**
     * Restores the game_bookquiz table.
     *
     * @param stdClass $data
     */
    protected function process_game_bookquiz( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game');
        $data->lastchapterid = $this->get_mappingid('book_chapters', $data->lastchapterid);

        $DB->insert_record('game_bookquiz', $data);
    }

    /**
     * Restores the game_bookauiz_chapters table.
     *
     * @param stdClass $data
     */
    protected function process_game_bookquiz_chapter( $data) {
        global $DB;

        $data = (object)$data;

        $data->attemptid = $this->get_new_parentid('game_attempts');
        $data->chapterid = $this->get_mappingid('book_chapters', $data->chapterid);

        $DB->insert_record('game_bookquiz_chapters', $data);
    }

    /**
     * Restores the game_bookquiz_questions table.
     *
     * @param stdClass $data
     */
    protected function process_game_bookquiz_question( $data) {
        global $DB;

        $data = (object)$data;

        $data->gameid = $this->get_new_parentid('game');
        $data->chapterid = $this->get_mappingid('book_chapters', $data->chapterid);
        $data->questioncategoryid = $this->get_mappingid('question_category', $data->questioncategoryid);

        $DB->insert_record('game_bookquiz_questions', $data);
    }

    /**
     * Restores the game_cross table.
     *
     * @param stdClass $data
     */
    protected function process_game_cross( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game_attempt');

        game_insert_record( 'game_cross', $data);
    }

    /**
     * Restores the game_cryptex table.
     *
     * @param stdClass $data
     */
    protected function process_game_cryptex( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game_attempt');

        game_insert_record( 'game_cryptex', $data);
    }

    /**
     * Restores the game_hangman table.
     *
     * @param stdClass $data
     */
    protected function process_game_hangman( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game_attempt');
        $data->queryid = $this->get_mappingid('game_queries', $data->queryid);

        game_insert_record( 'game_hangman', $data);
    }

    /**
     * Restores the game_hiddenpicture table.
     *
     * @param stdClass $data
     */
    protected function process_game_hiddenpicture( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game_attempt');

        game_insert_record( 'game_hiddenpicture', $data);
    }

    /**
     * Restores the game_millionaire table.
     *
     * @param stdClass $data
     */
    protected function process_game_millionaire( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game_attempt');
        $data->queryid = $this->get_mappingid('game_queries', $data->queryid);

        game_insert_record( 'game_millionaire', $data);
    }

    /**
     * Restores the game_snakes table.
     *
     * @param stdClass $data
     */
    protected function process_game_snake( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_mappingid('game_attempt', $data->id);
        $data->queryid = $this->get_mappingid('game_queries', $data->queryid);

        game_insert_record( 'game_snakes', $data);
    }

    /**
     * Restores the game_sudoku table.
     *
     * @param stdClass $data
     */
    protected function process_game_sudoku( $data) {
        global $DB;

        $data = (object)$data;

        $data->id = $this->get_new_parentid('game_attempt');

        game_insert_record( 'game_sudoku', $data);
    }

    /**
     * Add Game related files, no need to match by itemname (just internally handled context).
     */
    protected function after_execute() {
        $this->add_related_files('mod_game', 'snakes_file', null);
        $this->add_related_files('mod_game', 'snakes_board', null);
    }
}
