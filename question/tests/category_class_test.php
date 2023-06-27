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
 * Events tests.
 *
 * @package core_question
 * @copyright 2019 the Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/category_class.php');

class core_question_category_class_testcase extends advanced_testcase {

    /**
     * @var question_category_object used in the tests.
     */
    protected $qcobject;

    /**
     * @var context a context to use.
     */
    protected $context;

    /**
     * @var stdClass top category in context.
     */
    protected $topcat;

    protected function setUp(): void {
        parent::setUp();
        self::setAdminUser();
        $this->resetAfterTest();
        $this->context = context_course::instance(SITEID);
        $contexts = new question_edit_contexts($this->context);
        $this->topcat = question_get_top_category($this->context->id, true);
        $this->qcobject = new question_category_object(null,
                new moodle_url('/question/category.php', ['courseid' => SITEID]),
                $contexts->having_one_edit_tab_cap('categories'), 0, null, 0,
                $contexts->having_cap('moodle/question:add'));
    }

    /**
     * Test creating a category.
     */
    public function test_add_category_no_idnumber() {
        global $DB;

        $id = $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'New category', '', true, FORMAT_HTML, ''); // No idnumber passed as '' to match form data.

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New category', $newcat->name);
        $this->assertNull($newcat->idnumber);
    }

    /**
     * Test creating a category with a tricky idnumber.
     */
    public function test_add_category_set_idnumber_0() {
        global $DB;

        $id = $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'New category', '', true, FORMAT_HTML, '0');

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New category', $newcat->name);
        $this->assertSame('0', $newcat->idnumber);
    }

    /**
     * Trying to add a category with duplicate idnumber blanks it.
     * (In reality, this would probably get caught by form validation.)
     */
    public function test_add_category_try_to_set_duplicate_idnumber() {
        global $DB;

        $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'Existing category', '', true, FORMAT_HTML, 'frog');

        $id = $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'New category', '', true, FORMAT_HTML, 'frog');

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New category', $newcat->name);
        $this->assertNull($newcat->idnumber);
    }

    /**
     * Test updating a category.
     */
    public function test_update_category() {
        global $DB;

        $id = $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'Old name', 'Description', true, FORMAT_HTML, 'frog');

        $this->qcobject->update_category($id, $this->topcat->id . ',' . $this->topcat->contextid,
                'New name', 'New description', FORMAT_HTML, '0', false);

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New name', $newcat->name);
        $this->assertSame('0', $newcat->idnumber);
    }

    /**
     * Test updating a category to remove the idnumber.
     */
    public function test_update_category_removing_idnumber() {
        global $DB;

        $id = $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'Old name', 'Description', true, FORMAT_HTML, 'frog');

        $this->qcobject->update_category($id, $this->topcat->id . ',' . $this->topcat->contextid,
                'New name', 'New description', FORMAT_HTML, '', false);

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New name', $newcat->name);
        $this->assertNull($newcat->idnumber);
    }

    /**
     * Test updating a category without changing the idnumber.
     */
    public function test_update_category_dont_change_idnumber() {
        global $DB;

        $id = $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'Old name', 'Description', true, FORMAT_HTML, 'frog');

        $this->qcobject->update_category($id, $this->topcat->id . ',' . $this->topcat->contextid,
                'New name', 'New description', FORMAT_HTML, 'frog', false);

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New name', $newcat->name);
        $this->assertSame('frog', $newcat->idnumber);
    }

    /**
     * Trying to update a category so its idnumber duplicates idnumber blanks it.
     * (In reality, this would probably get caught by form validation.)
     */
    public function test_update_category_try_to_set_duplicate_idnumber() {
        global $DB;

        $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'Existing category', '', true, FORMAT_HTML, 'toad');
        $id = $this->qcobject->add_category($this->topcat->id . ',' . $this->topcat->contextid,
                'old name', '', true, FORMAT_HTML, 'frog');

        $this->qcobject->update_category($id, $this->topcat->id . ',' . $this->topcat->contextid,
                'New name', '', FORMAT_HTML, 'toad', false);

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New name', $newcat->name);
        $this->assertNull($newcat->idnumber);
    }
}
