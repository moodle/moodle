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

namespace mod_adaptivequiz\local\questionanalysis;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/tag/lib.php');

use core_tag_tag;
use Exception;
use InvalidArgumentException;
use mod_adaptivequiz\local\attempt\attempt_state;
use mod_adaptivequiz\local\questionanalysis\statistics\question_statistic;
use question_engine;
use stdClass;

/**
 * Questions-analyser class.
 *
 * The class provides a mechanism for loading and analysing question usage, performance, and efficacy.
 *
 * @package    mod_adaptivequiz
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_analyser {

    /** @var array $questions An array of all questions loaded and their stats */
    protected $questions = array();

    /** @var array $statistics An array of the statistics added to this report */
    protected $statistics = array();

    /**
     * Constructor - Create a new analyser.
     *
     * @return void
     */
    public function __construct() {

    }

    /**
     * Load attempts from an adaptive quiz instance.
     *
     * @param int $instance
     */
    public function load_attempts(int $instance): void {
        global $DB;

        $adaptivequiz  = $DB->get_record('adaptivequiz', ['id' => $instance], '*');

        // Get all of the completed attempts for this adaptive quiz instance.
        $attempts  = $DB->get_records('adaptivequiz_attempt',
            ['instance' => $instance, 'attemptstate' => attempt_state::COMPLETED]);

        foreach ($attempts as $attempt) {
            if ($attempt->uniqueid == 0) {
                continue;
            }

            $user = $DB->get_record('user', ['id' => $attempt->userid]);
            if (!$user) {
                $user = new stdClass();
                $user->firstname = get_string('unknownuser', 'adaptivequiz');
                $user->lastname = '#' . $attempt->userid;
            }

            // For each attempt, get the attempt's final score.
            $score = new attempt_score($attempt->measure, $attempt->standarderror, $adaptivequiz->lowestlevel,
                $adaptivequiz->highestlevel);

            // For each attempt, loop through all questions asked and add that usage
            // to the question.
            $quba = question_engine::load_questions_usage_by_activity($attempt->uniqueid);
            foreach ($quba->get_slots() as $i => $slot) {
                $question = $quba->get_question($slot);

                // Create a question-analyser for the question.
                if (empty($this->questions[$question->id])) {
                    $tags = core_tag_tag::get_item_tags_array('core_question', 'question', $question->id);
                    $difficulty = adaptivequiz_get_difficulty_from_tags($tags);
                    $this->questions[$question->id] = new question_analyser($quba->get_owning_context(), $question,
                        $difficulty, $adaptivequiz->lowestlevel, $adaptivequiz->highestlevel);
                }

                // Record the attempt score and the individual question result.
                $correct = ($quba->get_question_mark($slot) > 0);
                $answer = $quba->get_response_summary($slot);
                $this->questions[$question->id]->add_result($attempt->id, $user, $score, $correct, $answer);
            }
        }
    }

    /**
     * Add a statistic to calculate.
     *
     * @param string $key A key to identify this statistic for sorting and printing.
     * @param question_statistic $statistic
     * @return void
     */
    public function add_statistic($key, question_statistic $statistic) {
        if (!empty($this->statistics[$key])) {
            throw new InvalidArgumentException("Statistic key '$key' is already in use.");
        }
        $this->statistics[$key] = $statistic;
        foreach ($this->questions as $question) {
            $question->add_statistic($key, $statistic);
        }
    }

    /**
     * Answer a header row.
     *
     * @return array
     */
    public function get_header() {
        $header = array();
        $header['id'] = get_string('id', 'adaptivequiz');
        $header['name'] = get_string('adaptivequizname', 'adaptivequiz');
        $header['level'] = get_string('attemptquestion_level', 'adaptivequiz');
        foreach ($this->statistics as $key => $statistic) {
            $header[$key] = $statistic->get_display_name();
        }
        return $header;
    }

    /**
     * Return an array of table records, sorted by the statistics given.
     *
     * @param string $sort Which statistic to sort on.
     * @param string $direction ASC or DESC.
     * @return array
     */
    public function get_records($sort = null, $direction = 'ASC') {
        if (empty($this->questions)) {
            return [];
        }

        $records = [];
        foreach ($this->questions as $question) {
            $record = [];
            $record[] = $question->get_question_definition()->id;
            $record[] = $question->get_question_definition()->name;
            $record[] = $question->get_question_level();
            foreach ($this->statistics as $key => $statistic) {
                $record[] = $question->get_statistic_result($key)->printable();
            }
            $records[] = $record;
        }

        if ($direction != 'ASC' && $direction != 'DESC') {
            throw new InvalidArgumentException('Invalid sort direction. Must be SORT_ASC or SORT_DESC, \''.$direction.'\' given.');
        }
        if ($direction == 'DESC') {
            $direction = SORT_DESC;
        } else {
            $direction = SORT_ASC;
        }

        if (!is_null($sort)) {
            $sortkeys = [];
            foreach ($this->questions as $question) {
                if ($sort == 'name') {
                    $sortkeys[] = $question->get_question_definition()->name;
                    $sorttype = SORT_REGULAR;
                } else if ($sort == 'level') {
                    $sortkeys[] = $question->get_question_level();
                    $sorttype = SORT_NUMERIC;
                } else {
                    $sortkeys[] = $question->get_statistic_result($sort)->sortable();
                    $sorttype = SORT_NUMERIC;
                }
            }
            array_multisort($sortkeys, $direction, $sorttype, $records);
        }

        return $records;
    }

    /**
     * Answer a question-analyzer for a particular question id analyze
     *
     * @param int $qid The question id
     * @return question_analyser
     * @throws Exception
     */
    public function get_question_analyzer($qid) {
        if (!isset($this->questions[$qid])) {
            throw new Exception('Question-id not found.');
        }
        return $this->questions[$qid];
    }

    /**
     * Answer the record for a single question
     *
     * @param int $qid The question id
     * @return array
     * @throws Exception
     */
    public function get_record($qid) {
        $question = $this->get_question_analyzer($qid);
        $record = [];
        $record[] = $question->get_question_definition()->id;
        $record[] = $question->get_question_definition()->name;
        $record[] = $question->get_question_level();
        foreach ($this->statistics as $key => $statistic) {
            $record[] = $question->get_statistic_result($key)->printable();
        }
        return $record;
    }
}
