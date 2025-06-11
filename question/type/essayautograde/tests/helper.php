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
 * Test helpers for the essayautograde question type.
 *
 * @package    qtype_essayautograde
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the essayautograde question type.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_test_helper extends question_test_helper {

    public function get_test_questions() {
        return array('editor', 'editorfilepicker', 'plain', 'monospaced', 'responsetemplate', 'responsesample', 'noinline');
    }

    /**
     * Helper method to reduce duplication.
     * @return qtype_essayautograde_question
     */
    protected function initialise_essayautograde_question() {
        question_bank::load_question_definition_classes('essayautograde');
        $q = new qtype_essayautograde_question();
        test_question_maker::initialise_a_question($q);
        $q->qtype = question_bank::get_qtype('essayautograde');
        $q->name = 'Essay question (HTML editor)';
        $q->questiontext = 'Please write a story about a frog.';
        $q->generalfeedback = 'I hope your story had a beginning, a middle and an end.';
        $q->responseformat = 'editor';
        $q->responserequired = 1;
        $q->responsefieldlines = 10;
        $q->attachments = 0;
        $q->attachmentsrequired = 0;
        $q->graderinfo = '';
        $q->graderinfoformat = FORMAT_HTML;
        $q->responsetemplate = '';
        $q->responsetemplateformat = FORMAT_HTML;
        $q->responsesample = '';
        $q->responsesampleformat = FORMAT_HTML;
        $q->maxbytes = 0;
        $q->filetypeslist = '';
        $q->enableautograde = 1;
        $q->itemtype = 1;
        $q->itemcount = 0;
        $q->showfeedback = 0;
        $q->showcalculation = 0;
        $q->showtextstats = 0;
        $q->textstatitems = 0;
        $q->showgradebands = 0;
        $q->addpartialgrades = 0;
        $q->showtargetphrases = 0;
        $q->errorcmid = 0;
        $q->errorpercent = 0;
        $q->correctfeedback = 'Correct feedback';
        $q->correctfeedbackformat = FORMAT_HTML;
        $q->incorrectfeedback = 'Incorrect feedback';
        $q->incorrectfeedbackformat = FORMAT_HTML;
        $q->partiallycorrectfeedback = 'Partially correct feedback';
        $q->partiallycorrectfeedbackformat = FORMAT_HTML;
        return $q;
    }

    /**
     * Makes an essayautograde question using the HTML editor as input.
     * @return qtype_essayautograde_question
     */
    public function make_essayautograde_question_editor() {
        return $this->initialise_essayautograde_question();
    }

    /**
     * Make the data what would be received from the editing form for an essayautograde
     * question using the HTML editor allowing embedded files as input, and up
     * to three attachments.
     *
     * @return stdClass the data that would be returned by $form->get_gata();
     */
    public function get_essayautograde_question_form_data_editor() {
        $fromform = new stdClass();

        $fromform->name = 'Essay question (HTML editor)';
        $fromform->questiontext = array('text' => 'Please write a story about a frog.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback =
             array('text' => 'I hope your story had a beginning, a middle and an end.', 'format' => FORMAT_HTML);
        $fromform->responseformat = 'editor';
        $fromform->responserequired = 1;
        $fromform->responsefieldlines = 10;
        $fromform->attachments = 0;
        $fromform->attachmentsrequired = 0;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsesample = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->correctfeedback = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->partiallycorrectfeedback = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->incorrectfeedback = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->addpartialgrades = 1;

        return $fromform;
    }

    /**
     * Makes an essayautograde question using the HTML editor allowing embedded files as
     * input, and up to three attachments.
     * @return qtype_essayautograde_question
     */
    public function make_essayautograde_question_editorfilepicker() {
        $q = $this->initialise_essayautograde_question();
        $q->responseformat = 'editorfilepicker';
        $q->attachments = 3;
        return $q;
    }

    /**
     * Make the data what would be received from the editing form for an essayautograde
     * question using the HTML editor allowing embedded files as input, and up
     * to three attachments.
     *
     * @return stdClass the data that would be returned by $form->get_gata();
     */
    public function get_essayautograde_question_form_data_editorfilepicker() {
        $fromform = new stdClass();

        $fromform->name = 'Essay question with filepicker and attachments';
        $fromform->questiontext = array('text' => 'Please write a story about a frog.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback =
             array('text' => 'I hope your story had a beginning, a middle and an end.', 'format' => FORMAT_HTML);
        $fromform->responseformat = 'editorfilepicker';
        $fromform->responserequired = 1;
        $fromform->responsefieldlines = 10;
        $fromform->attachments = 3;
        $fromform->attachmentsrequired = 0;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsesample = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->correctfeedback = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->partiallycorrectfeedback = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->incorrectfeedback = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->addpartialgrades = 1;

        return $fromform;
    }

    /**
     * Makes an essayautograde question using plain text input.
     * @return qtype_essayautograde_question
     */
    public function make_essayautograde_question_plain() {
        $q = $this->initialise_essayautograde_question();
        $q->responseformat = 'plain';
        return $q;
    }

    /**
     * Make the data what would be received from the editing form for an essayautograde
     * question using the HTML editor allowing embedded files as input, and up
     * to three attachments.
     *
     * @return stdClass the data that would be returned by $form->get_gata();
     */
    public function get_essayautograde_question_form_data_plain() {
        $fromform = new stdClass();

        $fromform->name = 'Essay question with filepicker and attachments';
        $fromform->questiontext = array('text' => 'Please write a story about a frog.', 'format' => FORMAT_PLAIN);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback =
             array('text' => 'I hope your story had a beginning, a middle and an end.', 'format' => FORMAT_PLAIN);
        $fromform->responseformat = 'plain';
        $fromform->responserequired = 1;
        $fromform->responsefieldlines = 10;
        $fromform->attachments = 0;
        $fromform->attachmentsrequired = 0;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_PLAIN);
        $fromform->responsetemplate = array('text' => '', 'format' => FORMAT_PLAIN);
        $fromform->responsesample = array('text' => '', 'format' => FORMAT_PLAIN);
        $fromform->correctfeedback = array('text' => '', 'format' => FORMAT_PLAIN);
        $fromform->partiallycorrectfeedback = array('text' => '', 'format' => FORMAT_PLAIN);
        $fromform->incorrectfeedback = array('text' => '', 'format' => FORMAT_PLAIN);
        $fromform->addpartialgrades = 0;
        return $fromform;
    }

    /**
     * Makes an essayautograde question using monospaced input.
     * @return qtype_essayautograde_question
     */
    public function make_essayautograde_question_monospaced() {
        $q = $this->initialise_essayautograde_question();
        $q->responseformat = 'monospaced';
        return $q;
    }

    public function make_essayautograde_question_responsetemplate() {
        $q = $this->initialise_essayautograde_question();
        $q->responsetemplate = 'Once upon a time';
        $q->responsetemplateformat = FORMAT_HTML;
        return $q;
    }

    public function make_essayautograde_question_responsesample() {
        $q = $this->initialise_essayautograde_question();
        $q->responsesample = 'Once upon a time';
        $q->responsesampleformat = FORMAT_HTML;
        return $q;
    }

    /**
     * Makes an essayautograde question without an inline text editor.
     * @return qtype_essayautograde_question
     */
    public function make_essayautograde_question_noinline() {
        $q = $this->initialise_essayautograde_question();
        $q->responseformat = 'noinline';
        $q->attachments = 3;
        $q->attachmentsrequired = 1;
        return $q;
    }

    /**
     * Creates an empty draft area for attachments.
     * @return int The draft area's itemid.
     */
    protected function make_attachment_draft_area() {
        $draftid = 0;
        $contextid = 0;

        $component = 'question';
        $filearea = 'response_attachments';

        // Create an empty file area.
        file_prepare_draft_area($draftid, $contextid, $component, $filearea, null);
        return $draftid;
    }

    /**
     * Creates an attachment in the provided attachment draft area.
     * @param int $draftid The itemid for the draft area in which the file should be created.
     * @param string $name The filename for the file to be created.
     * @param string $contents The contents of the file to be created.
     */
    protected function make_attachment($draftid, $name, $contents) {
        global $USER;

        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);

        // Create the file in the provided draft area.
        $fileinfo = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftid,
            'filepath'  => '/',
            'filename'  => $name,
        );
        $fs->create_file_from_string($fileinfo, $contents);
    }

    /**
     * Generates a draft file area that contains the provided number of attachments. You should ensure
     * that a user is logged in with setUser before you run this function.
     *
     * @param int $attachments The number of attachments to generate.
     * @return int The itemid of the generated draft file area.
     */
    public function make_attachments($attachments) {
        $draftid = $this->make_attachment_draft_area();

        // Create the relevant amount of dummy attachments in the given draft area.
        for ($i = 0; $i < $attachments; ++$i) {
            $this->make_attachment($draftid, $i, $i);
        }

        return $draftid;
    }

    /**
     * Generates a question_file_saver that contains the provided number of attachments. You should ensure
     * that a user is logged in with setUser before you run this function.
     *
     * @param int $:attachments The number of attachments to generate.
     * @return question_file_saver a question_file_saver that contains the given amount of dummy files, for use in testing.
     */
    public function make_attachments_saver($attachments) {
        return new question_file_saver($this->make_attachments($attachments), 'question', 'response_attachments');
    }

    /**
     * Makes a essay autograde question with correct ansewer true, defaultmark 1.
     * @return qtype_essayautograde_question
     */
    public static function make_an_essayautograde_question() {
        question_bank::load_question_definition_classes('essayautograde');
        $essay = new qtype_essayautograde_question();
        test_question_maker::initialise_a_question($essay);
        $essay->name = 'Essayautograde question';
        $essay->questiontext = 'Write an essay.';
        $essay->generalfeedback = 'I hope you wrote an interesting essay.';
        $essay->penalty = 0;
        $essay->qtype = question_bank::get_qtype('essayautograde');
        $essay->responseformat = 'editor';
        $essay->responserequired = 1;
        $essay->responsefieldlines = 15;
        $essay->attachments = 0;
        $essay->attachmentsrequired = 0;
        $essay->graderinfo = '';
        $essay->graderinfoformat = FORMAT_MOODLE;
        $essay->responsetemplate = '';
        $essay->responsetemplateformat = FORMAT_MOODLE;
        $essay->responsesample = '';
        $essay->responsesampleformat = FORMAT_MOODLE;
        return $essay;
    }
}
