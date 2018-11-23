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
 * Unit tests for the drag-and-drop words into sentences edit form.
 *
 * @package   qtype_ddwtos
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/ddwtos/edit_ddwtos_form.php');

/**
 * Unit tests for the drag-and-drop words into sentences edit form.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddwtos_edit_form_test extends advanced_testcase {
    /**
     * Helper method.
     *
     * @param string $classname the question form class to instantiate.
     *
     * @return array with two elements:
     *      question_edit_form great a question form instance that can be tested.
     *      stdClass the question category.
     */
    protected function get_form($classname) {
        $this->setAdminUser();
        $this->resetAfterTest();

        $syscontext = context_system::instance();
        $category = question_make_default_categories(array($syscontext));
        $fakequestion = new stdClass();
        $fakequestion->qtype = 'ddwtos'; // Does not actually matter if this is wrong.
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

        $form = new $classname(new moodle_url('/'), $fakequestion, $category,
                new question_edit_contexts($syscontext));

        return [$form, $category];
    }

    /**
     * Test the form shows the right number of groups of choices.
     */
    public function test_number_of_choice_groups() {
        list($form) = $this->get_form('qtype_ddwtos_edit_form');
        // Use reflection to get the protected property we need.
        $property = new ReflectionProperty('qtype_ddwtos_edit_form', '_form');
        $property->setAccessible(true);
        $mform = $property->getValue($form);
        $choices = $mform->getElement('choices[0]');
        $groupoptions = $choices->_elements[1];
        $this->assertCount(8, $groupoptions->_options);
    }

    /**
     * Test the form correctly validates the HTML allowed in choices.
     */
    public function test_choices_validation() {
        list($form, $category) = $this->get_form('qtype_ddwtos_edit_form');

        $submitteddata = [
                'category' => $category->id,
                'questiontext' => ['text' => 'Test [[1]] question [[2]]', 'format' => FORMAT_HTML],
                'choices' => [
                        ['answer' => 'frog'],
                        ['answer' => '<b>toad</b>'],
                        ['answer' => '<span lang="fr"><em>chien</em></span>'],
                        ['answer' => '<textarea>evil!</textarea>'],
                ],
        ];

        $errors = $form->validation($submitteddata, []);

        $this->assertArrayNotHasKey('choices[0]', $errors);
        $this->assertArrayNotHasKey('choices[1]', $errors);
        $this->assertArrayNotHasKey('choices[2]', $errors);
        $this->assertEquals('&lt;textarea&gt; is not allowed. ' .
                '(Only &lt;sub&gt;, &lt;sup&gt;, &lt;b&gt;, &lt;i&gt;, ' .
                '&lt;em&gt;, &lt;strong&gt;, &lt;span&gt; are permitted.)',
                $errors['choices[3]']);
    }
}
