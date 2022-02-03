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
    public static function is_random($slotid): bool {
        global $DB;
        $params = [
            'itemid' => $slotid,
            'component' => 'mod_quiz',
            'questionarea' => 'slot'
            ];
        return $DB->record_exists('question_set_references', $params);
    }

    /**
     * Get the version options for the question.
     *
     * @param int $questionid
     * @return array
     */
    public static function get_version_options($questionid): array {
        global $DB;
        $sql = "SELECT qv.id AS versionid, qv.version
                  FROM {question_versions} qv
                 WHERE qv.questionbankentryid = (SELECT DISTINCT qbe.id
                                                   FROM {question_bank_entries} qbe
                                                   JOIN {question_versions} qv ON qbe.id = qv.questionbankentryid
                                                   JOIN {question} q ON qv.questionid = q.id
                                                  WHERE q.id = ?)
              ORDER BY qv.version DESC";

        return $DB->get_records_sql($sql, [$questionid]);
    }

    /**
     * Sort the elements of an array according to a key.
     *
     * @param array $arrays
     * @param string $on
     * @param int $order
     * @return array
     */
    public static function question_array_sort($arrays, $on, $order = SORT_ASC): array {
        $element = [];
        foreach ($arrays as $array) {
            $element[$array->$on] = $array;
        }
        ksort($element, $order);
        return $element;
    }

    /**
     * Get the question id from slot id.
     *
     * @param int $slotid
     * @return mixed
     */
    public static function get_question_for_redo($slotid) {
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
     * Get random question object from the slot id.
     *
     * @param int $slotid
     * @return false|mixed|\stdClass
     */
    public static function get_random_question_data_from_slot($slotid) {
        global $DB;
        $params = [
            'itemid' => $slotid,
            'component' => 'mod_quiz',
            'questionarea' => 'slot'
        ];
        return $DB->get_record('question_set_references', $params);
    }

    /**
     * Get the question ids for specific question version.
     *
     * @param int $quizid
     * @return array
     */
    public static function get_specific_version_question_ids($quizid) {
        global $DB;
        $questionids = [];
        $sql = 'SELECT qv.questionid
                  FROM {quiz_slots} qs
                  JOIN {question_references} qr ON qr.itemid = qs.id
                  JOIN {question_versions} qv ON qv.questionbankentryid = qr.questionbankentryid
                   AND qv.version = qr.version
                 WHERE qr.version IS NOT NULL
                   AND qs.quizid = ?
                   AND qr.component = ?
                   AND qr.questionarea = ?';
        $questions = $DB->get_records_sql($sql, [$quizid, 'mod_quiz', 'slot']);
        foreach ($questions as $question) {
            $questionids [] = $question->questionid;
        }
        return $questionids;
    }

    /**
     * Get the question ids for always latest options.
     *
     * @param int $quizid
     * @return array
     */
    public static function get_always_latest_version_question_ids($quizid) {
        global $DB;
        $questionids = [];
        $sql = 'SELECT qr.questionbankentryid as entry
                  FROM {quiz_slots} qs
                  JOIN {question_references} qr ON qr.itemid = qs.id
                 WHERE qr.version IS NULL
                   AND qs.quizid = ?
                   AND qr.component = ?
                   AND qr.questionarea = ?';
        $entryids = $DB->get_records_sql($sql, [$quizid, 'mod_quiz', 'slot']);
        $questionentries = [];
        foreach ($entryids as $entryid) {
            $questionentries [] = $entryid->entry;
        }
        if (empty($questionentries)) {
            return $questionids;
        }
        list($questionidcondition, $params) = $DB->get_in_or_equal($questionentries);
        $extracondition = 'AND qv.questionbankentryid ' . $questionidcondition;
        $questionsql = "SELECT q.id
                          FROM {question} q
                          JOIN {question_versions} qv ON qv.questionid = q.id
                         WHERE qv.version = (SELECT MAX(v.version)
                                                FROM {question_versions} v
                                                JOIN {question_bank_entries} be
                                                  ON be.id = v.questionbankentryid
                                               WHERE be.id = qv.questionbankentryid)
                         $extracondition";
        $questions = $DB->get_records_sql($questionsql, $params);
        foreach ($questions as $question) {
            $questionids [] = $question->id;
        }
        return $questionids;
    }

    /**
     * Get the question structure data for the given quiz or question ids.
     *
     * @param null $quizid
     * @param array $questionids
     * @param bool $attempt
     * @return array
     */
    public static function get_question_structure_data($quizid, $questionids = [], $attempt = false) {
        global $DB;
        $params = ['quizid' => $quizid];
        $condition = '';
        $joinon = 'AND qr.version = qv.version';
        if (!empty($questionids)) {
            list($condition, $param) = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED, 'questionid');
            $condition = 'AND q.id ' . $condition;
            $joinon = '';
            $params = array_merge($params, $param);
        }
        if ($attempt) {
            $selectstart = 'q.*, slot.id AS slotid, slot.slot,';
        } else {
            $selectstart = 'slot.slot, slot.id AS slotid, q.*,';
        }
        $sql = "SELECT $selectstart
                       q.id AS questionid,
                       q.name,
                       q.qtype,
                       q.length,
                       slot.page,
                       slot.maxmark,
                       slot.requireprevious,
                       qc.id as category,
                       qc.contextid,qv.status,
                       qv.id as versionid,
                       qv.version,
                       qv.questionbankentryid
                  FROM {quiz_slots} slot
             LEFT JOIN {question_references} qr ON qr.itemid = slot.id AND qr.component = 'mod_quiz' AND qr.questionarea = 'slot'
             LEFT JOIN {question_bank_entries} qbe ON qbe.id = qr.questionbankentryid
             LEFT JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id $joinon
             LEFT JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
             LEFT JOIN {question} q ON q.id = qv.questionid
                 WHERE slot.quizid = :quizid
             $condition";
        $questiondatas = $DB->get_records_sql($sql, $params);
        foreach ($questiondatas as $questiondata) {
            $questiondata->_partiallyloaded = true;
        }
        if (!empty($questiondatas)) {
            return $questiondatas;
        }
        return [];
    }

    /**
     * Get question structure.
     *
     * @param int $quizid
     * @return array
     */
    public static function get_question_structure($quizid) {
        $firstslotsets = self::get_question_structure_data($quizid);
        $latestquestionids = self::get_always_latest_version_question_ids($quizid);
        $secondslotsets = self::get_question_structure_data($quizid, $latestquestionids);
        foreach ($firstslotsets as $key => $firstslotset) {
            foreach ($secondslotsets as $secondslotset) {
                if ($firstslotset->slotid === $secondslotset->slotid) {
                    unset($firstslotsets[$key]);
                }
            }
        }

        return self::question_array_sort(array_merge($firstslotsets, $secondslotsets), 'slot');
    }

    /**
     * Load random questions.
     *
     * @param int $quizid
     * @param array $questiondata
     * @return array
     */
    public static function question_load_random_questions($quizid, $questiondata) {
        global $DB, $USER;
        $sql = 'SELECT slot.id AS slotid,
                       slot.maxmark,
                       slot.slot,
                       slot.page,
                       qsr.filtercondition
                 FROM {question_set_references} qsr
                 JOIN {quiz_slots} slot ON slot.id = qsr.itemid
                WHERE slot.quizid = ?
                  AND qsr.component = ?
                  AND qsr.questionarea = ?';
        $randomquestiondatas = $DB->get_records_sql($sql, [$quizid, 'mod_quiz', 'slot']);

        $randomquestions = [];
        // Questions already added.
        $usedquestionids = [];
        foreach ($questiondata as $question) {
            if (isset($usedquestions[$question->id])) {
                $usedquestionids[$question->id] += 1;
            } else {
                $usedquestionids[$question->id] = 1;
            }
        }
        // Usages for this user's previous quiz attempts.
        $qubaids = new \mod_quiz\question\qubaids_for_users_attempts($quizid, $USER->id);
        $randomloader = new \core_question\local\bank\random_question_loader($qubaids, $usedquestionids);

        foreach ($randomquestiondatas as $randomquestiondata) {
            $filtercondition = json_decode($randomquestiondata->filtercondition);
            $tagids = [];
            if (isset($filtercondition->tags)) {
                foreach ($filtercondition->tags as $tag) {
                    $tagstring = explode(',', $tag);
                    $tagids [] = $tagstring[0];
                }
            }
            $randomquestiondata->randomfromcategory = $filtercondition->questioncategoryid;
            $randomquestiondata->randomincludingsubcategories = $filtercondition->includingsubcategories;
            $randomquestiondata->questionid = $randomloader->get_next_question_id($randomquestiondata->randomfromcategory,
                $randomquestiondata->randomincludingsubcategories, $tagids);
            $randomquestions [] = $randomquestiondata;
        }

        foreach ($randomquestions as $randomquestion) {
            // Should not add if there is no question found from the ramdom question loader, maybe empty category.
            if ($randomquestion->questionid === null) {
                continue;
            }
            $question = new \stdClass();
            $question->slotid = $randomquestion->slotid;
            $question->maxmark = $randomquestion->maxmark;
            $question->slot = $randomquestion->slot;
            $question->page = $randomquestion->page;
            $qdatas = question_preload_questions($randomquestion->questionid);
            $qdatas = reset($qdatas);
            foreach ($qdatas as $key => $qdata) {
                $question->$key = $qdata;
            }
            $questiondata[$question->id] = $question;
        }

        return $questiondata;
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

    /**
     * Get the version information for a question to show in the version selection dropdown.
     *
     * @param int $questionid
     * @param int $slotid
     * @return array
     */
    public static function get_question_version_info($questionid, $slotid): array {
        global $DB;
        $versiondata = [];
        $versionsoptions = self::get_version_options($questionid);
        $latestversion = reset($versionsoptions);
        // Object for using the latest version.
        $alwaysuselatest = new \stdClass();
        $alwaysuselatest->versionid = 0;
        $alwaysuselatest->version = 0;
        $alwaysuselatest->versionvalue = get_string('alwayslatest', 'quiz');
        array_unshift($versionsoptions, $alwaysuselatest);
        $referencedata = $DB->get_record('question_references', ['itemid' => $slotid]);
        if (!isset($referencedata->version) || ($referencedata->version === null)) {
            $currentversion = 0;
        } else {
            $currentversion = $referencedata->version;
        }

        foreach ($versionsoptions as $versionsoption) {
            $versionsoption->selected = false;
            if ($versionsoption->version === $currentversion) {
                $versionsoption->selected = true;
            }
            if (!isset($versionsoption->versionvalue)) {
                if ($versionsoption->version === $latestversion->version) {
                    $versionsoption->versionvalue = get_string('questionversionlatest', 'quiz', $versionsoption->version);
                } else {
                    $versionsoption->versionvalue = get_string('questionversion', 'quiz', $versionsoption->version);
                }
            }

            $versiondata[] = $versionsoption;
        }
        return $versiondata;
    }
}
