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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/question/type/edit_question_form.php');

/**
 * Defines the editing form for the thruefalse question type.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * truefalse editing form definition.
 */
class question_edit_truefalse_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    function definition_inner(&$mform) {
        $mform->addElement('select', 'correctanswer', get_string('correctanswer', 'qtype_truefalse'),
                array(0 => get_string('false', 'qtype_truefalse'), 1 => get_string('true', 'qtype_truefalse')));

        $mform->addElement('editor', 'feedbacktrue', get_string('feedbacktrue', 'qtype_truefalse'), null, $this->editoroptions);;
        $mform->setType('feedbacktrue', PARAM_RAW);

        $mform->addElement('editor', 'feedbackfalse', get_string('feedbackfalse', 'qtype_truefalse'), null, $this->editoroptions);
        $mform->setType('feedbackfalse', PARAM_RAW);

        // Fix penalty factor at 1.
        $mform->setDefault('penalty', 1);
        $mform->freeze('penalty');
    }

    function set_data($question) {
        if (!empty($question->options->trueanswer)) {
            $trueanswer = $question->options->answers[$question->options->trueanswer];
            $draftid = file_get_submitted_draft_itemid('trueanswer');
            $answerid = $question->options->trueanswer;
            $text = $trueanswer->feedback;

            $question->correctanswer = ($trueanswer->fraction != 0);
            $question->feedbacktrue = array();
            $question->feedbacktrue['text'] = $trueanswer->feedback;
            $question->feedbacktrue['format'] = $trueanswer->feedbackformat;
            $question->feedbacktrue['text'] = file_prepare_draft_area(
                $draftid,       // draftid
                $this->context->id,    // context
                'question',     // component
                'answerfeedback',        // filarea
                !empty($answerid)?(int)$answerid:null, // itemid
                $this->fileoptions,    // options
                $text      // text
            );
            $question->feedbacktrue['itemid'] = $draftid;
        }
        if (!empty($question->options->falseanswer)) {
            $falseanswer = $question->options->answers[$question->options->falseanswer];
            $draftid = file_get_submitted_draft_itemid('falseanswer');
            $answerid = $question->options->falseanswer;
            $text = $falseanswer->feedback;

            $question->correctanswer = ($falseanswer->fraction != 0);
            $question->feedbackfalse = array();
            $question->feedbackfalse['text'] = $falseanswer->feedback;
            $question->feedbackfalse['format'] = $falseanswer->feedbackformat;
            $question->feedbackfalse['text'] = file_prepare_draft_area(
                $draftid,       // draftid
                $this->context->id,    // context
                'question',     // component
                'answerfeedback',        // filarea
                !empty($answerid)?(int)$answerid:null, // itemid
                $this->fileoptions,    // options
                $text      // text
            );
            $question->feedbackfalse['itemid'] = $draftid;
        }
        parent::set_data($question);
    }

    function qtype() {
        return 'truefalse';
    }
}
