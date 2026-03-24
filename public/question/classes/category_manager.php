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

namespace core_question;

use stdClass;
use core\exception\moodle_exception;
use core\context;

/**
 * Category manager class, used for CRUD operations on question categories and related utility methods.
 *
 * @copyright 2024 Catalyst IT Europe Ltd.
 * @author Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_question
 */
class category_manager {
    /**
     * Cached checks for managecategories permissions in each context.
     *
     * @var array $managedcontexts;
     */
    protected array $managedcontexts = [];

    /**
     * Deletes an existing question category.
     *
     * @param int $categoryid id of category to delete.
     */
    public function delete_category(int $categoryid): void {
        global $DB;
        $this->require_can_delete_category($categoryid);
        $category = $DB->get_record('question_categories', ['id' => $categoryid]);

        $transaction = $DB->start_delegated_transaction();
        // Send the children categories to live with their grandparent.
        $DB->set_field('question_categories', 'parent', $category->parent, ['parent' => $category->id]);

        // Finally delete the category itself.
        $DB->delete_records('question_categories', ['id' => $category->id]);

        // Log the deletion of this category.
        $event = \core\event\question_category_deleted::create_from_question_category_instance($category);
        $event->add_record_snapshot('question_categories', $category);
        $event->trigger();
        $transaction->allow_commit();
    }

    /**
     * Move questions and then delete the category.
     *
     * @param int $oldcat id of the old category.
     * @param int $newcat id of the new category.
     */
    public function move_questions_and_delete_category(int $oldcat, int $newcat): void {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $this->require_can_delete_category($oldcat);
        $this->move_questions($oldcat, $newcat);
        $this->delete_category($oldcat);
        $transaction->allow_commit();
    }

    /**
     * Checks whether the category is a "Top" category (with no parent).
     *
     * @param int $categoryid a category id.
     * @return bool
     * @throws \dml_exception
     */
    public function is_top_category(int $categoryid): bool {
        global $DB;
        return 0 == $DB->get_field('question_categories', 'parent', ['id' => $categoryid]);
    }

    /**
     * Checks whether this is the only child of a top category in a context.
     *
     * @param int $categoryid a category id.
     * @return bool
     * @throws \dml_exception
     */
    public function is_only_child_of_top_category_in_context(int $categoryid): bool {
        global $DB;
        return 1 == $DB->count_records_sql("
            SELECT count(siblingcategory.id)
              FROM {question_categories} thiscategory
              JOIN {question_categories} parentcategory ON thiscategory.parent = parentcategory.id
              JOIN {question_categories} siblingcategory ON siblingcategory.parent = thiscategory.parent
             WHERE thiscategory.id = ? AND parentcategory.parent = 0", [$categoryid]);
    }

    /**
     * Ensures that this user is allowed to delete this category.
     *
     * @param int $todelete a category id.
     * @throws \required_capability_exception
     * @throws \dml_exception|moodle_exception
     */
    public function require_can_delete_category(int $todelete): void {
        global $DB;
        if ($this->is_top_category($todelete)) {
            throw new moodle_exception('cannotdeletetopcat', 'question');
        } else if ($this->is_only_child_of_top_category_in_context($todelete)) {
            throw new moodle_exception('cannotdeletecate', 'question');
        } else {
            $contextid = $DB->get_field('question_categories', 'contextid', ['id' => $todelete], MUST_EXIST);
            $this->require_manage_category(context::instance_by_id($contextid));
        }
    }

    /**
     * Move questions to another category.
     *
     * @param int $oldcat id of the old category.
     * @param int $newcat id of the new category.
     * @throws \dml_exception
     */
    public function move_questions(int $oldcat, int $newcat): void {
        $questionids = $this->get_real_question_ids_in_category($oldcat);
        question_move_questions_to_category($questionids, $newcat);
    }

    /**
     * Check the user can manage categories in the given context.
     *
     * This caches a successful check in $this->managedcontexts in case we check the same context multiple times.
     *
     * @param context $context
     * @return void
     * @throws \required_capability_exception
     */
    public function require_manage_category(context $context): void {
        if (!array_key_exists($context->id, $this->managedcontexts)) {
            require_capability('moodle/question:managecategory', $context);
            $this->managedcontexts[$context->id] = true;
        }
    }

    /**
     * Check there is no question category with the given ID number in the given context.
     *
     * @param ?string $idnumber The ID number to look for.
     * @param int $contextid The context to check the categories in.
     * @param ?int $excludecategoryid If set, exclude this category from the check (e.g. if this is the one being edited).
     * @return bool
     * @throws \dml_exception
     */
    public function idnumber_is_unique_in_context(?string $idnumber, int $contextid, ?int $excludecategoryid = null): bool {
        global $DB;
        if (empty($idnumber)) {
            return true;
        }
        $where = 'idnumber = ? AND contextid = ?';
        $params = [$idnumber, $contextid];
        if ($excludecategoryid) {
            $where .= ' AND id != ?';
            $params[] = $excludecategoryid;
        }
        return !$DB->record_exists_select('question_categories', $where, $params);
    }

    /**
     * Create a new category.
     *
     * Data is expected to come from question_category_edit_form.
     *
     * By default redirects on success, unless $return is true.
     *
     * @param string $newparent 'categoryid,contextid' of the parent category.
     * @param string $newcategory the name.
     * @param string $newinfo the description.
     * @param string $newinfoformat description format. One of the FORMAT_ constants.
     * @param ?string $idnumber the idnumber. '' is converted to null.
     * @return int New category id.
     */
    public function add_category(
        string $newparent,
        string $newcategory,
        string $newinfo,
        string $newinfoformat = FORMAT_HTML,
        ?string $idnumber = null,
    ): int {
        global $DB;
        if (empty($newcategory)) {
            throw new moodle_exception('categorynamecantbeblank', 'question');
        }
        [$parentid, $contextid] = explode(',', $newparent);
        // ...moodle_form makes sure select element output is legal no need for further cleaning.
        $this->require_manage_category(context::instance_by_id($contextid));

        if ($parentid) {
            if (!($DB->get_field('question_categories', 'contextid', ['id' => $parentid]) == $contextid)) {
                throw new moodle_exception(
                    'cannotinsertquestioncatecontext',
                    'question',
                    '',
                    ['cat' => $newcategory, 'ctx' => $contextid],
                );
            }
        }

        if (!$this->idnumber_is_unique_in_context($idnumber, $contextid)) {
            throw new moodle_exception('idnumbertaken', 'error');
        }

        if ((string)$idnumber === '') {
            $idnumber = null;
        }

        $transaction = $DB->start_delegated_transaction();

        $cat = new stdClass();
        $cat->parent = $parentid;
        $cat->contextid = $contextid;
        $cat->name = $newcategory;
        $cat->info = $newinfo;
        $cat->infoformat = $newinfoformat;
        $cat->sortorder = $this->get_max_sortorder($parentid) + 1;
        $cat->stamp = make_unique_id_code();
        $cat->idnumber = $idnumber;
        $categoryid = $DB->insert_record("question_categories", $cat);

        // Log the creation of this category.
        $category = new stdClass();
        $category->id = $categoryid;
        $category->contextid = $contextid;
        $event = \core\event\question_category_created::create_from_question_category_instance($category);
        $event->trigger();
        $transaction->allow_commit();

        return $categoryid;
    }

    /**
     * Updates an existing category with given params.
     *
     * Warning! parameter order and meaning confusingly different from add_category in some ways!
     *
     * @param int $updateid id of the category to update.
     * @param string $newparent 'categoryid,contextid' of the parent category to set.
     * @param string $newname category name.
     * @param string $newinfo category description.
     * @param string $newinfoformat description format. One of the FORMAT_ constants.
     * @param ?string $idnumber the idnumber. '' is converted to null.
     * @param ?int $sortorder The updated sortorder. Not updated if null.
     */
    public function update_category(
        int $updateid,
        string $newparent,
        string $newname,
        ?string $newinfo = null,
        string $newinfoformat = FORMAT_HTML,
        ?string $idnumber = null,
        ?int $sortorder = null,
    ): void {
        global $DB, $CFG;
        require_once($CFG->libdir . '/questionlib.php');

        if (empty($newname)) {
            throw new moodle_exception('categorynamecantbeblank', 'question');
        }

        // Get the record we are updating.
        $oldcat = $DB->get_record('question_categories', ['id' => $updateid]);
        $lastcategoryinthiscontext = $this->is_only_child_of_top_category_in_context($updateid);

        if (!empty($newparent) && !$lastcategoryinthiscontext) {
            [$parentid, $tocontextid] = explode(',', $newparent);
        } else {
            $parentid = $oldcat->parent;
            $tocontextid = $oldcat->contextid;
        }

        if (is_null($newinfo)) {
            $newinfo = $oldcat->info;
        }

        // Check permissions.
        $fromcontext = context::instance_by_id($oldcat->contextid);
        $this->require_manage_category($fromcontext);

        // If moving to another context, check permissions some more, and confirm contextid,stamp uniqueness.
        $newstamprequired = false;
        if ($oldcat->contextid != $tocontextid) {
            $tocontext = context::instance_by_id($tocontextid);
            $this->require_manage_category($tocontext);

            // Confirm stamp uniqueness in the new context. If the stamp already exists, generate a new one.
            if ($DB->record_exists('question_categories', ['contextid' => $tocontextid, 'stamp' => $oldcat->stamp])) {
                $newstamprequired = true;
            }
        }

        if (!$this->idnumber_is_unique_in_context($idnumber, $tocontextid, $updateid)) {
            throw new moodle_exception('idnumbertaken', 'error');
        }

        if ((string)$idnumber === '') {
            $idnumber = null;
        }

        $transaction = $DB->start_delegated_transaction();

        // Update the category record.
        $cat = new stdClass();
        $cat->id = $updateid;
        $cat->name = $newname;
        $cat->info = $newinfo;
        $cat->infoformat = $newinfoformat;
        $cat->parent = $parentid;
        $cat->contextid = $tocontextid;
        $cat->idnumber = $idnumber;
        if ($newstamprequired) {
            $cat->stamp = make_unique_id_code();
        }
        if ($sortorder) {
            $cat->sortorder = $sortorder;
        }
        $DB->update_record('question_categories', $cat);
        // Update the set_reference records when moving a category to a different context.
        move_question_set_references($cat->id, $cat->id, $oldcat->contextid, $tocontextid);

        // Log the update of this category.
        $event = \core\event\question_category_updated::create_from_question_category_instance($cat);
        $event->trigger();

        if ($oldcat->contextid != $tocontextid) {
            // Moving to a new context. Must move files belonging to questions.
            question_move_category_to_context($cat->id, $oldcat->contextid, $tocontextid);
        }
        $transaction->allow_commit();
    }

    /**
     * Returns ids of the question in the given question category.
     *
     * This method only returns the real question. It does not include
     * subquestions of question types like multianswer.
     *
     * @param int $categoryid id of the category.
     * @return int[] array of question ids.
     */
    public function get_real_question_ids_in_category(int $categoryid): array {
        global $DB;

        $sql = "SELECT q.id
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid = :categoryid
                   AND (q.parent = 0 OR q.parent = q.id)";

        $questionids = $DB->get_records_sql($sql, ['categoryid' => $categoryid]);
        return array_keys($questionids);
    }

    /**
     * Get current max sort in a given parent
     *
     * @param int $parentid The ID of the parent category.
     * @return int current max sort order
     */
    public function get_max_sortorder(int $parentid): int {
        global $DB;
        $sql = "SELECT MAX(sortorder)
                  FROM {question_categories}
                 WHERE parent = :parent";
        $lastmax = $DB->get_field_sql($sql, ['parent' => $parentid]);
        return $lastmax ?? 0;
    }

    /**
     * Upgrade step to find question categories with the wrong parent category.
     *
     * This will find question categories that have a parent in a different context, and set the parent to the top category
     * of the current context. GROUP BY is to ensure we only get one result for each category, just in case we somehow end up
     * with multiple top-level categories in a context.
     *
     * This could occur before the fix for MDL-86300, where a course restore left question categories that were the child of a top
     * category with the original top category as the parent, rather than the new top category.
     *
     * @todo Deprecate in 6.0 MDL-87844 for Removal in 7.0 MDL-87845.
     */
    public static function fix_restored_category_parents(): void {
        global $DB;
        $categoriestofix = $DB->get_records_sql("
            SELECT qc.id as id, MIN(qc3.id) AS parent
              FROM {question_categories} qc
              JOIN {question_categories} qc2 ON qc2.id = qc.parent AND qc.contextid != qc2.contextid
              JOIN {question_categories} qc3 ON qc3.contextid = qc.contextid AND qc3.parent = 0
          GROUP BY qc.id
        ");
        foreach ($categoriestofix as $categorytofix) {
            $DB->update_record('question_categories', $categorytofix, true);
        }
    }
}
