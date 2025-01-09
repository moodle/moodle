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
 * /**
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
     * Run the install task.
     *
     * @return void
     */
    public function execute() {

        global $DB, $CFG;

        require_once($CFG->dirroot . '/course/modlib.php');
        require_once($CFG->libdir . '/questionlib.php');

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
            $subcategories = array_reverse(\sort_categories_by_tree($subcategories, $oldtopcategory->id));
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
                    $courseshortname = "{$coursecategory->name}-{$coursecategory->id}";
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
            $this->move_question_category($oldtopcategory, $newmod->context);

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
    protected function create_course(\core_course_category $coursecategory, string $shortname): stdClass {
        $data = (object) [
            'enablecompletion' => 0,
            'fullname' => get_string('coursecategory', 'mod_qbank', $coursecategory->name),
            'shortname' => $shortname,
            'category' => $coursecategory->id,
        ];
        return \create_course($data);
    }

    /**
     * Create a new 'Top' category in our new context and move the old categories descendents beneath it.
     *
     * @param stdClass $oldtopcategory The old 'Top' category that we are moving.
     * @param \context $newcontext The context we are moving our category to.
     * @return void
     */
    protected function move_question_category(stdClass $oldtopcategory, \context $newcontext): void {
        global $DB;

        $newtopcategory = question_get_top_category($newcontext->id, true);

        // This function moves subcategories, so we have to start at the top.
        question_move_category_to_context($oldtopcategory->id, $oldtopcategory->contextid, $newcontext->id);

        // Move the parent from the old top category to the new one.
        $DB->set_field('question_categories', 'parent', $newtopcategory->id, ['parent' => $oldtopcategory->id]);
    }

    /**
     * Recursively check if a question category or its children contain any questions.
     *
     * @param int $categoryid The parent category to check from.
     * @return bool True if neither the category nor its children contain any questions.
     * @throws \dml_exception
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
