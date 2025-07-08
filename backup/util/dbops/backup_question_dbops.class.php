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

        // First step, get all the categories for the given context (course/module)
        // i.e. the whole context questions bank
        $contextcategories = $DB->get_records_menu('question_categories', ['contextid' => $contextid], '', 'id, parent');

        // Now, based in the annotated question bank entries, get all the categories they
        // belong to.
        $questioncategories = $DB->get_records_sql_menu(
            "SELECT DISTINCT qc2.id, qc2.parent
               FROM {question_categories} qc2
                    JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc2.id
                    JOIN {backup_ids_temp} bi ON bi.itemid = qbe.id
              WHERE bi.backupid = ?
                    AND bi.itemname = 'question_bank_entry'
                    AND qc2.contextid != ?",
            [$backupid, $contextid]
        );

        // These are all the question categories we want to include in the backup.
        $categories = $contextcategories + $questioncategories;
        // If we're not already including the parents of a category, add them in.
        foreach ($categories as $parentid) {
            if (!array_key_exists($parentid, $categories)) {
                $categories += self::get_parent_categories($parentid);
            }
        }
        // Insert annotations of the found categories.
        foreach (array_keys($categories) as $categoryid) {
            backup_structure_dbops::insert_backup_ids_record($backupid, 'question_category', $categoryid);
        }
        // For these categories, we want to include all questions.
        foreach (array_keys($contextcategories) as $categoryid) {
            backup_structure_dbops::insert_backup_ids_record($backupid, 'question_category_complete', $categoryid);
        }
        // For these categories, we only want to include the questions that have been annotated.
        // Exclude those where we're already including all questions.
        $partialcategories = array_diff(
            array_keys($questioncategories),
            array_keys($contextcategories),
        );
        foreach ($partialcategories as $categoryid) {
            backup_structure_dbops::insert_backup_ids_record($backupid, 'question_category_partial', $categoryid);
        }
    }

    /**
     * Recursively find the parents and ancestors of the given category
     *
     * @param int $categoryid The category we want to find parents for.
     * @return array id => parentid for each category
     */
    protected static function get_parent_categories(int $categoryid): array {
        global $DB;
        $parentcategories = [];
        $parentid = $DB->get_field('question_categories', 'parent', ['id' => $categoryid]);
        $parentcategories[$categoryid] = $parentid;
        // If this is not a top category, keep going.
        if ($parentid > 0) {
            array_merge($parentcategories, self::get_parent_categories($parentid));
        }
        return $parentcategories;
    }

    /**
     * Delete all the annotated questions present in backup_ids_temp
     */
    public static function delete_temp_questions($backupid) {
        global $DB;
        $DB->delete_records('backup_ids_temp', array('backupid' => $backupid, 'itemname' => 'question'));
    }
}
