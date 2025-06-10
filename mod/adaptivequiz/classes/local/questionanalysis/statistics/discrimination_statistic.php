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
 * This interface defines the methods required for pluggable statistics that may be added to the question analysis.
 *
 * @copyright  2013 Middlebury College {@link http://www.middlebury.edu/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\questionanalysis\statistics;

use mod_adaptivequiz\local\questionanalysis\question_analyser;

class discrimination_statistic implements question_statistic {
    /**
     * Answer a display-name for this statistic.
     *
     * @return string
     */
    public function get_display_name () {
        return get_string('discrimination_display_name', 'adaptivequiz');
    }

    /**
     * Calculate this statistic for a question's results
     *
     * @param question_analyser $analyser
     * @return question_statistic_result
     */
    public function calculate (question_analyser $analyser) {
        // Discrimination is generally defined as comparing the results of two sub-groups,
        // the top 27% of test-takers (the upper group) and the bottom 27% of test-takers (the lower group),
        // assuming a normal distribution of scores).
        //
        // Given that likely have a very sparse data-set we will instead categorize our
        // responses into the upper group if the respondent's overall ability measure minus the measure's standard error
        // is greater than the question's level. Likewise, responses will be categorized into the lower group if the respondent's
        // ability measure plus the measure's standard error is less than the question's level.
        // Responses where the user's ability measure and error-range include the question level will be ignored.

        $level = $analyser->get_question_level_in_logits();
        $uppergroupsize = 0;
        $uppergroupcorrect = 0;
        $lowergroupsize = 0;
        $lowergroupcorrect = 0;

        foreach ($analyser->get_results() as $result) {
            if ($result->score->measured_ability_in_logits() - $result->score->standard_error_in_logits() > $level) {
                // Upper group.
                $uppergroupsize++;
                if ($result->correct) {
                    $uppergroupcorrect++;
                }
            } else if ($result->score->measured_ability_in_logits() + $result->score->standard_error_in_logits() < $level) {
                // Lower Group.
                $lowergroupsize++;
                if ($result->correct) {
                    $lowergroupcorrect++;
                }
            }
        }

        if ($uppergroupsize > 0 && $lowergroupsize > 0) {
            // We need at least one result in the upper and lower groups.
            $upperproportion = $uppergroupcorrect / $uppergroupsize;
            $lowerproportion = $lowergroupcorrect / $lowergroupsize;
            $discrimination = $upperproportion - $lowerproportion;
            return new discrimination_statistic_result($discrimination);
        } else {
            // If we don't have any responses in the upper or lower group, then we don't have a meaningful result.
            return new discrimination_statistic_result(null);
        }
    }
}
