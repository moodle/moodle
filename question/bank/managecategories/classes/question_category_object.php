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

/**
 * QUESTION_PAGE_LENGTH - Number of categories to display on page.
 */
define('QUESTION_PAGE_LENGTH', 25);

use context;
use moodle_exception;
use moodle_url;
use qbank_managecategories\form\question_category_edit_form;
use question_bank;
use stdClass;

/**
 * Class for performing operations on question categories.
 *
 * @package    qbank_managecategories
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_object {

    /**
     * @var array common language strings.
     */
    public $str;

    /**
     * @var array nested lists to display categories.
     */
    public $editlists = [];

    /**
     * @var string tab.
     */
    public $tab;

    /**
     * @var int tab size.
     */
    public $tabsize = 3;

    /**
     * @var moodle_url Object representing url for this page
     */
    public $pageurl;

    /**
     * @var question_category_edit_form Object representing form for adding / editing categories.
     */
    public $catform;

    /**
     * Constructor.
     *
     * @param int $page page number.
     * @param moodle_url $pageurl base URL of the display categories page. Used for redirects.
     * @param context[] $contexts contexts where the current user can edit categories.
     * @param int $currentcat id of the category to be edited. 0 if none.
     * @param int|null $defaultcategory id of the current category. null if none.
     * @param int $todelete id of the category to delete. 0 if none.
     * @param context[] $addcontexts contexts where the current user can add questions.
     */
    public function __construct($page, $pageurl, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts) {

        $this->tab = str_repeat('&nbsp;', $this->tabsize);

        $this->str = new stdClass();
        $this->str->course         = get_string('course');
        $this->str->category       = get_string('category', 'question');
        $this->str->categoryinfo   = get_string('categoryinfo', 'question');
        $this->str->questions      = get_string('questions', 'question');
        $this->str->add            = get_string('add');
        $this->str->delete         = get_string('delete');
        $this->str->moveup         = get_string('moveup');
        $this->str->movedown       = get_string('movedown');
        $this->str->edit           = get_string('editthiscategory', 'question');
        $this->str->hide           = get_string('hide');
        $this->str->order          = get_string('order');
        $this->str->parent         = get_string('parent', 'question');
        $this->str->add            = get_string('add');
        $this->str->action         = get_string('action');
        $this->str->top            = get_string('top');
        $this->str->addcategory    = get_string('addcategory', 'question');
        $this->str->editcategory   = get_string('editcategory', 'question');
        $this->str->cancel         = get_string('cancel');
        $this->str->editcategories = get_string('editcategories', 'question');
        $this->str->page           = get_string('page');

        $this->pageurl = $pageurl;

        $this->initialize($page, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts);
    }

    /**
     * Initializes this classes general category-related variables
     *
     * @param int $page page number.
     * @param context[] $contexts contexts where the current user can edit categories.
     * @param int $currentcat id of the category to be edited. 0 if none.
     * @param int|null $defaultcategory id of the current category. null if none.
     * @param int $todelete id of the category to delete. 0 if none.
     * @param context[] $addcontexts contexts where the current user can add questions.
     */
    public function initialize($page, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts): void {
        $lastlist = null;
        foreach ($contexts as $context) {
            $this->editlists[$context->id] =
                new question_category_list('ul', '', true, $this->pageurl, $page, 'cpage', QUESTION_PAGE_LENGTH, $context);
            $this->editlists[$context->id]->lastlist =& $lastlist;
            if ($lastlist !== null) {
                $lastlist->nextlist =& $this->editlists[$context->id];
            }
            $lastlist =& $this->editlists[$context->id];
        }

        $count = 1;
        $paged = false;
        foreach ($this->editlists as $key => $list) {
            list($paged, $count) = $this->editlists[$key]->list_from_records($paged, $count);
        }
        $this->catform = new question_category_edit_form($this->pageurl,
                ['contexts' => $contexts, 'currentcat' => $currentcat ?? 0]);
        if (!$currentcat) {
            $this->catform->set_data(['parent' => $defaultcategory]);
        }
    }

    /**
     * Displays the user interface.
     *
     */
    public function display_user_interface(): void {
        // Interface for editing existing categories.
        $this->output_edit_lists();
    }

    /**
     * Outputs a table to allow entry of a new category
     */
    public function output_new_table(): void {
        $this->catform->display();
    }

    /**
     * Outputs a list to allow editing/rearranging of existing categories.
     *
     * $this->initialize() must have already been called
     *
     */
    public function output_edit_lists(): void {
        global $OUTPUT;

        echo $OUTPUT->heading_with_help(get_string('questioncategories', 'question'), 'editcategories', 'question');

        foreach ($this->editlists as $context => $list) {
            $listhtml = $list->to_html(0, ['str' => $this->str]);
            if ($listhtml) {
                echo $OUTPUT->box_start('boxwidthwide boxaligncenter generalbox questioncategories contextlevel' .
                    $list->context->contextlevel);
                $fullcontext = context::instance_by_id($context);
                echo $OUTPUT->heading(get_string('questioncatsfor', 'question', $fullcontext->get_context_name()), 3);
                echo $listhtml;
                echo $OUTPUT->box_end();
            }
        }
        echo $list->display_page_numbers();
    }

    /**
     * Gets all the courseids for the given categories.
     *
     * @param array $categories contains category objects in  a tree representation
     * @return array courseids flat array in form categoryid=>courseid
     */
    public function get_course_ids(array $categories): array {
        $courseids = [];
        foreach ($categories as $key => $cat) {
            $courseids[$key] = $cat->course;
            if (!empty($cat->children)) {
                $courseids = array_merge($courseids, $this->get_course_ids($cat->children));
            }
        }
        return $courseids;
    }

    /**
     * Edit a category, or add a new one if the id is zero.
     *
     * @param int $categoryid Category id.
     */
    public function edit_single_category(int $categoryid): void {
        // Interface for adding a new category.
        global $DB;

        if ($categoryid) {
            // Editing an existing category.
            $category = $DB->get_record("question_categories", ["id" => $categoryid], '*', MUST_EXIST);
            if ($category->parent == 0) {
                throw new moodle_exception('cannotedittopcat', 'question', '', $categoryid);
            }

            $category->parent = "{$category->parent},{$category->contextid}";
            $category->submitbutton = get_string('savechanges');
            $category->categoryheader = $this->str->edit;
            $this->catform->set_data($category);
        }

        // Show the form.
        $this->catform->display();
    }

    /**
     * Sets the viable parents.
     *
     *  Viable parents are any except for the category itself, or any of it's descendants
     *  The parentstrings parameter is passed by reference and changed by this function.
     *
     * @param array $parentstrings a list of parentstrings
     * @param object $category Category object
     */
    public function set_viable_parents(array &$parentstrings, object $category): void {

        unset($parentstrings[$category->id]);
        if (isset($category->children)) {
            foreach ($category->children as $child) {
                $this->set_viable_parents($parentstrings, $child);
            }
        }
    }

    /**
     * Gets question categories.
     *
     * @param int|null $parent - if given, restrict records to those with this parent id.
     * @param string $sort - [[sortfield [,sortfield]] {ASC|DESC}].
     * @return array categories.
     */
    public function get_question_categories(int $parent = null, string $sort = "sortorder ASC"): array {
        global $COURSE, $DB;
        if (is_null($parent)) {
            $categories = $DB->get_records('question_categories', ['course' => $COURSE->id], $sort);
        } else {
            $select = "parent = ? AND course = ?";
            $categories = $DB->get_records_select('question_categories', $select, [$parent, $COURSE->id], $sort);
        }
        return $categories;
    }

    /**
     * Deletes an existing question category.
     *
     * @param int $categoryid id of category to delete.
     */
    public function delete_category(int $categoryid): void {
        global $CFG, $DB;
        helper::question_can_delete_cat($categoryid);
        if (!$category = $DB->get_record("question_categories", ["id" => $categoryid])) {  // Security.
            throw new moodle_exception('unknowcategory');
        }
        // Send the children categories to live with their grandparent.
        $DB->set_field("question_categories", "parent", $category->parent, ["parent" => $category->id]);

        // Finally delete the category itself.
        $DB->delete_records("question_categories", ["id" => $category->id]);

        // Log the deletion of this category.
        $event = \core\event\question_category_deleted::create_from_question_category_instance($category);
        $event->add_record_snapshot('question_categories', $category);
        $event->trigger();

    }

    /**
     * Move questions and then delete the category.
     *
     * @param int $oldcat id of the old category.
     * @param int $newcat id of the new category.
     */
    public function move_questions_and_delete_category(int $oldcat, int $newcat): void {
        helper::question_can_delete_cat($oldcat);
        $this->move_questions($oldcat, $newcat);
        $this->delete_category($oldcat);
    }

    /**
     * Display the form to move a category.
     *
     * @param int $questionsincategory
     * @param object $category
     * @throws \coding_exception
     */
    public function display_move_form($questionsincategory, $category): void {
        global $OUTPUT;
        $vars = new stdClass();
        $vars->name = $category->name;
        $vars->count = $questionsincategory;
        echo $OUTPUT->box(get_string('categorymove', 'question', $vars), 'generalbox boxaligncenter');
        $this->moveform->display();
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
     * Create a new category.
     *
     * Data is expected to come from question_category_edit_form.
     *
     * By default redirects on success, unless $return is true.
     *
     * @param string $newparent 'categoryid,contextid' of the parent category.
     * @param string $newcategory the name.
     * @param string $newinfo the description.
     * @param bool $return if true, return rather than redirecting.
     * @param int|string $newinfoformat description format. One of the FORMAT_ constants.
     * @param null $idnumber the idnumber. '' is converted to null.
     * @return bool|int New category id if successful, else false.
     */
    public function add_category($newparent, $newcategory, $newinfo, $return = false, $newinfoformat = FORMAT_HTML,
            $idnumber = null): int {
        global $DB;
        if (empty($newcategory)) {
            throw new moodle_exception('categorynamecantbeblank', 'question');
        }
        list($parentid, $contextid) = explode(',', $newparent);
        // ...moodle_form makes sure select element output is legal no need for further cleaning.
        require_capability('moodle/question:managecategory', context::instance_by_id($contextid));

        if ($parentid) {
            if (!($DB->get_field('question_categories', 'contextid', ['id' => $parentid]) == $contextid)) {
                throw new moodle_exception('cannotinsertquestioncatecontext', 'question', '',
                    ['cat' => $newcategory, 'ctx' => $contextid]);
            }
        }

        if ((string) $idnumber === '') {
            $idnumber = null;
        } else if (!empty($contextid)) {
            // While this check already exists in the form validation, this is a backstop preventing unnecessary errors.
            if ($DB->record_exists('question_categories',
                    ['idnumber' => $idnumber, 'contextid' => $contextid])) {
                $idnumber = null;
            }
        }

        $cat = new stdClass();
        $cat->parent = $parentid;
        $cat->contextid = $contextid;
        $cat->name = $newcategory;
        $cat->info = $newinfo;
        $cat->infoformat = $newinfoformat;
        $cat->sortorder = 999;
        $cat->stamp = make_unique_id_code();
        $cat->idnumber = $idnumber;
        $categoryid = $DB->insert_record("question_categories", $cat);

        // Log the creation of this category.
        $category = new stdClass();
        $category->id = $categoryid;
        $category->contextid = $contextid;
        $event = \core\event\question_category_created::create_from_question_category_instance($category);
        $event->trigger();

        if ($return) {
            return $categoryid;
        } else {
            // Always redirect after successful action.
            redirect($this->pageurl);
        }
    }

    /**
     * Updates an existing category with given params.
     *
     * Warning! parameter order and meaning confusingly different from add_category in some ways!
     *
     * @param int $updateid id of the category to update.
     * @param int $newparent 'categoryid,contextid' of the parent category to set.
     * @param string $newname category name.
     * @param string $newinfo category description.
     * @param int|string $newinfoformat description format. One of the FORMAT_ constants.
     * @param int $idnumber the idnumber. '' is converted to null.
     * @param bool $redirect if true, will redirect once the DB is updated (default).
     */
    public function update_category($updateid, $newparent, $newname, $newinfo, $newinfoformat = FORMAT_HTML,
            $idnumber = null, $redirect = true): void {
        global $CFG, $DB;
        if (empty($newname)) {
            throw new moodle_exception('categorynamecantbeblank', 'question');
        }

        // Get the record we are updating.
        $oldcat = $DB->get_record('question_categories', ['id' => $updateid]);
        $lastcategoryinthiscontext = helper::question_is_only_child_of_top_category_in_context($updateid);

        if (!empty($newparent) && !$lastcategoryinthiscontext) {
            list($parentid, $tocontextid) = explode(',', $newparent);
        } else {
            $parentid = $oldcat->parent;
            $tocontextid = $oldcat->contextid;
        }

        // Check permissions.
        $fromcontext = context::instance_by_id($oldcat->contextid);
        require_capability('moodle/question:managecategory', $fromcontext);

        // If moving to another context, check permissions some more, and confirm contextid,stamp uniqueness.
        $newstamprequired = false;
        if ($oldcat->contextid != $tocontextid) {
            $tocontext = context::instance_by_id($tocontextid);
            require_capability('moodle/question:managecategory', $tocontext);

            // Confirm stamp uniqueness in the new context. If the stamp already exists, generate a new one.
            if ($DB->record_exists('question_categories', ['contextid' => $tocontextid, 'stamp' => $oldcat->stamp])) {
                $newstamprequired = true;
            }
        }

        if ((string) $idnumber === '') {
            $idnumber = null;
        } else if (!empty($tocontextid)) {
            // While this check already exists in the form validation, this is a backstop preventing unnecessary errors.
            if ($DB->record_exists_select('question_categories',
                    'idnumber = ? AND contextid = ? AND id <> ?',
                    [$idnumber, $tocontextid, $updateid])) {
                $idnumber = null;
            }
        }

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
        $DB->update_record('question_categories', $cat);
        // Update the set_reference records when moving a category to a different context.
        move_question_set_references($cat->id, $cat->id, $oldcat->contextid, $tocontextid);

        // Log the update of this category.
        $event = \core\event\question_category_updated::create_from_question_category_instance($cat);
        $event->trigger();

        // If the category name has changed, rename any random questions in that category.
        if ($oldcat->name != $cat->name) {
            // Get the question ids for each question category.
            $questionids = $this->get_real_question_ids_in_category($cat->id);

            foreach ($questionids as $question) {
                $where = "qtype = 'random' AND id = ? AND " . $DB->sql_compare_text('questiontext') . " = ?";

                $randomqtype = question_bank::get_qtype('random');
                $randomqname = $randomqtype->question_name($cat, false);
                $DB->set_field_select('question', 'name', $randomqname, $where, [$question->id, '0']);

                $randomqname = $randomqtype->question_name($cat, true);
                $DB->set_field_select('question', 'name', $randomqname, $where, [$question->id, '1']);
            }
        }

        if ($oldcat->contextid != $tocontextid) {
            // Moving to a new context. Must move files belonging to questions.
            question_move_category_to_context($cat->id, $oldcat->contextid, $tocontextid);
        }

        // Cat param depends on the context id, so update it.
        $this->pageurl->param('cat', $updateid . ',' . $tocontextid);
        if ($redirect) {
            // Always redirect after successful action.
            redirect($this->pageurl);
        }
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
}
