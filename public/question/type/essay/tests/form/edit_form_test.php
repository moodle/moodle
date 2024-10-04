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

namespace qtype_essay\form;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/essay/edit_essay_form.php');

/**
 * Unit tests for the essay edit form.
 *
 * @package   qtype_essay
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class edit_form_test extends \advanced_testcase {
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
        global $USER;
        $this->setAdminUser();
        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $bankcontext = \context_module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id, true);
        $fakequestion = new \stdClass();
        $fakequestion->qtype = 'essay';
        $fakequestion->contextid = $bankcontext->id;
        $fakequestion->createdby = $USER->id;
        $fakequestion->category = $category->id;
        $fakequestion->questiontext = 'please writer an assay about ...';
        $fakequestion->responseformat = 'editorfilepicker';
        $fakequestion->responserequired = 1;
        $fakequestion->responsefieldlines = 10;
        $fakequestion->attachments = -1;
        $fakequestion->attachmentsrequired = 3;
        $fakequestion->filetypeslist = '';

        $form = new $classname(
            new \moodle_url('/'),
            $fakequestion,
            $category,
            new \core_question\local\bank\question_edit_contexts($bankcontext)
        );

        return [$form, $category];
    }

    /**
     * Test the form for correct validation of attachments options.
     *
     * @dataProvider user_preference_provider
     * @param int $allowed
     * @param int $required
     * @param array $expected
     */
    public function test_attachments_validation(int $allowed, int $required, array $expected): void {
        list($form, $category) = $this->get_form('qtype_essay_edit_form');
        $submitteddata = [
            'category' => $category->id,
            'questiontext' => [
                'text' => 'please writer an assay about ...',
                'format' => FORMAT_HTML,
            ],
            'responseformat' => 'editorfilepicker',
            'responserequired' => '1',
            'attachments' => $allowed,
            'attachmentsrequired' => $required,
        ];
        $errors = $form->validation($submitteddata, []);
        $this->assertArrayNotHasKey('attachments', $errors);
        $this->assertEquals($expected, $errors);
    }

    /**
     * Return an array of all possible allowed and required attachments,
     * and the expected results from the form validation method.
     *
     * @return array, an array of all possible options.
     */
    public static function user_preference_provider(): array {
        $valid = [];
        $invalid = ['attachmentsrequired' => get_string('mustrequirefewer', 'qtype_essay')];
        return [
            'Attachments allowed=0, required=0, valid' => [0, 0, $valid],
            'Attachments allowed=0, required=1, invalid, so required is set to 0 when saving' => [0, 1, $valid],
            'Attachments allowed=0, required=2, invalid, so required is set to 0 when saving' => [0, 2, $valid],
            'Attachments allowed=0, required=3, invalid, so required is set to 0 when saving' => [0, 3, $valid],

            'Attachments allowed=1, required=0, valid' => [1, 0, $valid],
            'Attachments allowed=1, required=1, valid' => [1, 1, $valid],
            'Attachments allowed=1, required=2, invalid' => [1, 2, $invalid],

            'Attachments allowed=2, required=3, invalid' => [2, 3, $invalid],

            'Attachments allowed=3, required=4, invalid' => [3, 4, $invalid],

            'Attachments allowed=-1, required=4, valid' => [-1, 4, $valid],
        ];
    }
}
