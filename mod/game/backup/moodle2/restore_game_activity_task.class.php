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
 * Restores a game
 *
 * @package mod_game
 * @subpackage backup-moodle2
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/game/backup/moodle2/restore_game_stepslib.php'); // Because it exists (must).

/**
 * game restore task that provides all the settings and steps to perform one complete restore of the activity
 *
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_game_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Game only has one structure step.
        $this->add_step(new restore_game_activity_structure_step('game_structure', 'game.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('game', array('toptext'), 'game');
        $contents[] = new restore_decode_content('game', array('bottomtext'), 'game');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * game logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('game', 'add', 'view.php?id={course_module}', '{game}');
        $rules[] = new restore_log_rule('game', 'update', 'view.php?id={course_module}', '{game}');
        $rules[] = new restore_log_rule('game', 'view', 'view.php?id={course_module}', '{game}');
        $rules[] = new restore_log_rule('game', 'choose', 'view.php?id={course_module}', '{game}');
        $rules[] = new restore_log_rule('game', 'choose again', 'view.php?id={course_module}', '{game}');
        $rules[] = new restore_log_rule('game', 'report', 'report.php?id={course_module}', '{game}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        // Fix old wrong uses (missing extension).
        $rules[] = new restore_log_rule('game', 'view all', 'index?id={course}', null,
                                        null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('game', 'view all', 'index.php?id={course}', null);

        return $rules;
    }

    /**
     * Do something at end of restore.
     */
    public function after_restore() {
        global $DB;

        // Get the blockid.
        $gameid = $this->get_activityid();

        // Extract Game configdata and update it to point to the new glossary.
        $rec = $DB->get_record_select( 'game', 'id='.$gameid,
            null, 'id,quizid,glossaryid,glossarycategoryid,questioncategoryid,bookid,glossaryid2,glossarycategoryid2');

        $restoreid = $this->get_restoreid();
        $ret = restore_dbops::get_backup_ids_record($restoreid, 'quiz', $rec->quizid);
        if ($ret != false) {
            $rec->quizid = $ret->newitemid;
        }

        $ret = restore_dbops::get_backup_ids_record($restoreid, 'glossary', $rec->glossaryid);
        if ($ret != false) {
            $rec->glossaryid = $ret->newitemid;
        }

        $ret = restore_dbops::get_backup_ids_record($restoreid, 'glossary_categories', $rec->glossarycategoryid);
        if ($ret != false) {
            $rec->glossarycategoryid = $ret->newitemid;
        }

        $ret = restore_dbops::get_backup_ids_record($restoreid, 'question_categories', $rec->questioncategoryid);
        if ($ret != false) {
            $rec->questioncategoryid = $ret->newitemid;
        }

        $ret = restore_dbops::get_backup_ids_record($restoreid, 'book', $rec->bookid);
        if ($ret != false) {
            $rec->bookid = $ret->newitemid;
        }

        $ret = restore_dbops::get_backup_ids_record($restoreid, 'glossary', $rec->glossaryid2);
        if ($ret != false) {
            $rec->glossaryid2 = $ret->newitemid;
        }

        $ret = restore_dbops::get_backup_ids_record($restoreid, 'glossary_categories', $rec->glossarycategoryid);
        if ($ret != false) {
            $rec->glossarycategoryid = $ret->newitemid;
        }

        $DB->update_record( 'game', $rec);

        // Read game_repetitions.
        $recs = $DB->get_records_select( 'game_repetitions', 'gameid='.$gameid, null, '',
                'id,questionid,glossaryentryid');
        if ($recs != false) {
            foreach ($recs as $rec) {
                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'question', $rec->questionid);
                if ($ret != false) {
                    $rec->questionid = $ret->newitemid;
                }

                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'glossary_entry', $rec->glossaryentryid);
                if ($ret != false) {
                    $rec->glossaryentryid = $ret->newitemid;
                }

                $DB->update_record( 'game_repetitions', $rec);
            }
        }

        // Read game_queries.
        $recs = $DB->get_records_select( 'game_queries', 'gameid='.$gameid, null, '',
                'id,questionid,glossaryentryid,answerid');
        if ($recs != false) {
            foreach ($recs as $rec) {
                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'question', $rec->questionid);
                if ($ret != false) {
                    $rec->questionid = $ret->newitemid;
                }
                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'glossary_entry', $rec->glossaryentryid);
                if ($ret != false) {
                    $rec->glossaryentryid = $ret->newitemid;
                }

                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'question_answers', $rec->glossaryentryid);
                if ($ret != false) {
                    $rec->answerid = $ret->newitemid;
                }

                $DB->update_record( 'game_queries', $rec);
            }
        }

        // Read bookquiz.
        $recs = $DB->get_records_select( 'game_bookquiz', 'id='.$gameid, null, '', 'id,lastchapterid');
        if ($recs != false) {
            foreach ($recs as $rec) {
                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'book_chapters', $rec->lastchapterid);
                if ($ret != false) {
                    $rec->lastchapterid = $ret->newitemid;
                }

                $DB->update_record( 'game_bookquiz', $rec);
            }
        }

        // Read bookquiz_chapters.
        $sql = "SELECT gbc.* ".
            "FROM {game_bookquiz_chapters} gbc LEFT JOIN {game_attempts} a ON gbc.attemptid = a.id".
            " WHERE a.gameid=$gameid";
        $recs = $DB->get_records_sql( $sql);
        if ($recs != false) {
            foreach ($recs as $rec) {
                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'book_chapters', $rec->chapterid);
                if ($ret != false) {
                    $rec->chapterid = $ret->newitemid;
                }
                $DB->update_record( 'game_bookquiz_chapter', $rec);
            }
        }

        // Read bookquiz_questions.
        $recs = $DB->get_records_select( 'game_bookquiz_questions', 'id='.$gameid, null, '', 'id,chapterid,questioncategoryid');
        if ($recs != false) {
            foreach ($recs as $rec) {
                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'book_chapters', $rec->chapterid);
                if ($ret != false) {
                    $rec->chapterid = $ret->newitemid;
                }

                $ret = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'book_chapters', $rec->questioncategoryid);
                if ($ret != false) {
                    $rec->questioncategoryid = $ret->newitemid;
                }

                $DB->update_record( 'game_bookquiz_questions', $rec);
            }
        }
    }
}
