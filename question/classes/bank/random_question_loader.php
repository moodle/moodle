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

namespace core_question\bank;


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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class random_question_loader {
    /** @var \qubaid_condition which usages to consider previous attempts from. */
    protected $qubaids;

    /** @var array qtypes that cannot be used by random questions. */
    protected $excludedqtypes;

    /** @var array categoryid & include subcategories => num previous uses => questionid => 1. */
    protected $availablequestionscache = array();

    /**
     * @var array questionid => num recent uses. Questions that have been used,
     * but that is not yet recorded in the DB.
     */
    protected $recentlyusedquestions;

    /**
     * Constructor.
     * @param \qubaid_condition $qubaids the usages to consider when counting previous uses of each question.
     * @param array $usedquestions questionid => number of times used count. If we should allow for
     *      further existing uses of a question in addition to the ones in $qubaids.
     */
    public function __construct(\qubaid_condition $qubaids, array $usedquestions = array()) {
        $this->qubaids = $qubaids;
        $this->recentlyusedquestions = $usedquestions;

        foreach (\question_bank::get_all_qtypes() as $qtype) {
            if (!$qtype->is_usable_by_random()) {
                $this->excludedqtypes[] = $qtype->name();
            }
        }
    }

    /**
     * Pick a question at random from the given category, from among those with the fewest uses.
     *
     * It is up the the caller to verify that the cateogry exists. An unknown category
     * behaves like an empty one.
     *
     * @param int $categoryid the id of a category in the question bank.
     * @param bool $includesubcategories wether to pick a question from exactly
     *      that category, or that category and subcategories.
     * @return int|null the id of the question picked, or null if there aren't any.
     */
    public function get_next_question_id($categoryid, $includesubcategories) {
        $this->ensure_questions_for_category_loaded($categoryid, $includesubcategories);

        $categorykey = $this->get_category_key($categoryid, $includesubcategories);
        if (empty($this->availablequestionscache[$categorykey])) {
            return null;
        }

        reset($this->availablequestionscache[$categorykey]);
        $lowestcount = key($this->availablequestionscache[$categorykey]);
        reset($this->availablequestionscache[$categorykey][$lowestcount]);
        $questionid = key($this->availablequestionscache[$categorykey][$lowestcount]);
        $this->use_question($questionid);
        return $questionid;
    }

    /**
     * Get the key into {@link $availablequestionscache} for this combination of options.
     * @param int $categoryid the id of a category in the question bank.
     * @param bool $includesubcategories wether to pick a question from exactly
     *      that category, or that category and subcategories.
     * @return string the cache key.
     */
    protected function get_category_key($categoryid, $includesubcategories) {
        if ($includesubcategories) {
            return $categoryid . '|1';
        } else {
            return $categoryid . '|0';
        }
    }

    /**
     * Populate {@link $availablequestionscache} for this combination of options.
     * @param int $categoryid the id of a category in the question bank.
     * @param bool $includesubcategories wether to pick a question from exactly
     *      that category, or that category and subcategories.
     */
    protected function ensure_questions_for_category_loaded($categoryid, $includesubcategories) {
        global $DB;

        $categorykey = $this->get_category_key($categoryid, $includesubcategories);

        if (isset($this->availablequestionscache[$categorykey])) {
            // Data is already in the cache, nothing to do.
            return;
        }

        // Load the available questions from the question bank.
        if ($includesubcategories) {
            $categoryids = question_categorylist($categoryid);
        } else {
            $categoryids = array($categoryid);
        }

        list($extraconditions, $extraparams) = $DB->get_in_or_equal($this->excludedqtypes,
                SQL_PARAMS_NAMED, 'excludedqtype', false);

        $questionidsandcounts = \question_bank::get_finder()->get_questions_from_categories_with_usage_counts(
                $categoryids, $this->qubaids, 'q.qtype ' . $extraconditions, $extraparams);
        if (!$questionidsandcounts) {
            // No questions in this category.
            $this->availablequestionscache[$categorykey] = array();
            return;
        }

        // Put all the questions with each value of $prevusecount in separate arrays.
        $idsbyusecount = array();
        foreach ($questionidsandcounts as $questionid => $prevusecount) {
            if (isset($this->recentlyusedquestions[$questionid])) {
                // Recently used questions are never returned.
                continue;
            }
            $idsbyusecount[$prevusecount][] = $questionid;
        }

        // Now put that data into our cache. For each count, we need to shuffle
        // questionids, and make those the keys of an array.
        $this->availablequestionscache[$categorykey] = array();
        foreach ($idsbyusecount as $prevusecount => $questionids) {
            shuffle($questionids);
            $this->availablequestionscache[$categorykey][$prevusecount] = array_combine(
                    $questionids, array_fill(0, count($questionids), 1));
        }
        ksort($this->availablequestionscache[$categorykey]);
    }

    /**
     * Update the internal data structures to indicate that a given question has
     * been used one more time.
     *
     * @param int $questionid the question that is being used.
     */
    protected function use_question($questionid) {
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
     * Check whether a given question is available in a given category. If so, mark it used.
     *
     * @param int $categoryid the id of a category in the question bank.
     * @param bool $includesubcategories wether to pick a question from exactly
     *      that category, or that category and subcategories.
     * @param int $questionid the question that is being used.
     * @return bool whether the question is available in the requested category.
     */
    public function is_question_available($categoryid, $includesubcategories, $questionid) {
        $this->ensure_questions_for_category_loaded($categoryid, $includesubcategories);
        $categorykey = $this->get_category_key($categoryid, $includesubcategories);

        foreach ($this->availablequestionscache[$categorykey] as $questionids) {
            if (isset($questionids[$questionid])) {
                $this->use_question($questionid);
                return true;
            }
        }

        return false;
    }
}
