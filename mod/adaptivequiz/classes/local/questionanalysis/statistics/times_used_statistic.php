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

class times_used_statistic implements question_statistic {
    /**
     * Answer a display-name for this statistic.
     *
     * @return string
     */
    public function get_display_name () {
        return get_string('times_used_display_name', 'adaptivequiz');
    }

    /**
     * Calculate this statistic for a question's results
     *
     * @param question_analyser $analyser
     * @return question_statistic_result
     */
    public function calculate (question_analyser $analyser) {
        return new times_used_statistic_result (count($analyser->get_results()));
    }
}
