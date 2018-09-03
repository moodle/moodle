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
 * Unit tests for the select missing words question definition class.
 *
 * @package   qtype_gapselect
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/gapselect/edit_form_base.php');


/**
 * Subclass of qtype_gapselect_edit_form_base that is easier to used in unit tests.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect_edit_form_base_testable extends qtype_gapselect_edit_form_base {
    public function __construct() {
        $syscontext = context_system::instance();
        $category = question_make_default_categories(array($syscontext));
        $fakequestion = new stdClass();
        $fakequestion->qtype = 'stack';
        $fakequestion->contextid = $syscontext->id;
        $fakequestion->createdby = 2;
        $fakequestion->category = $category->id;
        $fakequestion->questiontext = 'Test [[1]] question [[2]]';
        $fakequestion->options = new stdClass();
        $fakequestion->options->answers = array();
        $fakequestion->formoptions = new stdClass();
        $fakequestion->formoptions->movecontext = null;
        $fakequestion->formoptions->repeatelements = true;
        $fakequestion->inputs = null;
        parent::__construct(new moodle_url('/'), $fakequestion, $category,
                new question_edit_contexts($syscontext));
    }

    public function get_illegal_tag_error($text) {
        return parent::get_illegal_tag_error($text);
    }

    /**
     * Set the list of allowed tags.
     * @param array $allowed
     */
    public function set_allowed_tags(array $allowed) {
        $this->allowedhtmltags = $allowed;
    }
}


/**
 * Unit tests for Stack question editing form.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect_edit_form_test extends advanced_testcase {

    /**
     * Helper method.
     * @return qtype_gapselect_edit_form_base_testable a new form instance that can be tested.
     */
    protected function get_form() {
        $this->setAdminUser();
        $this->resetAfterTest();

        return new qtype_gapselect_edit_form_base_testable();
    }

    public function test_get_illegal_tag_error() {
        $form = $this->get_form();

        $this->assertEquals('', $form->get_illegal_tag_error('frog'));
        $this->assertEquals('', $form->get_illegal_tag_error('<i>toad</i>'));

        $a = new stdClass();
        $a->tag = '&lt;ijk&gt;';
        $a->allowed = '&lt;sub&gt;, &lt;sup&gt;, &lt;b&gt;, &lt;i&gt;, &lt;em&gt;, &lt;strong&gt;';
        $this->assertEquals(get_string('tagsnotallowed', 'qtype_gapselect', $a), $form->get_illegal_tag_error('<ijk>'));

        $a->tag = '&lt;/cat&gt;';
        $this->assertEquals(get_string('tagsnotallowed', 'qtype_gapselect', $a), $form->get_illegal_tag_error('</cat>'));

        $a->tag = '&lt;br /&gt;';
        $this->assertEquals(get_string('tagsnotallowed', 'qtype_gapselect', $a), $form->get_illegal_tag_error('<i><br /></i>'));

        $form->set_allowed_tags(array());

        $this->assertEquals('', $form->get_illegal_tag_error('frog'));

        $a->tag = '&lt;i&gt;';
        $this->assertEquals(get_string('tagsnotallowedatall', 'qtype_gapselect', $a),
                $form->get_illegal_tag_error('<i>toad</i>'));

        $a->tag = '&lt;ijk&gt;';
        $this->assertEquals(get_string('tagsnotallowedatall', 'qtype_gapselect', $a),
                $form->get_illegal_tag_error('<ijk>'));

        $a->tag = '&lt;/cat&gt;';
        $this->assertEquals(get_string('tagsnotallowedatall', 'qtype_gapselect', $a),
                $form->get_illegal_tag_error('</cat>'));

        $a->tag = '&lt;i&gt;';
        $this->assertEquals(get_string('tagsnotallowedatall', 'qtype_gapselect', $a),
                $form->get_illegal_tag_error('<i><br /></i>'));
    }
}
