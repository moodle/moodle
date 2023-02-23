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

namespace qbank_statistics;

use core_question\statistics\questions\all_calculated_for_qubaid_condition;
use core_component;

/**
 * Helper for statistics
 *
 * @package    qbank_statistics
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * @var float Threshold to determine 'Needs checking?'
     */
    private const NEED_FOR_REVISION_LOWER_THRESHOLD = 30;

    /**
     * @var float Threshold to determine 'Needs checking?'
     */
    private const NEED_FOR_REVISION_UPPER_THRESHOLD = 50;

    /**
     * For a list of questions find all the places (defined by (component, contextid) where there are attempts.
     *
     * @param int[] $questionids array of question ids that we are interested in.
     * @return \stdClass[] list of objects with fields ->component and ->contextid.
     */
    private static function get_all_places_where_questions_were_attempted(array $questionids): array {
        global $DB;

        [$questionidcondition, $params] = $DB->get_in_or_equal($questionids);
        // The MIN(qu.id) is just to ensure that the rows have a unique key.
        $places = $DB->get_records_sql("
                SELECT MIN(qu.id) AS somethingunique, qu.component, qu.contextid, " .
                       \context_helper::get_preload_record_columns_sql('ctx') . "
                  FROM {question_usages} qu
                  JOIN {question_attempts} qa ON qa.questionusageid = qu.id
                  JOIN {context} ctx ON ctx.id = qu.contextid
                 WHERE qa.questionid $questionidcondition
              GROUP BY qu.component, qu.contextid, " .
                       implode(', ', array_keys(\context_helper::get_preload_record_columns('ctx'))) . "
              ORDER BY qu.contextid ASC
                ", $params);

        // Strip out the unwanted ids.
        $places = array_values($places);
        foreach ($places as $place) {
            unset($place->somethingunique);
            \context_helper::preload_from_record($place);
        }

        return $places;
    }

    /**
     * Load the question statistics for all the attempts belonging to a particular component in a particular context.
     *
     * @param string $component frankenstyle component name, e.g. 'mod_quiz'.
     * @param \context $context the context to load the statistics for.
     * @return all_calculated_for_qubaid_condition|null question statistics.
     */
    private static function load_statistics_for_place(string $component, \context $context): ?all_calculated_for_qubaid_condition {
        // This check is basically if (component_exists).
        if (empty(core_component::get_component_directory($component))) {
            return null;
        }

        if (!component_callback_exists($component, 'calculate_question_stats')) {
            return null;
        }

        return component_callback($component, 'calculate_question_stats', [$context]);
    }

    /**
     * Extract the value for one question and one type of statistic from a set of statistics.
     *
     * @param all_calculated_for_qubaid_condition $statistics the batch of statistics.
     * @param int $questionid a question id.
     * @param string $item ane of the field names in all_calculated_for_qubaid_condition, e.g. 'facility'.
     * @return float|null the required value.
     */
    private static function extract_item_value(all_calculated_for_qubaid_condition $statistics,
            int $questionid, string $item): ?float {

        // Look in main questions.
        foreach ($statistics->questionstats as $stats) {
            if ($stats->questionid == $questionid && isset($stats->$item)) {
                return $stats->$item;
            }
        }

        // If not found, look in sub questions.
        foreach ($statistics->subquestionstats as $stats) {
            if ($stats->questionid == $questionid && isset($stats->$item)) {
                return $stats->$item;
            }
        }

        return null;
    }

    /**
     * Calculate average for a stats item on a list of questions.
     *
     * @param int[] $questionids list of ids of the questions we are interested in.
     * @param string $item ane of the field names in all_calculated_for_qubaid_condition, e.g. 'facility'.
     * @return array array keys are question ids and the corresponding values are the average values.
     *      Only questions for which there are data are included.
     */
    private static function calculate_average_question_stats_item(array $questionids, string $item): array {
        $places = self::get_all_places_where_questions_were_attempted($questionids);

        $counts = [];
        $sums = [];

        foreach ($places as $place) {
            $statistics = self::load_statistics_for_place($place->component,
                    \context::instance_by_id($place->contextid));
            if ($statistics === null) {
                continue;
            }

            foreach ($questionids as $questionid) {
                $value = self::extract_item_value($statistics, $questionid, $item);
                if ($value === null) {
                    continue;
                }

                $counts[$questionid] = ($counts[$questionid] ?? 0) + 1;
                $sums[$questionid] = ($sums[$questionid] ?? 0) + $value;
            }
        }

        // Return null if there is no quizzes.
        $averages = [];
        foreach ($sums as $questionid => $sum) {
            $averages[$questionid] = $sum / $counts[$questionid];
        }
        return $averages;
    }

    /**
     * Calculate average facility index
     *
     * @param int $questionid
     * @return float|null
     */
    public static function calculate_average_question_facility(int $questionid): ?float {
        $averages = self::calculate_average_question_stats_item([$questionid], 'facility');
        return $averages[$questionid] ?? null;
    }

    /**
     * Calculate average discriminative efficiency
     *
     * @param int $questionid question id
     * @return float|null
     */
    public static function calculate_average_question_discriminative_efficiency(int $questionid): ?float {
        $averages = self::calculate_average_question_stats_item([$questionid], 'discriminativeefficiency');
        return $averages[$questionid] ?? null;
    }

    /**
     * Calculate average discriminative efficiency
     *
     * @param int $questionid question id
     * @return float|null
     */
    public static function calculate_average_question_discrimination_index(int $questionid): ?float {
        $averages = self::calculate_average_question_stats_item([$questionid], 'discriminationindex');
        return $averages[$questionid] ?? null;
    }

    /**
     * Format a number to a localised percentage with specified decimal points.
     *
     * @param float|null $number The number being formatted
     * @param bool $fraction An indicator for whether the number is a fraction or is already multiplied by 100
     * @param int $decimals Sets the number of decimal points
     * @return string
     * @throws \coding_exception
     */
    public static function format_percentage(?float $number, bool $fraction = true, int $decimals = 2): string {
        if (is_null($number)) {
            return get_string('na', 'qbank_statistics');
        }
        $coefficient = $fraction ? 100 : 1;
        return get_string('percents', 'moodle', format_float($number * $coefficient, $decimals));
    }

    /**
     * Format discrimination index (Needs checking?).
     *
     * @param float|null $value stats value
     * @return array
     */
    public static function format_discrimination_index(?float $value): array {
        if (is_null($value)) {
            $content = get_string('emptyvalue', 'qbank_statistics');
            $classes = '';
        } else if ($value < self::NEED_FOR_REVISION_LOWER_THRESHOLD) {
            $content = get_string('verylikely', 'qbank_statistics');
            $classes = 'alert-danger';
        } else if ($value < self::NEED_FOR_REVISION_UPPER_THRESHOLD) {
            $content = get_string('likely', 'qbank_statistics');
            $classes = 'alert-warning';
        } else {
            $content = get_string('unlikely', 'qbank_statistics');
            $classes = 'alert-success';
        }
        return [$content, $classes];
    }
}
