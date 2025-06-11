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

namespace tiny_wordlimit;

use assign_submission_onlinetext;
use context;
use qtype_essay_question;

/**
 * Tiny Wordlimit plugin class for detecting if wordlimits are present.
 *
 * @package    tiny_wordlimit
 * @copyright  2025 University of Graz
 * @author     André Menrath <andre.menrath@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wordlimit {
    /**
     * Returns the word limits found on the current page.
     *
     * Within an quiz it returns the wordlimits for all question where the index is the question slot.
     * Within an assignment and essay question preview the wordlimit is returned at index 1.
     *
     * @param context $context The context in which the editor is used
     * @return array
     */
    public static function detect_wordlimits_on_page($context): array {
        unset($context);

        // Check each case in order, returning as soon as we find a match.
        if ($wordlimits = self::get_quiz_attempt_wordlimits()) {
            return $wordlimits;
        }

        if ($wordlimit = self::get_question_preview_wordlimit()) {
            return [1 => $wordlimit];
        }

        if ($wordlimit = self::get_assignment_wordlimit()) {
            return [1 => $wordlimit];
        }

        return [];
    }

    /**
     * Retrieves word limits for all questions on a page of a quiz attempt.
     *
     * @return array|null Returns word limits if applicable, otherwise null
     */
    private static function get_quiz_attempt_wordlimits(): ?array {
        global $attemptobj, $slots;

        if (!$attemptobj || !$slots) {
            return null;
        }

        $wordlimits = [];
        foreach ($slots as $slot) {
            $question = $attemptobj->get_question_attempt($slot)->get_question(false);
            if ($question instanceof qtype_essay_question) {
                $wordlimits[$slot] = $question->maxwordlimit;
            }
        }

        return !empty($wordlimits) ? $wordlimits : null;
    }

    /**
     * Retrieves the word limit for a previewed essay question.
     *
     * @return int|null Returns word limits if applicable, otherwise null
     */
    private static function get_question_preview_wordlimit(): ?int {
        global $PAGE, $question;

        if ($PAGE->pagetype !== 'question-bank-previewquestion-preview') {
            return null;
        }

        if ($question instanceof qtype_essay_question && property_exists($question, 'maxwordlimit')) {
            return $question->maxwordlimit;
        }

        return null;
    }

    /**
     * Retrieves the word limit for an assignment submission.
     *
     * @return int|null Returns word limits if applicable, otherwise null
     */
    private static function get_assignment_wordlimit(): ?int {
        global $assign;

        if (!$assign) {
            return null;
        }

        $assignment = $assign->get_submission_plugin_by_type('onlinetext');
        if ($assignment instanceof assign_submission_onlinetext && $assignment->get_config('wordlimitenabled')) {
            return (int) $assignment->get_config('wordlimit');
        }

        return null;
    }
}
