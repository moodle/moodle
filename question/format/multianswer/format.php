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
 * This defines the core classes of the Moodle question engine.
 *
 * @package qformat
 * @subpackage qformat_multianswer
 * @copyright 2003 Henrik Kaipe
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Importer that imports a text file containing a single Multianswer question
 * from a text file.
 *
 * @copyright 2003 Henrik Kaipe
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_multianswer extends qformat_default {

    function provide_import() {
      return true;
    }

    function readquestions($lines) {
        // For this class the method has been simplified as
        // there can never be more than one question for a
        // multianswer import
        $questions = array();

        $question = qtype_multianswer_extract_question(implode('', $lines));
        $question->qtype = MULTIANSWER;
        $question->generalfeedback = '';
        $question->course = $this->course;

        if (!empty($question)) {
            $name = html_to_text(implode(' ', $lines));
            $name = preg_replace('/{[^}]*}/', '', $name);
            $name = trim($name);

            if ($name) {
                $question->name = shorten_text($name, 45);
            } else {
                // We need some name, so use the current time, since that will be
                // reasonably unique.
                $question->name = userdate(time());
            }

            $questions[] = $question;
        }

        return $questions;
    }
}
