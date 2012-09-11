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
 * Embedded answer (Cloze) question importer.
 *
 * @package   qformat_multianswer
 * @copyright 2003 Henrik Kaipe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Importer that imports a text file containing a single Multianswer question
 * from a text file.
 *
 * @copyright 2003 Henrik Kaipe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_multianswer extends qformat_default {

    public function provide_import() {
        return true;
    }

    public function readquestions($lines) {
        question_bank::get_qtype('multianswer'); // Ensure the multianswer code is loaded.

        // For this class the method has been simplified as
        // there can never be more than one question for a
        // multianswer import.
        $questions = array();

        $questiontext = array();
        $questiontext['text'] = implode('', $lines);
        $questiontext['format'] = FORMAT_MOODLE;
        $questiontext['itemid'] = '';
        $question = qtype_multianswer_extract_question($questiontext);
        $question->questiontext = $question->questiontext['text'];
        $question->questiontextformat = 0;

        $question->qtype = 'multianswer';
        $question->generalfeedback = '';
        $question->generalfeedbackformat = FORMAT_MOODLE;
        $question->length = 1;
        $question->penalty = 0.3333333;

        if (!empty($question)) {
            $question->name = $this->create_default_question_name($question->questiontext, get_string('questionname', 'question'));
            $questions[] = $question;
        }

        return $questions;
    }
}
