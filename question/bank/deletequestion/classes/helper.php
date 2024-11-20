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

namespace qbank_deletequestion;

/**
 * Class helper of qbank_deletequestion.
 *
 * @package qbank_deletequestion
 * @copyright 2023 The Open University
 * @since Moodle 4.2
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get the confirmation message of delete question.
     *
     * @param array $questionids List of id questions.
     * @param bool $deleteallversions Delete all question version or not.
     * @return array List confirmation message.
     */
    public static function get_delete_confirmation_message(array $questionids, bool $deleteallversions): array {
        global $DB;
        $questionnames = '';
        $inuse = false;
        $questionversions = [];
        $countselectedquestion = count($questionids);

        if ($deleteallversions) {
            $versionsofeachquestionbankentry = \question_bank::get_all_versions_of_questions($questionids);
            foreach ($versionsofeachquestionbankentry as $entryid => $versions) {
                // Re-order to oldest first.
                $versionsofeachquestionbankentry[$entryid] = array_reverse($versions, true);
                // Flip the array to list question by question id. [ qid => version ].
                $questionversions += array_flip($versions);
            }
            // Flatten an array.
            $questionids = array_merge(...$versionsofeachquestionbankentry);
        }

        // Get the names of all the questions.
        $questions = $DB->get_records_list('question', 'id', $questionids, '', 'id, name');

        // Build the message.
        foreach ($questionids as $questionid) {
            if (questions_in_use([$questionid])) {
                $questionnames .= '* ';
                $inuse = true;
            }
            $questionname = format_string($questions[$questionid]->name);
            if (isset($questionversions[$questionid])) {
                $a = new \stdClass();
                $a->name = $questionname;
                $a->version = $questionversions[$questionid];
                $questionnames .= get_string('questionnameandquestionversion',
                    'question', $a) . '<br />';
            } else {
                $questionnames .= $questionname . '<br />';
            }
        }

        // Add the in-use message if required.
        if ($inuse) {
            $questionnames .= '<br />'.get_string('questionsinuse', 'question');
        }

        // Add in the right tile and message text.
        $confirmtitle = [
            'confirmtitle' => $countselectedquestion > 1 ? get_string('deleteversiontitle_plural',
                'question') : get_string('deleteversiontitle', 'question'),
        ];
        $message = get_string('deleteselectedquestioncheck', 'question', $questionnames);
        if ($deleteallversions) {
            $confirmtitle = [
                'confirmtitle' => get_string('deletequestiontitle', 'question'),
            ];
            $message = get_string('deletequestioncheck', 'question', $questionnames);
            if ($countselectedquestion > 1) {
                $confirmtitle = [
                    'confirmtitle' => get_string('deletequestiontitle_plural', 'question'),
                ];
                $message = get_string('deletequestionscheck', 'question', $questionnames);
            }
        }

        return [$confirmtitle, $message];
    }

    /**
     * Delete questions has (single/multiple) version.
     *
     * @param array $questionids List of questionid.
     * @param bool $deleteallversions Delete all question version or not.
     */
    public static function delete_questions(array $questionids, bool $deleteallversions): void {
        if ($deleteallversions) {
            // Get all the question id from multidimensional array.
            $listofquestions = \question_bank::get_all_versions_of_questions($questionids);
            // Flatten an array.
            $questionids = array_merge(...$listofquestions);
        }
        foreach ($questionids as $questionid) {
            $questionid = (int) $questionid;
            question_require_capability_on($questionid, 'edit');
            question_delete_question($questionid);
        }
    }
}
