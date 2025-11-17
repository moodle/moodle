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

namespace mod_qbank\task;

use context_system;
use core\context;
use core\task\adhoc_task;
use core\task\manager;
use core_course_category;
use core_question\local\bank\question_bank_helper;
use stdClass;

/**
 * This script transfers question categories at CONTEXT_SITE, CONTEXT_COURSE, & CONTEXT_COURSECAT to a new qbank instance
 * context.
 *
 * Firstly, it finds any question categories where questions are not being used and deletes them, including questions.
 *
 * Then for any remaining, if it is at course level context, it creates a mod_qbank instance taking the course name
 * and moves the category there including subcategories, files and tags.
 *
 * If the original question category context was at system context, then it creates a mod_qbank instance on the site course i.e.
 * front page and moves the category & sub categories there, along with its files and tags.
 *
 * If the original question category context was a course category context, then it creates a course in that category,
 * taking the category name. Then it creates a mod_qbank instance in that course and moves the category & sub categories
 * there, along with files and tags belonging to those categories.
 *
 * @package    mod_qbank
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transfer_question_categories extends adhoc_task {

    /**
     * @var array a cache [ context id => question category ] of the top category in each context.
     * Used by get_top_category_id_for_context() to avoid repeated DB queries.
     * 0 is cached if this context id has no corresponding top category.
     */
    private array $topcategorycache = [];

    #[\Override]
    public function execute(): void {

        global $DB, $CFG;

        require_once($CFG->dirroot . '/course/modlib.php');
        require_once($CFG->libdir . '/questionlib.php');

        $this->fix_wrong_parents();

        $recordset = $DB->get_recordset('question_categories', ['parent' => 0]);

        foreach ($recordset as $oldtopcategory) {

            if (!$oldcontext = context::instance_by_id($oldtopcategory->contextid, IGNORE_MISSING)) {
                // That context does not exist anymore, we will treat these as if they were at site context level.
                $oldcontext = context_system::instance();
            }

            $trans = $DB->start_delegated_transaction();

            // Remove any unused questions if they are marked as deleted.
            // Also, if a category contained questions which were all unusable then delete it as well.
            $subcategories = $DB->get_records_select('question_categories',
                'parent <> 0 AND contextid = :contextid',
                ['contextid' => $oldtopcategory->contextid]
            );
            // This gives us categories in parent -> child order so array_reverse it,
            // because we should process stale categories from the bottom up.
            $subcategories = array_reverse(sort_categories_by_tree($subcategories, $oldtopcategory->id));
            foreach ($subcategories as $subcategory) {
                \qbank_managecategories\helper::question_remove_stale_questions_from_category($subcategory->id);
                if ($this->question_category_is_empty($subcategory->id)) {
                    question_category_delete_safe($subcategory);
                }
            }

            // If the top category no longer has any subcategories, because they only contained stale questions,
            // delete the top category and stop here without creating a new qbank.
            if (!$DB->record_exists('question_categories', ['parent' => $oldtopcategory->id])) {
                $DB->delete_records('question_categories', ['id' => $oldtopcategory->id]);
                $trans->allow_commit();
                continue;
            }

            // We don't want to transfer any categories at valid contexts i.e. quiz modules.
            if ($oldcontext->contextlevel === CONTEXT_MODULE) {
                $trans->allow_commit();
                continue;
            }

            // Category is in use so let's process it. Firstly, a course and mod instance is needed.
            switch ($oldcontext->contextlevel) {
                case CONTEXT_SYSTEM:
                    $course = get_site();
                    $bankname = question_bank_helper::get_bank_name_string('systembank', 'question');
                    break;
                case CONTEXT_COURSECAT:
                    $coursecategory = core_course_category::get($oldcontext->instanceid);
                    $courseshortname = "$coursecategory->name-$coursecategory->id";
                    $course = $this->create_course($coursecategory, $courseshortname);
                    $bankname = question_bank_helper::get_bank_name_string('sharedbank', 'mod_qbank', $coursecategory->name);
                    break;
                case CONTEXT_COURSE:
                    $course = get_course($oldcontext->instanceid);
                    $bankname = question_bank_helper::get_bank_name_string('sharedbank', 'mod_qbank', $course->shortname);
                    break;
                default:
                    // This shouldn't be possible, so we can't really transfer it.
                    // We should commit any pre-transfer category cleanup though.
                    $trans->allow_commit();
                    continue 2;
            }

            if (!$newmod = question_bank_helper::get_default_open_instance_system_type($course)) {
                $newmod = question_bank_helper::create_default_open_instance($course, $bankname, question_bank_helper::TYPE_SYSTEM);
            }

            // We have our new mod instance, now move all the subcategories of the old 'top' category to this new context.
            $movedcategories = $this->move_question_category($oldtopcategory, $newmod->context);

            // Create a set of new tasks to update the questions in each category to the new contexts.
            // The category itself is already in the new context. We record the old context
            // so we know where to move files and tags from.
            foreach ($movedcategories as $categoryid) {
                $task = new transfer_questions();
                $task->set_custom_data(['categoryid' => $categoryid, 'contextid' => $oldtopcategory->contextid]);
                manager::queue_adhoc_task($task);
            }

            // Job done, lets delete the old 'top' category.
            $DB->delete_records('question_categories', ['id' => $oldtopcategory->id]);
            $trans->allow_commit();
        }

        $recordset->close();
    }

    /**
     * Wrapper for \create_course.
     *
     * @param core_course_category $coursecategory
     * @param string $shortname
     * @return stdClass
     */
    protected function create_course(core_course_category $coursecategory, string $shortname): stdClass {
        $data = (object) [
            'enablecompletion' => 0,
            'fullname' => get_string('coursecategory', 'mod_qbank', $coursecategory->name),
            'shortname' => $shortname,
            'category' => $coursecategory->id,
        ];
        return create_course($data);
    }

    /**
     * Create a new 'Top' category in our new context and move the old categories descendents beneath it.
     *
     * @param stdClass $oldtopcategory The old 'Top' category that we are moving.
     * @param context\module $newcontext The context we are moving our category to.
     * @return int[] The IDs of all categories moved to the new context.
     */
    protected function move_question_category(stdClass $oldtopcategory, context\module $newcontext): array {
        global $DB;

        $newtopcategory = question_get_top_category($newcontext->id, true);

        move_question_set_references($oldtopcategory->id, $newtopcategory->id, $oldtopcategory->contextid, $newcontext->id, true);

        // This function moves subcategories, so we have to start at the top.
        $movedcategories = $this->move_subcategories_to_context($oldtopcategory->id, $newcontext);

        // Move the parent from the old top category to the new one.
        $DB->set_field('question_categories', 'parent', $newtopcategory->id, ['parent' => $oldtopcategory->id]);

        return $movedcategories;
    }

    /**
     * Recursively update the contextid for all subcategories of the given category.
     *
     * @param int $categoryid The ID of the category to update subcategories for. When calling directly,
     *                        this should be a top category.
     * @param context\module $newcontext The new context for the subcategories.
     * @return int[] The IDs of all categories moved to the new context.
     */
    protected function move_subcategories_to_context(int $categoryid, context\module $newcontext): array {
        global $DB;
        $movedcategories = [];

        $subcatids = $DB->get_records('question_categories', ['parent' => $categoryid]);
        foreach ($subcatids as $subcatid => $data) {
            // Because of the fallback above, where categories pointing to a
            // missing contextid are all moved to the new shared system-level
            // question bank, some categories are moved from previously
            // separate contextids to the same context. This can violate
            // unique indexes, so we fix this by ensuring uniqueness.

            // For the stamp, we just generate a new stamp if required.
            if ($DB->record_exists('question_categories', ['stamp' => $data->stamp, 'contextid' => $newcontext->id])) {
                $data->stamp = make_unique_id_code();
            }

            // The idnumber we just reset duplicates to null, as is done in other places.
            if (
                $data->idnumber !== null &&
                $DB->record_exists('question_categories', ['idnumber' => $data->idnumber, 'contextid' => $newcontext->id])
            ) {
                $data->idnumber = null;
            }

            // Update the contextid and save the category.
            $data->contextid = $newcontext->id;
            $DB->update_record('question_categories', $data);

            $movedcategories[] = $subcatid;
            $movedcategories = array_merge(
                $this->move_subcategories_to_context($subcatid, $newcontext),
                $movedcategories,
            );
        }
        return $movedcategories;
    }

    /**
     * Find the Top category for a context, if there is one.
     *
     * @param int $contextid the id of a context (which might not exist).
     * @return int a Top category id, or 0 if none is found.
     */
    protected function get_top_category_id_for_context(int $contextid): int {
        global $DB;

        // Use the cache if we have already loaded this.
        if (array_key_exists($contextid, $this->topcategorycache)) {
            return $this->topcategorycache[$contextid];
        }

        $topcategoryid = (int) $DB->get_field('question_categories', 'id',
            ['contextid' => $contextid, 'parent' => 0]);

        $this->topcategorycache[$contextid] = $topcategoryid;
        return $topcategoryid;
    }

    /**
     * Fix the context of child categories whose contextid does not match that of their parents.
     *
     * Fix here means:
     *
     * - if the child category's context exists, and has a 'Top' category, we move the child
     *   category to be just under that Top category. That is where they would have appeared
     *   before, e.g. in the return from question_categorylist().
     *
     * - if the child category points to a context that does not exist at all, then we
     *   instead change its context to be the same as it's parent's context. This may
     *   break things like images in the question text of questions there, but there is
     *   no real alternative.
     *
     * This is necessary because, due to old bugs, for example in backup and restoree code,
     * we know there can be question categories in the databases of old Moodle sites with
     * the wrong context id.
     */
    public function fix_wrong_parents(): void {
        global $DB;

        $categoriestofix = $this->get_categories_in_a_different_context_to_their_parent();
        foreach ($categoriestofix as $childcategoryid => $childcontextid) {

            $topcategoryid = $this->get_top_category_id_for_context($childcontextid);
            if ($topcategoryid) {
                // Suitable Top category in the child's current context, so move to be a parent of that.
                $DB->set_field('question_categories', 'parent', $topcategoryid, ['id' => $childcategoryid]);
            } else {
                // Top not found. Change the child to have the same context as its parent.
                // This is not efficient in DB queries, but we expect this to be a rare case, and this is simple and right.
                $childcategory = $DB->get_record('question_categories', ['id' => $childcategoryid]);
                $parentcontextid = $DB->get_field('question_categories', 'contextid', ['id' => $childcategory->parent]);
                $this->move_category_and_its_children($childcategoryid, $parentcontextid);
            }
        }
    }

    /**
     * Get question categories that are in a different context to their parent.
     *
     * @return int[] child category id => context id of the child category.
     */
    public function get_categories_in_a_different_context_to_their_parent(): array {
        global $DB;

        return $DB->get_records_sql_menu('
            SELECT c.id, c.contextid
              FROM {question_categories} c
              JOIN {question_categories} p ON p.id = c.parent
             WHERE p.contextid <> c.contextid
          ORDER BY c.id
        ');
    }

    /**
     * Set the contextid of category $categoryid and all its children to $newcontextid.
     *
     * We may need to modify the category before moving it to avoid unique key violations {@see move_subcategories_to_context()}.
     *
     * @param int $categoryid a question_category id.
     * @param int $newcontextid the place to move to.
     */
    public function move_category_and_its_children(int $categoryid, int $newcontextid): void {
        global $DB;

        $category = $DB->get_record('question_categories', ['id' => $categoryid]);
        // Check for fields that are part of a unique key for conflicts with existing records.
        if ($DB->record_exists('question_categories', ['contextid' => $newcontextid, 'stamp' => $category->stamp])) {
            $category->stamp = make_unique_id_code();
        }
        if ($DB->record_exists('question_categories', ['contextid' => $newcontextid, 'idnumber' => $category->idnumber])) {
            $category->idnumber = null;
        }
        $category->contextid = $newcontextid;
        $DB->update_record('question_categories', $category);
        $children = $DB->get_records('question_categories', ['parent' => $categoryid], '', 'id, contextid');
        foreach ($children as $child) {
            if ($child->contextid != $newcontextid) {
                $this->move_category_and_its_children($child->id, $newcontextid);
            }
        }
    }

    /**
     * Recursively check if a question category or its children contain any questions.
     *
     * @param int $categoryid The parent category to check from.
     * @return bool True if neither the category nor its children contain any questions.
     */
    protected function question_category_is_empty(int $categoryid): bool {
        global $DB;

        if ($DB->record_exists('question_bank_entries', ['questioncategoryid' => $categoryid])) {
            return false;
        }
        // If this category is empty, recursively check child categories.
        $childcategoryids = $DB->get_fieldset('question_categories', 'id', ['parent' => $categoryid]);
        foreach ($childcategoryids as $childcategoryid) {
            if (!$this->question_category_is_empty($childcategoryid)) {
                // If we found questions in a child, we don't want to check any other children.
                return false;
            }
        }
        return true;
    }
}
