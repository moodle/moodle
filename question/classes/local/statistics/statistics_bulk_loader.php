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

namespace core_question\local\statistics;

use core_question\local\bank\column_base;
use core_question\statistics\questions\all_calculated_for_qubaid_condition;
use core_component;

/**
 * Helper to efficiently load all the statistics for a set of questions.
 *
 * If you are implementing a question bank column, do not use this method directly.
 * Instead, override the {@see column_base::get_required_statistics_fields()} method
 * in your column class, and the question bank view will take care of it for you.
 *
 * @package   core_question
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statistics_bulk_loader {

    /**
     * Load and aggregate the requested statistics for all the places where the given questions are used.
     *
     * The returned array will contain a values for each questionid and field, which will be null if the value is not available.
     *
     * @param int[] $questionids array of question ids.
     * @param string[] $requiredstatistics array of the fields required, e.g. ['facility', 'discriminationindex'].
     * @return float[][] if a value is not available, it will be set to null.
     */
    public static function load_aggregate_statistics(array $questionids, array $requiredstatistics): array {
        // Prevent unnecessary statistics calculations.
        if (empty($requiredstatistics)) {
            $aggregates = [];
            foreach ($questionids as $questionid) {
                $aggregates[$questionid] = [];
            }
            return $aggregates;
        }

        $places = self::get_all_places_where_questions_were_attempted($questionids);

        // Set up blank two-dimensional arrays to store the running totals. Indexed by questionid and field name.
        $zerovaluesforonequestion = array_combine($requiredstatistics, array_fill(0, count($requiredstatistics), 0));
        $counts = array_combine($questionids, array_fill(0, count($questionids), $zerovaluesforonequestion));
        $sums = array_combine($questionids, array_fill(0, count($questionids), $zerovaluesforonequestion));

        // Load the data for each place, and add to the running totals.
        foreach ($places as $place) {
            $statistics = self::load_statistics_for_place($place->component,
                    \context::instance_by_id($place->contextid));
            if ($statistics === null) {
                continue;
            }

            foreach ($questionids as $questionid) {
                foreach ($requiredstatistics as $item) {
                    $value = self::extract_item_value($statistics, $questionid, $item);
                    if ($value === null) {
                        continue;
                    }

                    $counts[$questionid][$item] += 1;
                    $sums[$questionid][$item] += $value;
                }
            }
        }

        // Compute the averages from the final totals.
        $aggregates = [];
        foreach ($questionids as $questionid) {
            $aggregates[$questionid] = [];
            foreach ($requiredstatistics as $item) {
                if ($counts[$questionid][$item] > 0) {
                    $aggregates[$questionid][$item] = $sums[$questionid][$item] / $counts[$questionid][$item];
                } else {
                    $aggregates[$questionid][$item] = null;
                }

            }
        }

        return $aggregates;
    }

    /**
     * For a list of questions find all the places, defined by (component, contextid), where there are attempts.
     *
     * @param int[] $questionids array of question ids that we are interested in.
     * @return \stdClass[] list of objects with fields ->component and ->contextid.
     */
    protected static function get_all_places_where_questions_were_attempted(array $questionids): array {
        global $DB;

        [$questionidcondition, $params] = $DB->get_in_or_equal($questionids);
        // The MIN(qu.id) is just to ensure that the rows have a unique key.
        $places = $DB->get_records_sql("
                SELECT MIN(qu.id) AS somethingunique, qu.component, qu.contextid
                  FROM {question_usages} qu
                  JOIN {question_attempts} qatt ON qatt.questionusageid = qu.id
                 WHERE qatt.questionid $questionidcondition
              GROUP BY qu.component, qu.contextid
              ORDER BY qu.contextid ASC
                ", $params);

        // Strip out the unwanted ids.
        $places = array_values($places);
        foreach ($places as $place) {
            unset($place->somethingunique);
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
    protected static function load_statistics_for_place(
        string $component,
        \context $context
    ): ?all_calculated_for_qubaid_condition {
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
     * @param string $item one of the field names in all_calculated_for_qubaid_condition, e.g. 'facility'.
     * @return float|null the required value.
     */
    protected static function extract_item_value(all_calculated_for_qubaid_condition $statistics,
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
}
