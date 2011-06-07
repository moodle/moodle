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
 * Defines the editing form for the true-false question type.
 *
 * @package    qtype
 * @subpackage truefalse
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot.'/question/type/edit_question_form.php');


/**
 * True-false question editing form definition.
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_truefalse_edit_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    protected function definition_inner($mform) {
        $mform->addElement('select', 'correctanswer',
                get_string('correctanswer', 'qtype_truefalse'), array(
                0 => get_string('false', 'qtype_truefalse'),
                1 => get_string('true', 'qtype_truefalse')));

        $mform->addElement('editor', 'feedbacktrue',
                get_string('feedbacktrue', 'qtype_truefalse'), null, $this->editoroptions);
        $mform->setType('feedbacktrue', PARAM_RAW);

        $mform->addElement('editor', 'feedbackfalse',
                get_string('feedbackfalse', 'qtype_truefalse'), null, $this->editoroptions);
        $mform->setType('feedbackfalse', PARAM_RAW);

        $mform->addElement('header', 'multitriesheader',
                get_string('settingsformultipletries', 'question'));

        $mform->addElement('hidden', 'penalty', 1);

        $mform->addElement('static', 'penaltymessage',
                get_string('penaltyforeachincorrecttry', 'question'), 1);
        $mform->addHelpButton('penaltymessage', 'penaltyforeachincorrecttry', 'question');
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (!empty($question->options->trueanswer)) {
            $trueanswer = $question->options->answers[$question->options->trueanswer];
            $question->correctanswer = ($trueanswer->fraction != 0);

            $draftid = file_get_submitted_draft_itemid('trueanswer');
            $answerid = $question->options->trueanswer;

            $question->feedbacktrue = array();
            $question->feedbacktrue['format'] = $trueanswer->feedbackformat;
            $question->feedbacktrue['text'] = file_prepare_draft_area(
                $draftid,             // draftid
                $this->context->id,   // context
                'question',           // component
                'answerfeedback',     // filarea
                !empty($answerid) ? (int) $answerid : null, // itemid
                $this->fileoptions,   // options
                $trueanswer->feedback // text
            );
            $question->feedbacktrue['itemid'] = $draftid;
        }

        if (!empty($question->options->falseanswer)) {
            $falseanswer = $question->options->answers[$question->options->falseanswer];

            $draftid = file_get_submitted_draft_itemid('falseanswer');
            $answerid = $question->options->falseanswer;

            $question->feedbackfalse = array();
            $question->feedbackfalse['format'] = $falseanswer->feedbackformat;
            $question->feedbackfalse['text'] = file_prepare_draft_area(
                $draftid,              // draftid
                $this->context->id,    // context
                'question',            // component
                'answerfeedback',      // filarea
                !empty($answerid) ? (int) $answerid : null, // itemid
                $this->fileoptions,    // options
                $falseanswer->feedback // text
            );
            $question->feedbackfalse['itemid'] = $draftid;
        }

        return $question;
    }

    public function qtype() {
        return 'truefalse';
    }
}
