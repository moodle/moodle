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
 * Test helpers for the essay question type.
 *
 * @package    qtype_essay
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the essay question type.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('editor', 'editorfilepicker', 'plain', 'monospaced', 'responsetemplate');
    }

    /**
     * Helper method to reduce duplication.
     * @return qtype_essay_question
     */
    protected function initialise_essay_question() {
        question_bank::load_question_definition_classes('essay');
        $q = new qtype_essay_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Essay question (HTML editor)';
        $q->questiontext = 'Please write a story about a frog.';
        $q->generalfeedback = 'I hope your story had a beginning, a middle and an end.';
        $q->responseformat = 'editor';
        $q->responsefieldlines = 10;
        $q->attachments = 0;
        $q->graderinfo = '';
        $q->graderinfoformat = FORMAT_HTML;
        $q->qtype = question_bank::get_qtype('essay');

        return $q;
    }

    /**
     * Makes an essay question using the HTML editor as input.
     * @return qtype_essay_question
     */
    public function make_essay_question_editor() {
        return $this->initialise_essay_question();
    }

    /**
     * Makes an essay question using the HTML editor allowing embedded files as
     * input, and up to three attachments.
     * @return qtype_essay_question
     */
    public function make_essay_question_editorfilepicker() {
        $q = $this->initialise_essay_question();
        $q->responseformat = 'editorfilepicker';
        $q->attachments = 3;
        return $q;
    }

    /**
     * Make the data what would be received from the editing form for an essay
     * question using the HTML editor allowing embedded files as input, and up
     * to three attachments.
     *
     * @return stdClass the data that would be returned by $form->get_gata();
     */
    public function get_essay_question_form_data_editorfilepicker() {
        $fromform = new stdClass();

        $fromform->name = 'Essay question with filepicker and attachments';
        $fromform->questiontext = array('text' => 'Please write a story about a frog.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'I hope your story had a beginning, a middle and an end.', 'format' => FORMAT_HTML);
        $fromform->responseformat = 'editorfilepicker';
        $fromform->responsefieldlines = 10;
        $fromform->attachments = 3;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = array('text' => '', 'format' => FORMAT_HTML);

        return $fromform;
    }

    /**
     * Makes an essay question using plain text input.
     * @return qtype_essay_question
     */
    public function make_essay_question_plain() {
        $q = $this->initialise_essay_question();
        $q->responseformat = 'plain';
        return $q;
    }

    /**
     * Makes an essay question using monospaced input.
     * @return qtype_essay_question
     */
    public function make_essay_question_monospaced() {
        $q = $this->initialise_essay_question();
        $q->responseformat = 'monospaced';
        return $q;
    }

    public function make_essay_question_responsetemplate() {
        $q = $this->initialise_essay_question();
        $q->responsetemplate = 'Once upon a time';
        $q->responsetemplateformat = FORMAT_HTML;
        return $q;
    }
}
