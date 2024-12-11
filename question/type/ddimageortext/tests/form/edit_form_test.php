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

namespace qtype_ddimageortext\form;

use qtype_ddimageortext_edit_form;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/ddimageortext/edit_ddimageortext_form.php');

/**
 * Unit tests for the drag-and-drop onto image edit form.
 *
 * @package   qtype_ddimageortext
 * @copyright  2019 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class edit_form_test extends \advanced_testcase {
    /**
     * Helper method.
     *
     * @return array with two elements:
     *      question_edit_form great a question form instance that can be tested.
     *      stdClass the question category.
     */
    protected function get_form() {
        $this->setAdminUser();
        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $bankcontext = \context_module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id, true);
        $fakequestion = new \stdClass();
        $fakequestion->qtype = 'ddimageortext';
        $fakequestion->contextid = $bankcontext->id;
        $fakequestion->createdby = 2;
        $fakequestion->category = $category->id;
        $fakequestion->questiontext = 'Test question';
        $fakequestion->options = new \stdClass();
        $fakequestion->options->answers = array();
        $fakequestion->formoptions = new \stdClass();
        $fakequestion->formoptions->movecontext = null;
        $fakequestion->formoptions->repeatelements = true;
        $fakequestion->inputs = null;

        $form = new qtype_ddimageortext_edit_form(new \moodle_url('/'), $fakequestion, $category,
                new \core_question\local\bank\question_edit_contexts($bankcontext));

        return [$form, $category];
    }

    /**
     * Test the form correctly validates the HTML allowed in items.
     */
    public function test_item_validation(): void {
        list($form, $category) = $this->get_form();

        $submitteddata = [
            'category' => $category->id,
            'bgimage' => '',
            'nodropzone' => 0,
            'noitems' => 5,
            'drags' => [
                ['dragitemtype' => 'image'],
                ['dragitemtype' => 'image'],
                ['dragitemtype' => 'word'],
                ['dragitemtype' => 'word'],
                ['dragitemtype' => 'word'],
            ],
            'dragitem' => [
                0,
                0,
                0,
                0,
                0,
            ],
            'draglabel' => [
                'frog',
                '<b>toad</b>',
                'cat',
                '<span lang="fr"><b>chien</b></span>',
                '<textarea>evil!</textarea>',
            ],
        ];

        $errors = $form->validation($submitteddata, []);

        $this->assertArrayNotHasKey('draglabel[0]', $errors);
        $this->assertEquals('HTML tags are not allowed in this text which is the alt text for a draggable image.',
                $errors['draglabel[1]']);
        $this->assertArrayNotHasKey('draglabel[2]', $errors);
        $this->assertArrayNotHasKey('draglabel[3]', $errors);
        $this->assertEquals('Only "&lt;br&gt;&lt;sub&gt;&lt;sup&gt;&lt;b&gt;&lt;i&gt;&lt;strong&gt;&lt;em&gt;&lt;span&gt;" ' .
                'tags are allowed in this draggable text.', $errors['draglabel[4]']);
    }
}
