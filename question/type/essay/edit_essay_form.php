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
 * Defines the editing form for the essay question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * essay editing form definition.
 */
class question_edit_essay_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {
        $mform->addElement('editor', 'feedback', get_string('feedback', 'quiz'), null, $this->editoroptions);
        $mform->setType('feedback', PARAM_RAW);

        $mform->addElement('hidden', 'fraction', 0);
        $mform->setType('fraction', PARAM_RAW);

        //don't need this default element.
        $mform->removeElement('penalty');
        $mform->addElement('hidden', 'penalty', 0);
        $mform->setType('penalty', PARAM_RAW);
    }

    function data_preprocessing($question) {
        if (!empty($question->options) && !empty($question->options->answers)) {
            $answer = reset($question->options->answers);
            $question->feedback = array();
            $draftid = file_get_submitted_draft_itemid('feedback');
            $question->feedback['text'] = file_prepare_draft_area(
                $draftid,       // draftid
                $this->context->id,    // context
                'question',   // component
                'answerfeedback',             // filarea
                !empty($answer->id)?(int)$answer->id:null, // itemid
                $this->fileoptions,    // options
                $answer->feedback      // text
            );
            $question->feedback['format'] = $answer->feedbackformat;
            $question->feedback['itemid'] = $draftid;
        }
        $question->penalty = 0;
        return $question;
    }

    function qtype() {
        return 'essay';
    }
}
