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

namespace qbank_managecategories\form;

use context;
use context_module;
use context_course;
use qbank_managecategories\helper;
use moodle_exception;
use moodle_url;
use core_question\local\bank\question_edit_contexts;
use qbank_managecategories\output\category;
use core_question\category_manager;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/questionlib.php');

/**
 * Defines the form for editing question categories.
 *
 * Form for editing questions categories (name, description, etc.)
 *
 * @package    qbank_managecategories
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_edit_form extends \core_form\dynamic_form {
    /** @var ?category_manager $manager */
    protected ?category_manager $manager = null;

    /**
     * Return the category manager.
     *
     * Since we cannot override the constructor, using this method ensures the manager has always been initialised
     * before access.
     *
     * @return category_manager
     */
    protected function get_manager(): category_manager {
        if (is_null($this->manager)) {
            $this->manager = new category_manager();
        }
        return $this->manager;
    }

    /**
     * Get the category, contexts, course and cmid based on the data provided via AJAX.
     *
     * @return array
     * @throws \coding_exception
     */
    protected function get_current_data() {
        // If categoryid is set, we are editing an existing category.
        $currentcat = isset($this->_ajaxformdata['categoryid']) ? (int)$this->_ajaxformdata['categoryid'] : 0;
        // Determine the context based on the provided IDs.
        $cmid = isset($this->_ajaxformdata['cmid']) ? (int)$this->_ajaxformdata['cmid'] : 0;
        $courseid = isset($this->_ajaxformdata['courseid']) ? (int)$this->_ajaxformdata['courseid'] : 0;
        if ($cmid !== 0) {
            $thiscontext = context_module::instance($cmid);
        }

        if ($courseid !== 0) {
            $thiscontext = context_course::instance($courseid);
        }

        if ($courseid === 0 && $cmid === 0) {
            $parentcontext = (int)explode(',', $this->_ajaxformdata['parent'])[1];
            $contextid = $parentcontext === 0 ? $this->_ajaxformdata['contextid'] : (int)$parentcontext;
            $thiscontext = context::instance_by_id($contextid);
        }

        if ($thiscontext) {
            $contexts = new question_edit_contexts($thiscontext);
            $contexts = $contexts->all();
        }
        return [$currentcat, $cmid, $courseid, $thiscontext, $contexts];
    }

    /**
     * Build the form definition.
     *
     * This adds all the form fields that the manage categories feature needs.
     * @throws \coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        [$currentcat, $cmid, $courseid, $thiscontext, $contexts] = $this->get_current_data();

        $mform->addElement('hidden', 'contextid', $thiscontext->id);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'cmid', $cmid);
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('hidden', 'currentparent');
        $mform->setDefault('currentparent', $this->_ajaxformdata['parent'] ?? '');
        $mform->setType('currentparent', PARAM_SEQUENCE);

        $mform->addElement(
            'questioncategory',
            'parent',
            get_string('parentcategory', 'question'),
            [
                'contexts' => $contexts,
                'top' => true,
                'currentcat' => $currentcat,
                'nochildrenof' => $currentcat,
            ],
        );
        $mform->setType('parent', PARAM_SEQUENCE);
        if ($this->get_manager()->is_only_child_of_top_category_in_context($currentcat)) {
            $mform->hardFreeze('parent');
        }
        $mform->addHelpButton('parent', 'parentcategory', 'question');

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="254" size="50"');
        $mform->setDefault('name', '');
        $mform->addRule('name', get_string('categorynamecantbeblank', 'question'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('editor', 'info', get_string('categoryinfo', 'question'),
                ['rows' => 10], ['noclean' => 1]);
        $mform->setDefault('info', '');
        $mform->setType('info', PARAM_RAW);

        $mform->addElement('text', 'idnumber', get_string('idnumber', 'question'), 'maxlength="100"  size="10"');
        $mform->addHelpButton('idnumber', 'idnumber', 'question');
        $mform->setType('idnumber', PARAM_RAW);

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'questioncount', $this->_ajaxformdata['questioncount'] ?? 0);
        $mform->setType('questioncount', PARAM_INT);

        $mform->addElement('hidden', 'sortorder', $this->_ajaxformdata['sortorder'] ?? 0);
        $mform->setType('sortorder', PARAM_INT);
    }

    /**
     * Validation.
     *
     * Ensure that we aren't trying to move the only child of a top category to a different context.
     * Ensure that the ID Number is unique within the target context.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     * @throws \dml_exception|\coding_exception
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        $currentrec = $DB->get_record('question_categories', ['id' => $data['id']]);
        $currentparent = $data['currentparent'];
        $data['parent'] ??= $currentparent; // If no parent was submitted, use the current parent.
        [$parentid, $contextid] = explode(',', $data['parent']);
        if ($currentrec) {
            $currentparent = $currentrec->parent . ',' . $currentrec->contextid;
            // Cannot move the last category in a context to another parent.
            $lastcategoryinthiscontext = $this->get_manager()->is_only_child_of_top_category_in_context($data['id']);
            if ($lastcategoryinthiscontext && $currentparent !== $data['parent']) {
                if ($parentid !== $this->_ajaxformdata['id']) {
                    $errors['parent'] = get_string('lastcategoryinthiscontext', 'qbank_managecategories');
                }
            }
            // Cannot move category in same category.
            if ($currentrec->id === $parentid && $currentrec->contextid === $contextid) {
                $errors['parent'] = get_string('categoryincategory', 'qbank_managecategories');
            }
        }
        // Add field validation check for duplicate idnumber.
        $id = $data['id'] ?? null;
        if (!empty($data['idnumber']) && !$this->get_manager()->idnumber_is_unique_in_context($data['idnumber'], $contextid, $id)) {
            $errors['idnumber'] = get_string('idnumbertaken', 'error');
        }

        return $errors;
    }

    #[\Override]
    protected function get_context_for_dynamic_submission(): context {
        $contextid = $this->optional_param('contextid', 0, PARAM_INT);
        if ($contextid === 0) {
            $contextid = $this->_ajaxformdata['contextid'];
        }
        return context::instance_by_id($contextid);
    }

    #[\Override]
    protected function check_access_for_dynamic_submission(): void {
        $this->get_manager()->require_manage_category($this->get_context_for_dynamic_submission());
    }

    /**
     * Process the category form
     *
     * Either add or update a question category, then return a reactive state update for the category.
     *
     * @return array[] Reactive state update.
     *     {@link https://moodledev.io/docs/4.2/guides/javascript/reactive#controlling-the-state-from-the-backend}
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws moodle_exception
     */
    public function process_dynamic_submission(): array {
        global $DB, $OUTPUT;

        $values = $this->get_data();

        $newinfo = format_text($values->info['text'], (int)$values->info['format'], ['noclean' => false]);
        $idnumber = $values->idnumber;

        if ((string)$idnumber === '') {
            $idnumber = null;
        }

        if (empty($values->id)) {
            $values->id = $this->get_manager()->add_category(
                $values->parent,
                $values->name,
                $newinfo,
                (int)$values->info['format'],
                $idnumber,
            );
        } else {
            $this->get_manager()->update_category(
                $values->id,
                $values->parent ?? '', // If we can't change the parent, it is not passed through he form.
                $values->name,
                $newinfo,
                (int)$values->info['format'],
                $idnumber,
            );
        }

        $record = $DB->get_record('question_categories', ['id' => $values->id]);

        // The question count will never change, we just need it passed through to re-render the category.
        $record->questioncount = $values->questioncount;

        $category = new category(
            $record,
            context::instance_by_id($record->contextid),
            $values->cmid ?? 0,
            $values->courseid ?? 0,
        );

        return [
            [
                'name' => 'categories',
                'action' => 'put',
                'fields' => [
                    'id' => $record->id,
                    'name' => $record->name,
                    'parent' => $record->parent,
                    'sortorder' => $record->sortorder,
                    'draghandle' => $category->get_canreorder(),
                    'templatecontext' => $category->export_for_template($OUTPUT),
                ],
            ],
        ];
    }

    #[\Override]
    public function set_data_for_dynamic_submission(): void {
        $categoryid = isset($this->_ajaxformdata['categoryid']) ? (int)$this->_ajaxformdata['categoryid'] : 0;
        if ($categoryid !== 0) {
            global $DB;
            $cattoset = $DB->get_record('question_categories', ['id' => $categoryid]);
            $this->set_data((object)[
                'id' => (int)$cattoset->id,
                'name' => $cattoset->name,
                'contextid' => (int)$cattoset->contextid,
                'info' => [
                    'format' => FORMAT_HTML,
                    'text' => $cattoset->info,
                ],
                'infoformat' => (int)$cattoset->infoformat,
                'parent' => (int)$cattoset->parent . ',' . (int)$cattoset->contextid,
                'idnumber' => $cattoset->idnumber,
            ]);
        }
    }

    #[\Override]
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $params = [];
        $cmid = isset($this->_ajaxformdata['cmid']) ? (int)$this->_ajaxformdata['cmid'] : 0;
        $courseid = isset($this->_ajaxformdata['courseid']) ? (int)$this->_ajaxformdata['courseid'] : 0;
        if ($cmid !== 0) {
            $params['cmid'] = $cmid;
        }

        if ($courseid !== 0) {
            $params['courseid'] = $courseid;
        }
        return new moodle_url('/question/bank/managecategories/category.php', $params);
    }
}
