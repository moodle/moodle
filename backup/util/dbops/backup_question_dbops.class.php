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
 * @subpackage backup-dbops
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing DB support to the questions backup stuff
 *
 * This class contains various static methods available for all the DB operations
 * performed by questions stuff
 *
 * TODO: Finish phpdocs
 */
abstract class backup_question_dbops extends backup_dbops {

    /**
     * Calculates all the question_categories to be included
     * in backup, based in a given context (course/module) and
     * the already annotated questions present in backup_ids_temp
     */
    public static function calculate_question_categories($backupid, $contextid) {
        global $DB;

        // First step, annotate all the categories for the given context (course/module)
        // i.e. the whole context questions bank
        $DB->execute("INSERT INTO {backup_ids_temp} (backupid, itemname, itemid)
                      SELECT ?, 'question_category', id
                        FROM {question_categories}
                       WHERE contextid = ?", array($backupid, $contextid));

        // Now, based in the annotated questions, annotate all the categories they
        // belong to (whole context question banks too)
        // First, get all the contexts we are going to save their question bank (no matter
        // where they are in the contexts hierarchy, transversals... whatever)
        $contexts = $DB->get_fieldset_sql("SELECT DISTINCT qc2.contextid
                                                 FROM {question_categories} qc2
                                                 JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc2.id
                                                 JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                                                 JOIN {question} q ON q.id = qv.questionid
                                                 JOIN {backup_ids_temp} bi ON bi.itemid = q.id
                                                WHERE bi.backupid = ?
                                                  AND bi.itemname = 'question'
                                                  AND qc2.contextid != ?", array($backupid, $contextid));

        // Calculate and get the set reference records.
        $setreferencecontexts = $DB->get_fieldset_sql("
        SELECT DISTINCT qc.contextid
          FROM {question_categories} qc
          JOIN {question_set_references} qsr ON qsr.questionscontextid = qc.contextid
         WHERE qsr.usingcontextid = ?", [$contextid]);
        foreach ($setreferencecontexts as $setreferencecontext) {
            if (!in_array($setreferencecontext, $contexts) && (int)$setreferencecontext !== $contextid) {
                $contexts [] = $setreferencecontext;
            }
        }

        // Calculate the get the reference records.
        $referencecontexts = $DB->get_fieldset_sql("
        SELECT DISTINCT qc.contextid
         FROM {question_categories} qc
         JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
         JOIN {question_references} qr ON qr.questionbankentryid = qbe.id
        WHERE qr.usingcontextid =?", [$contextid]);
        foreach ($referencecontexts as $referencecontext) {
            if (!in_array($referencecontext, $contexts) && (int)$referencecontext !== $contextid) {
                $contexts [] = $referencecontext;
            }
        }
        // And now, simply insert all the question categories (complete question bank)
        // for those contexts if we have found any
        if ($contexts) {
            list($contextssql, $contextparams) = $DB->get_in_or_equal($contexts);
            $params = array_merge(array($backupid), $contextparams);
            $DB->execute("INSERT INTO {backup_ids_temp} (backupid, itemname, itemid)
                          SELECT ?, 'question_category', id
                            FROM {question_categories}
                           WHERE contextid $contextssql", $params);
        }
    }

    /**
     * Delete all the annotated questions present in backup_ids_temp
     */
    public static function delete_temp_questions($backupid) {
        global $DB;
        $DB->delete_records('backup_ids_temp', array('backupid' => $backupid, 'itemname' => 'question'));
    }
}
