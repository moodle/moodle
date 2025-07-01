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

namespace qbank_managecategories;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/questionlib.php");

use context;
use core_question\category_manager;
use core_question\local\bank\question_version_status;
use core_question\output\question_category_selector;
use moodle_exception;
use html_writer;

/**
 * Class helper contains all the library functions.
 *
 * Library functions used by qbank_managecategories.
 * This code is based on lib/questionlib.php by Martin Dougiamas.
 *
 * @package    qbank_managecategories
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Guillermo Gomez Arias <guillermogomez@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Name of this plugin.
     */
    const PLUGINNAME = 'qbank_managecategories';

    /**
     * Remove stale questions from a category.
     *
     * This finds and removes any old-style random questions (qtype = random),
     * or any questions that were deleted while they were in use by a quiz (status = hidden),
     * but those usages have since been removed.
     *
     * If a category only contains stale questions, the users are unable to delete the question
     * category unless they move those stale questions to another one category, but to them the
     * category may appear empty. The purpose of this function is to detect the questions that
     * may have gone stale and remove them.
     *
     * You will typically use this prior to checking if the category contains questions.
     *
     * @param int $categoryid The category ID.
     * @throws \dml_exception
     */
    public static function question_remove_stale_questions_from_category(int $categoryid): void {
        global $DB;

        $sql = "SELECT q.id
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid = :categoryid
                   AND (q.qtype = :qtype OR qv.status = :status)";

        $params = ['categoryid' => $categoryid, 'qtype' => 'random', 'status' => question_version_status::QUESTION_STATUS_HIDDEN];
        $questions = $DB->get_records_sql($sql, $params);
        foreach ($questions as $question) {
            // The function question_delete_question does not delete questions in use.
            question_delete_question($question->id);
        }
    }

    /**
     * Checks whether this is the only child of a top category in a context.
     *
     * @param int $categoryid a category id.
     * @return bool
     * @throws \dml_exception
     * @deprecated Since Moodle 4.5. Use core_question\category_manager::is_only_child_of_top_category_in_context instead.
     * @todo Final removal in Moodle 6.0 MDL-80804
     */
    #[\core\attribute\deprecated(
        'core_question\category_manager::is_only_child_of_top_category_in_context',
        since: 4.5,
        reason: 'Moved to core namespace',
        mdl: 'MDL-72397'
    )]
    public static function question_is_only_child_of_top_category_in_context(int $categoryid): bool {
        \core\deprecation::emit_deprecation([__CLASS__, __FUNCTION__]);
        $manager = new category_manager();
        return $manager->is_only_child_of_top_category_in_context($categoryid);
    }

    /**
     * Checks whether the category is a "Top" category (with no parent).
     *
     * @param int $categoryid a category id.
     * @return bool
     * @throws \dml_exception
     * @deprecated Since Moodle 4.5. Use core_question\category_manager::is_top_category instead.
     * @todo Final removal in Moodle 6.0 MDL-80804.
     */
    #[\core\attribute\deprecated(
        'core_question\category_manager::is_top_category',
        since: 4.5,
        reason: 'Moved to core namespace',
        mdl: 'MDL-72397'
    )]
    public static function question_is_top_category(int $categoryid): bool {
        \core\deprecation::emit_deprecation([__CLASS__, __FUNCTION__]);
        $manager = new category_manager();
        return $manager->is_top_category($categoryid);
    }

    /**
     * Ensures that this user is allowed to delete this category.
     *
     * @param int $todelete a category id.
     * @throws \required_capability_exception
     * @throws \dml_exception|moodle_exception
     * @deprecated Since Moodle 4.5. Use core_question\category_manager::can_delete_category instead.
     * @todo Final removal in Moodle 6.0 MDL-80804.
     */
    #[\core\attribute\deprecated(
        'core_question\category_manager::can_delete_category',
        since: 4.5,
        reason: 'Moved to core namespace',
        mdl: 'MDL-72397'
    )]
    public static function question_can_delete_cat(int $todelete): void {
        \core\deprecation::emit_deprecation([__CLASS__, __FUNCTION__]);
        $manager = new category_manager();
        $manager->require_can_delete_category($todelete);
    }

    /**
     * Only for the use of add_indented_names().
     *
     * Recursively adds an indentedname field to each category, starting with the category
     * with id $id, and dealing with that category and all its children, and
     * return a new array, with those categories in the right order.
     *
     * @param array $categories an array of categories which has had childids
     *          fields added by flatten_category_tree(). Passed by reference for
     *          performance only. It is not modfied.
     * @param int $id the category to start the indenting process from.
     * @param int $depth the indent depth. Used in recursive calls.
     * @param int $nochildrenof
     * @return array a new array of categories, in the right order for the tree.
     */
    public static function flatten_category_tree(array &$categories, $id, int $depth = 0, int $nochildrenof = -1): array {

        // Indent the name of this category.
        $newcategories = [];
        $newcategories[$id] = $categories[$id];
        $newcategories[$id]->indentedname = str_repeat('&nbsp;&nbsp;&nbsp;', $depth) .
            $categories[$id]->name;

        // Recursively indent the children.
        foreach ($categories[$id]->childids as $childid) {
            if ($childid != $nochildrenof) {
                $newcategories = $newcategories + self::flatten_category_tree(
                    $categories,
                    $childid,
                    $depth + 1,
                    $nochildrenof,
                );
            }
        }

        // Remove the childids array that were temporarily added.
        unset($newcategories[$id]->childids);

        return $newcategories;
    }

    /**
     * Format categories into an indented list reflecting the tree structure.
     *
     * @param array $categories An array of category objects, for example from the.
     * @param int $nochildrenof
     * @return array The formatted list of categories.
     */
    public static function add_indented_names(array $categories, int $nochildrenof = -1): array {

        // Add an array to each category to hold the child category ids. This array
        // will be removed again by flatten_category_tree(). It should not be used
        // outside these two functions.
        foreach (array_keys($categories) as $id) {
            $categories[$id]->childids = [];
        }

        // Build the tree structure, and record which categories are top-level.
        // We have to be careful, because the categories array may include published
        // categories from other courses, but not their parents.
        $toplevelcategoryids = [];
        foreach (array_keys($categories) as $id) {
            if (
                !empty($categories[$id]->parent) &&
                array_key_exists($categories[$id]->parent, $categories)
            ) {
                $categories[$categories[$id]->parent]->childids[] = $id;
            } else {
                $toplevelcategoryids[] = $id;
            }
        }

        // Flatten the tree to and add the indents.
        $newcategories = [];
        foreach ($toplevelcategoryids as $id) {
            $newcategories = $newcategories + self::flatten_category_tree(
                $categories,
                $id,
                0,
                $nochildrenof,
            );
        }

        return $newcategories;
    }

    /**
     * Output a select menu of question categories.
     *
     * Categories from this course and (optionally) published categories from other courses
     * are included. Optionally, only categories the current user may edit can be included.
     *
     * @param array $contexts
     * @param bool $top
     * @param int $currentcat
     * @param string $selected optionally, the id of a category to be selected by
     *      default in the dropdown.
     * @param int $nochildrenof
     * @param bool $return to return the string of the select menu or echo that from the method
     * @return ?string The HTML, or null if the $return is false.
     * @throws \coding_exception|\dml_exception
     */
    public static function question_category_select_menu(
        array $contexts,
        bool $top = false,
        int $currentcat = 0,
        string $selected = "",
        int $nochildrenof = -1,
        bool $return = false,
    ): ?string {
        $categoriesarray = self::question_category_options(
            $contexts,
            $top,
            $currentcat,
            false,
            $nochildrenof,
            false,
        );
        $choose = '';
        $options = [];
        foreach ($categoriesarray as $group => $opts) {
            $options[] = [$group => $opts];
        }
        $outputhtml = html_writer::label(
            get_string('questioncategory', 'core_question'),
            'id_movetocategory',
            false,
            ['class' => 'accesshide'],
        );
        $attrs = [
            'id' => 'id_movetocategory',
            'class' => 'form-select',
            'data-action' => 'toggle',
            'data-togglegroup' => 'qbank',
            'data-toggle' => 'action',
            'disabled' => false,
        ];
        $outputhtml .= html_writer::select($options, 'category', $selected, $choose, $attrs);
        if ($return) {
            return $outputhtml;
        } else {
            echo $outputhtml;
            return null;
        }
    }

    /**
     * Get all the category objects, including a count of the number of questions in that category,
     * for all the categories in the lists $contexts.
     *
     * @param string $contexts comma separated list of contextids
     * @param string $sortorder used as the ORDER BY clause in the select statement.
     * @param bool $top Whether to return the top categories or not.
     * @param int $showallversions 1 to show all versions not only the latest.
     * @return array of category objects.
     * @throws \dml_exception
     */
    public static function get_categories_for_contexts(
        string $contexts,
        string $sortorder = 'parent, sortorder, name ASC',
        bool $top = false,
        int $showallversions = 0,
    ): array {
        return (new question_category_selector())->get_categories_for_contexts($contexts, $sortorder, $top, $showallversions);
    }

    /**
     * Output an array of question categories.
     *
     * @param array $contexts The list of contexts.
     * @param bool $top Whether to return the top categories or not.
     * @param int $currentcat
     * @param bool $popupform
     * @param int $nochildrenof
     * @param bool $escapecontextnames Whether the returned name of the thing is to be HTML escaped or not.
     * @return array
     * @throws \coding_exception|\dml_exception
     */
    public static function question_category_options(
        array $contexts,
        bool $top = false,
        int $currentcat = 0,
        bool $popupform = false,
        int $nochildrenof = -1,
        bool $escapecontextnames = true,
    ): array {
        return (new question_category_selector())->question_category_options(
            $contexts,
            $top,
            $currentcat,
            $popupform,
            $nochildrenof,
            $escapecontextnames,
        );
    }

    /**
     * Add context in categories key.
     *
     * @param array $categories The list of categories.
     * @return array
     */
    public static function question_add_context_in_key(array $categories): array {
        return (new question_category_selector())->question_add_context_in_key($categories);
    }

    /**
     * Finds top categories in the given categories hierarchy and replace their name with a proper localised string.
     *
     * @param array $categories An array of question categories.
     * @param bool $escape Whether the returned name of the thing is to be HTML escaped or not.
     * @return array The same question category list given to the function, with the top category names being translated.
     * @throws \coding_exception
     */
    public static function question_fix_top_names(array $categories, bool $escape = true): array {
        return (new question_category_selector())->question_fix_top_names($categories, $escape);
    }

    /**
     * Combine id and context id for a question category
     *
     * @param \stdClass $category a category to extract its id and context id
     * @return string the combined string
     */
    public static function combine_id_context(\stdClass $category): string {
        return $category->id . ',' . $category->contextid;
    }
}
