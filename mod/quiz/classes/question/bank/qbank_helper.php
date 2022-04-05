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

namespace mod_quiz\question\bank;

use core_question\local\bank\question_version_status;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessmanager.php');
require_once($CFG->dirroot . '/mod/quiz/attemptlib.php');

/**
 * Helper class for question bank and its associated data.
 *
 * @package    mod_quiz
 * @category   question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbank_helper {

    /**
     * Check if the slot is a random question or not.
     *
     * @param int $slotid
     * @return bool
     */
    public static function is_random(int $slotid): bool {
        global $DB;
        $params = [
            'itemid' => $slotid,
            'component' => 'mod_quiz',
            'questionarea' => 'slot'
            ];
        return $DB->record_exists('question_set_references', $params);
    }

    /**
     * Get the available versions of a question where one of the version has the given question id.
     *
     * @param int $questionid id of a question.
     * @return \stdClass[] other versions of this question. Each object has fields versionid,
     *       version and questionid. Array is returned most recent version first.
     */
    public static function get_version_options(int $questionid): array {
        global $DB;

        return $DB->get_records_sql("
                SELECT allversions.id AS versionid,
                       allversions.version,
                       allversions.questionid

                  FROM {question_versions} allversions

                 WHERE allversions.questionbankentryid = (
                            SELECT givenversion.questionbankentryid
                              FROM {question_versions} givenversion
                             WHERE givenversion.questionid = ?
                       )
                   AND allversions.status <> ?

              ORDER BY allversions.version DESC
              ", [$questionid, question_version_status::QUESTION_STATUS_DRAFT]);
    }

    /**
     * Get the question id from slot id.
     *
     * @param int $slotid
     * @return mixed
     */
    public static function get_question_for_redo(int $slotid) {
        global $DB;
        $params = [
            'itemid' => $slotid,
            'component' => 'mod_quiz',
            'questionarea' => 'slot'
        ];
        $referencerecord = $DB->get_record('question_references', $params);
        if ($referencerecord->version === null) {
            $questionsql = 'SELECT q.id
                              FROM {question} q
                              JOIN {question_versions} qv ON qv.questionid = q.id
                             WHERE qv.version = (SELECT MAX(v.version)
                                                   FROM {question_versions} v
                                                   JOIN {question_bank_entries} be
                                                     ON be.id = v.questionbankentryid
                                                  WHERE be.id = qv.questionbankentryid)
                               AND qv.questionbankentryid = ?';
            $questionid = $DB->get_record_sql($questionsql, [$referencerecord->questionbankentryid])->id;
        } else {
            $questionid = $DB->get_field('question_versions', 'questionid',
                ['questionbankentryid' => $referencerecord->questionbankentryid,
                'version' => $referencerecord->version]);
        }
        return $questionid;
    }

    /**
     * Get the information about which questions should be used to create a quiz attempt.
     *
     * Each element in the returned array is indexed by slot.slot (slot number) an each object hass:
     * - All the field of the slot table.
     * - contextid for where the question(s) come from.
     * - category id for where the questions come from.
     * - For non-random questions, All the fields of the question table (but id is in questionid).
     *   Also question version and question bankentryid.
     * - For random questions, filtercondition, which is also unpacked into category, randomrecurse,
     *   randomtags, and note that these also have a ->name set and ->qtype set to 'random'.
     *
     * @param int $quizid the id of the quiz to load the data for.
     * @param \context $quizcontext
     * @return array indexed by slot, with information about the content of each slot.
     */
    public static function get_question_structure(int $quizid, \context $quizcontext) {
        global $DB;

        // Load all the data about each slot.
        $slotdata = $DB->get_records_sql("
                SELECT slot.slot,
                       slot.id AS slotid,
                       slot.page,
                       slot.maxmark,
                       slot.requireprevious,
                       qsr.filtercondition,
                       qv.status,
                       qv.id AS versionid,
                       qv.version,
                       qr.version AS requestedversion,
                       qv.questionbankentryid,
                       q.id AS questionid,
                       q.*,
                       qc.id AS category,
                       COALESCE(qc.contextid, qsr.questionscontextid) AS contextid

                  FROM {quiz_slots} slot

             -- case where a particular question has been added to the quiz.
             LEFT JOIN {question_references} qr ON qr.usingcontextid = :quizcontextid AND qr.component = 'mod_quiz'
                                        AND qr.questionarea = 'slot' AND qr.itemid = slot.id
             LEFT JOIN {question_bank_entries} qbe ON qbe.id = qr.questionbankentryid
             LEFT JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                                        -- Either specified version, or latest ready version.
                                        AND qv.version = COALESCE(qr.version, (
                                             SELECT MAX(version)
                                               FROM {question_versions}
                                              WHERE questionbankentryid = qbe.id AND status <> :draft
                                        ))
             LEFT JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
             LEFT JOIN {question} q ON q.id = qv.questionid

             -- Case where a random question has been added.
             LEFT JOIN {question_set_references} qsr ON qsr.usingcontextid = :quizcontextid2 AND qsr.component = 'mod_quiz'
                                        AND qsr.questionarea = 'slot' AND qsr.itemid = slot.id

                 WHERE slot.quizid = :quizid
              ORDER BY slot.slot
              ", ['draft' => question_version_status::QUESTION_STATUS_DRAFT,
                    'quizcontextid' => $quizcontext->id, 'quizcontextid2' => $quizcontext->id,
                    'quizid' => $quizid]);

        // Uppack the random info from question_set_reference.
        foreach ($slotdata as $slot) {
            // Ensure the right id is the id.
            $slot->id = $slot->slotid;

            if ($slot->filtercondition) {
                // Unpack the information about a random question.
                $filtercondition = json_decode($slot->filtercondition);
                $slot->questionid = 's' . $slot->id; // Sometimes this is used as an array key, so needs to be unique.
                $slot->category = $filtercondition->questioncategoryid;
                $slot->randomrecurse = $filtercondition->includingsubcategories;
                $slot->randomtags = isset($filtercondition->tags) ? (array) $filtercondition->tags : [];
                $slot->qtype = 'random';
                $slot->name = get_string('random', 'quiz');
                $slot->length = 1;
            } else if ($slot->qtype === null) {
                // This question must have gone missing. Put in a placeholder.
                $slot->questionid = 's' . $slot->id; // Sometimes this is used as an array key, so needs to be unique.
                $slot->category = 0;
                $slot->qtype = 'missingtype';
                $slot->name = get_string('missingquestion', 'quiz');
                $slot->maxmark = 0;
                $slot->questiontext = ' ';
                $slot->questiontextformat = FORMAT_HTML;
                $slot->length = 1;
            } else if (!\question_bank::qtype_exists($slot->qtype)) {
                // Question of unknown type found in the database. Set to placeholder question types instead.
                $slot->qtype = 'missingtype';
            } else {
                $slot->_partiallyloaded = 1;
            }
        }

        return $slotdata;
    }

    /**
     * Get this list of random selection tag ids from one of the slots returned by get_question_structure.
     *
     * @param \stdClass $slotdata one of the array elements returend by get_question_structure.
     * @return array list of tag ids.
     */
    public static function get_tag_ids_for_slot(\stdClass $slotdata): array {
        if (empty($slot->randomtags)) {
            return [];
        }

        $tagids = [];
        foreach ($slotdata->randomtags as $tag) {
            $tagids[] = $tag->id;
        }
        return $tagids;
    }

    /**
     * Choose question for redo.
     *
     * @param int $slotid
     * @param \qubaid_condition $qubaids
     * @return int
     */
    public static function choose_question_for_redo($slotid, $qubaids): int {
        // Choose the replacement question.
        if (!self::is_random($slotid)) {
            $newqusetionid = self::get_question_for_redo($slotid);
        } else {
            $tagids = [];
            $randomquestiondata = self::get_random_question_data_from_slot($slotid);
            $filtercondition = json_decode($randomquestiondata->filtercondition);
            if (isset($filtercondition->tags)) {
                foreach ($filtercondition->tags as $tag) {
                    $tagstring = explode(',', $tag);
                    $tagids [] = $tagstring[0];
                }
            }

            $randomloader = new \core_question\local\bank\random_question_loader($qubaids, []);
            $newqusetionid = $randomloader->get_next_question_id($filtercondition->questioncategoryid,
                (bool) $filtercondition->includingsubcategories, $tagids);
            if ($newqusetionid === null) {
                throw new \moodle_exception('notenoughrandomquestions', 'quiz');
            }

        }
        return $newqusetionid;
    }
}
