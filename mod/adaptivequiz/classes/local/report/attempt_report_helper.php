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

namespace mod_adaptivequiz\local\report;

use core_tag_tag;
use mod_adaptivequiz\local\catalgo;
use question_engine;
use question_usage_by_activity;
use stdClass;

/**
 * Provides data for attempt reports.
 *
 * The class is intended to be used in exporting of renderable objects only.
 *
 * @package    mod_adaptivequiz
 * @copyright  2024 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_report_helper {

    /**
     * Returns data to build the answers distribution report for the given attempt.
     *
     * The returned data has the following format:
     * array(
     *     int => (object) array(
     *         'numcorrect' => int,
     *         'numwrong' => int,
     *     )
     * )
     * where the int key is a question difficulty level, 'numcorrect' and 'numwrong' are the numbers of correct and wrong answers
     * for the key level respectively.
     *
     * @param int $attemptid
     * @return stdClass[]
     */
    public static function prepare_answers_distribution_data(int $attemptid): array {
        global $DB;

        $attemptrecord = $DB->get_record('adaptivequiz_attempt', ['id' => $attemptid], '*', MUST_EXIST);
        $adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $attemptrecord->instance], '*', MUST_EXIST);

        $quba = question_engine::load_questions_usage_by_activity($attemptrecord->uniqueid);

        $data = [];
        // This step is required to ensure we have an entry for each difficulty level, even when no questions of
        // such level were administered.
        for ($i = $adaptivequiz->lowestlevel; $i <= $adaptivequiz->highestlevel; $i++) {
            $dataitem = new stdClass();
            $dataitem->numcorrect = 0;
            $dataitem->numwrong = 0;

            $data[$i] = $dataitem;
        }

        foreach ($quba->get_slots() as $slot) {
            $question = $quba->get_question($slot);
            $tags = core_tag_tag::get_item_tags_array('core_question', 'question', $question->id);
            $difficulty = adaptivequiz_get_difficulty_from_tags($tags);

            $answeredcorrectly = $quba->get_question_mark($slot) > 0;
            $answeredcorrectly ? $data[$difficulty]->numcorrect++ : $data[$difficulty]->numwrong++;
        }

        return $data;
    }

    /**
     * Returns data to build the questions administration report for the given attempt.
     *
     *  The returned data has the following format:
     *  array(
     *      int => (object) array(
     *          'targetdifficulty' => int,
     *          'administereddifficulty' => int,
     *          'abilitymeasure' => float,
     *          'standarderrormax' => float,
     *          'standarderrormin' => float,
     *          'standarderror' => float,
     *          'answeredcorrectly' => bool,
     *      )
     *  )
     *  where the int key is a question slot number (sequence number), the properties have the following meaning:
     *  targetdifficulty - the difficulty level the algorithm has suggested for the administered question
     *  administereddifficulty - the difficulty level the actually administered question had
     *  standarderrormax - the maximum possible value of ability measure given the current standard error value
     *  standarderrormin - the opposite to the above
     *  standarderror - the percentage value
     *  answeredcorrectly - whether the administered question was answered correctly
     *
     * @param int $attemptid
     * @return stdClass[]
     */
    public static function prepare_administration_data(int $attemptid): array {
        global $DB;

        $attemptrecord = $DB->get_record('adaptivequiz_attempt', ['id' => $attemptid], '*', MUST_EXIST);
        $adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $attemptrecord->instance], '*', MUST_EXIST);

        $quba = question_engine::load_questions_usage_by_activity($attemptrecord->uniqueid);

        $data = [];

        $numattempted = 0;
        $difficultysum = 0;
        $sumcorrect = 0;
        $sumincorrect = 0;

        foreach ($quba->get_slots() as $i => $slot) {
            $targetlevel = ($i > 0)
                ? self::compute_target_difficulty_level($quba, $adaptivequiz, $slot, $numattempted)
                : $adaptivequiz->startinglevel;

            $question = $quba->get_question($slot);
            $tags = core_tag_tag::get_item_tags_array('core_question', 'question', $question->id);
            $difficulty = adaptivequiz_get_difficulty_from_tags($tags);

            $answeredcorrectly = $quba->get_question_mark($slot) > 0;
            $answeredcorrectly ? $sumcorrect++ : $sumincorrect++;

            $qdifficultylogits = catalgo::convert_linear_to_logit($difficulty, $adaptivequiz->lowestlevel,
                $adaptivequiz->highestlevel);

            $difficultysum = $difficultysum + $qdifficultylogits;
            $numattempted++;

            $abilitylogits = catalgo::estimate_measure($difficultysum, $numattempted, $sumcorrect,
                $sumincorrect);
            $abilityfraction = 1 / ( 1 + exp( (-1 * $abilitylogits) ) );
            $ability = (($adaptivequiz->highestlevel - $adaptivequiz->lowestlevel) * $abilityfraction) + $adaptivequiz->lowestlevel;

            $stderrlogits = catalgo::estimate_standard_error($numattempted, $sumcorrect, $sumincorrect);
            $stderr = catalgo::convert_logit_to_percent($stderrlogits);

            $errormax = min($adaptivequiz->highestlevel,
                $ability + ($stderr * ($adaptivequiz->highestlevel - $adaptivequiz->lowestlevel)));
            $errormin = max($adaptivequiz->lowestlevel,
                $ability - ($stderr * ($adaptivequiz->highestlevel - $adaptivequiz->lowestlevel)));

            $dataitem = new stdClass();
            $dataitem->targetdifficulty = $targetlevel;
            $dataitem->administereddifficulty = $difficulty;
            $dataitem->abilitymeasure = $ability;
            $dataitem->standarderrormax = $errormax;
            $dataitem->standarderrormin = $errormin;
            $dataitem->standarderror = $stderr;
            $dataitem->answeredcorrectly = $answeredcorrectly;

            $data[$i+1] = $dataitem;
        }

        return $data;
    }

    /**
     * Computes the target difficulty level based on the answer to previous question.
     *
     * @param question_usage_by_activity $quba
     * @param stdClass $adaptivequiz A record from {adaptivequiz}.
     * @param int $slot Slot of the question to be administered.
     * @param int $numattempted Number of questions already attempted.
     * @return int
     */
    private static function compute_target_difficulty_level(
        question_usage_by_activity $quba,
        stdClass $adaptivequiz,
        int $slot,
        int $numattempted
    ): int {
        $previousslot = $slot - 1;

        $previousquestion = $quba->get_question($previousslot);
        $previousqtags = core_tag_tag::get_item_tags_array('core_question', 'question', $previousquestion->id);
        $previousdifficulty = adaptivequiz_get_difficulty_from_tags($previousqtags);

        $difficultylogits = catalgo::convert_linear_to_logit($previousdifficulty, $adaptivequiz->lowestlevel,
            $adaptivequiz->highestlevel);

        $answeredcorrectly = $quba->get_question_mark($previousslot) > 0;
        if ($answeredcorrectly) {
            $targetlevel = round(catalgo::map_logit_to_scale($difficultylogits + 2 / $numattempted,
                $adaptivequiz->highestlevel, $adaptivequiz->lowestlevel));
            if ($targetlevel == $previousdifficulty && $targetlevel < $adaptivequiz->highestlevel) {
                $targetlevel++;
            }

            return $targetlevel;
        }

        $targetlevel = round(catalgo::map_logit_to_scale($difficultylogits - 2 / $numattempted,
            $adaptivequiz->highestlevel, $adaptivequiz->lowestlevel));
        if ($targetlevel == $previousdifficulty && $targetlevel > $adaptivequiz->lowestlevel) {
            $targetlevel--;
        }

        return $targetlevel;
    }
}
