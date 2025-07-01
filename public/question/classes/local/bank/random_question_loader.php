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
 * A class for efficiently finds questions at random from the question bank.
 *
 * @package   core_question
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

/**
 * This class efficiently finds questions at random from the question bank.
 *
 * You can ask for questions at random one at a time. Each time you ask, you
 * pass a category id, and whether to pick from that category and all subcategories
 * or just that category.
 *
 * The number of teams each question has been used is tracked, and we will always
 * return a question from among those elegible that has been used the fewest times.
 * So, if there are questions that have not been used yet in the category asked for,
 * one of those will be returned. However, within one instantiation of this class,
 * we will never return a given question more than once, and we will never return
 * questions passed into the constructor as $usedquestions.
 *
 * @copyright 2015 The Open University
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class random_question_loader {
    /** @var \qubaid_condition which usages to consider previous attempts from. */
    protected $qubaids;

    /**
     * @var array Array of question types to include in random questions.
     */
    protected $includedqtypes = [];

    /** @var array categoryid & include subcategories => num previous uses => questionid => 1. */
    protected $availablequestionscache = [];

    /**
     * @var array questionid => num recent uses. Questions that have been used,
     * but that is not yet recorded in the DB.
     */
    protected $recentlyusedquestions;

    /**
     * Constructor.
     *
     * @param \qubaid_condition $qubaids the usages to consider when counting previous uses of each question.
     * @param array $usedquestions questionid => number of times used count. If we should allow for
     *      further existing uses of a question in addition to the ones in $qubaids.
     */
    public function __construct(\qubaid_condition $qubaids, array $usedquestions = []) {
        $this->qubaids = $qubaids;
        $this->recentlyusedquestions = $usedquestions;

        // Load the possible question types we can select from.
        foreach (\question_bank::get_all_qtypes() as $qtype) {
            if ($qtype->is_usable_by_random()) {
                $this->includedqtypes[] = $qtype->name();
            }
        }
    }

    /**
     * Pick a random question based on filter conditions
     *
     * @param array $filters filter array
     * @return int|null
     */
    public function get_next_filtered_question_id(array $filters): ?int {
        $this->ensure_filtered_questions_loaded($filters);

        $key = $this->get_filtered_questions_key($filters);
        if (empty($this->availablequestionscache[$key])) {
            return null;
        }

        reset($this->availablequestionscache[$key]);
        $lowestcount = key($this->availablequestionscache[$key]);
        reset($this->availablequestionscache[$key][$lowestcount]);
        $questionid = key($this->availablequestionscache[$key][$lowestcount]);
        $this->use_question($questionid);
        return $questionid;
    }


    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('get_next_filtered_question_id()', since: '4.3', mdl: 'MDL-72321', final: true)]
    public function get_next_question_id($categoryid, $includesubcategories, $tagids = []): ?int {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Key for filtered questions.
     * This function replace get_category_key
     *
     * @param array $filters filter array
     * @return String
     */
    protected function get_filtered_questions_key(array $filters): String {
        return sha1(json_encode($filters));
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('get_filtered_questions_key()', since: '4.3', mdl: 'MDL-72321', final: true)]
    protected function get_category_key($categoryid, $includesubcategories, $tagids = []): string {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Populate {@see $availablequestionscache} according to filter conditions.
     *
     * @param array $filters filter array
     * @return void
     */
    protected function ensure_filtered_questions_loaded(array $filters) {
        global $DB;

        // Check if this is already done.
        $key = $this->get_filtered_questions_key($filters);
        if (isset($this->availablequestionscache[$key])) {
            // Data is already in the cache, nothing to do.
            return;
        }

        // Build filter conditions.
        $params = [];
        $filterconditions = [];
        foreach (filter_condition_manager::get_condition_classes() as $conditionclass) {
            $filter = $conditionclass::get_filter_from_list($filters);
            if (is_null($filter)) {
                continue;
            }

            [$filterwhere, $filterparams] = $conditionclass::build_query_from_filter($filter);
            if (!empty($filterwhere)) {
                $filterconditions[] = '(' . $filterwhere . ')';
            }
            if (!empty($filterparams)) {
                $params = array_merge($params, $filterparams);
            }
        }
        $filtercondition = $filterconditions ? 'AND ' . implode(' AND ', $filterconditions) : '';

        // Prepare qtype check.
        [$qtypecondition, $qtypeparams] = $DB->get_in_or_equal($this->includedqtypes,
            SQL_PARAMS_NAMED, 'includedqtype');
        if ($qtypecondition) {
            $qtypecondition = 'AND q.qtype ' . $qtypecondition;
        }

        $questionidsandcounts = $DB->get_records_sql_menu("
                SELECT q.id,
                       (
                           SELECT COUNT(1)
                             FROM {$this->qubaids->from_question_attempts('qa')}
                            WHERE qa.questionid = q.id AND {$this->qubaids->where()}
                       ) AS previous_attempts

                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid

                 WHERE q.parent = :noparent
                   $qtypecondition
                   $filtercondition
                   AND qv.version = (
                           SELECT MAX(version)
                             FROM {question_versions}
                            WHERE questionbankentryid = qbe.id
                              AND status = :ready
                       )

              ORDER BY previous_attempts
            ", array_merge(
                $params,
                $this->qubaids->from_where_params(),
                ['noparent' => 0, 'ready' => question_version_status::QUESTION_STATUS_READY],
                $qtypeparams,
            ));

        if (!$questionidsandcounts) {
            // No questions in this category.
            $this->availablequestionscache[$key] = [];
            return;
        }

        // Put all the questions with each value of $prevusecount in separate arrays.
        $idsbyusecount = [];
        foreach ($questionidsandcounts as $questionid => $prevusecount) {
            if (isset($this->recentlyusedquestions[$questionid])) {
                // Recently used questions are never returned.
                continue;
            }
            $idsbyusecount[$prevusecount][] = $questionid;
        }

        // Now put that data into our cache. For each count, we need to shuffle
        // questionids, and make those the keys of an array.
        $this->availablequestionscache[$key] = [];
        foreach ($idsbyusecount as $prevusecount => $questionids) {
            shuffle($questionids);
            $this->availablequestionscache[$key][$prevusecount] = array_combine(
                $questionids, array_fill(0, count($questionids), 1));
        }
        ksort($this->availablequestionscache[$key]);
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('ensure_filtered_questions_loaded()', since: '4.3', mdl: 'MDL-72321', final: true)]
    protected function ensure_questions_for_category_loaded($categoryid, $includesubcategories, $tagids = []): void {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Update the internal data structures to indicate that a given question has
     * been used one more time.
     *
     * @param int $questionid the question that is being used.
     */
    protected function use_question($questionid): void {
        if (isset($this->recentlyusedquestions[$questionid])) {
            $this->recentlyusedquestions[$questionid] += 1;
        } else {
            $this->recentlyusedquestions[$questionid] = 1;
        }

        foreach ($this->availablequestionscache as $categorykey => $questionsforcategory) {
            foreach ($questionsforcategory as $numuses => $questionids) {
                if (!isset($questionids[$questionid])) {
                    continue;
                }
                unset($this->availablequestionscache[$categorykey][$numuses][$questionid]);
                if (empty($this->availablequestionscache[$categorykey][$numuses])) {
                    unset($this->availablequestionscache[$categorykey][$numuses]);
                }
            }
        }
    }

    /**
     * Get filtered questions.
     *
     * @param array $filters filter array
     * @return array list of filtered questions
     */
    protected function get_filtered_question_ids(array $filters): array {
        $this->ensure_filtered_questions_loaded($filters);
        $key = $this->get_filtered_questions_key($filters);

        $cachedvalues = $this->availablequestionscache[$key];
        $questionids = [];

        foreach ($cachedvalues as $usecount => $ids) {
            $questionids = array_merge($questionids, array_keys($ids));
        }

        return $questionids;
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('get_filtered_question_ids()', since: '4.3', mdl: 'MDL-72321', final: true)]
    protected function get_question_ids($categoryid, $includesubcategories, $tagids = []): array {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Check whether a given question is available in a given category. If so, mark it used.
     * If an optional list of tag ids are provided, then the question must be tagged with
     * ALL of the provided tags to be considered as available.
     *
     * @param array $filters filter array
     * @param int $questionid the question that is being used.
     * @return bool whether the question is available in the requested category.
     */
    public function is_filtered_question_available(array $filters, int $questionid): bool {
        $this->ensure_filtered_questions_loaded($filters);
        $categorykey = $this->get_filtered_questions_key($filters);

        foreach ($this->availablequestionscache[$categorykey] as $questionids) {
            if (isset($questionids[$questionid])) {
                $this->use_question($questionid);
                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('is_filtered_question_available()', since: '4.3', mdl: 'MDL-72321', final: true)]
    public function is_question_available($categoryid, $includesubcategories, $questionid, $tagids = []): bool {
            \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Get the list of available questions for the given criteria.
     *
     * @param array $filters filter array
     * @param int $limit Maximum number of results to return.
     * @param int $offset Number of items to skip from the begging of the result set.
     * @param string[] $fields The fields to return for each question.
     * @return \stdClass[] The list of question records
     */
    public function get_filtered_questions($filters, $limit = 100, $offset = 0, $fields = []) {
        global $DB;

        $questionids = $this->get_filtered_question_ids($filters);

        if (empty($questionids)) {
            return [];
        }

        if (empty($fields)) {
            // Return all fields.
            $fieldsstring = '*';
        } else {
            $fieldsstring = implode(',', $fields);
        }

        // Create the query to get the questions (validate that at least we have a question id. If not, do not execute the sql).
        $hasquestions = false;
        if (!empty($questionids)) {
            $hasquestions = true;
        }
        if ($hasquestions) {
            [$condition, $param] = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED, 'questionid');
            $condition = 'WHERE q.id ' . $condition;
            $sql = "SELECT {$fieldsstring}
                      FROM (SELECT q.*, qbe.questioncategoryid as category
                      FROM {question} q
                      JOIN {question_versions} qv ON qv.questionid = q.id
                      JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                      {$condition}) q
                  ORDER BY q.id";

            return $DB->get_records_sql($sql, $param, $offset, $limit);
        } else {
            return [];
        }
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('get_filtered_questions()', since: '4.3', mdl: 'MDL-72321', final: true)]
    public function get_questions($categoryid, $includesubcategories, $tagids = [], $limit = 100, $offset = 0, $fields = []) {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Count number of filtered questions
     *
     * @param array $filters filter array
     * @return int number of question
     */
    public function count_filtered_questions(array $filters): int {
        $questionids = $this->get_filtered_question_ids($filters);
        return count($questionids);
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('count_filtered_questions()', since: '4.3', mdl: 'MDL-72321', final: true)]
    public function count_questions($categoryid, $includesubcategories, $tagids = []): int {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }
}
