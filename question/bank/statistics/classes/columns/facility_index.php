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

namespace qbank_statistics\columns;

use core_question\local\bank\column_base;
use qbank_statistics\helper;
/**
 * This column show the average facility index for this question.
 *
 * @package    qbank_statistics
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class facility_index extends column_base {

    /**
     * Title for this column.
     *
     * @return string column title
     */
    public function get_title(): string {
        return get_string('facility_index', 'qbank_statistics');
    }

    public function help_icon(): ?\help_icon {
        return new \help_icon('facility_index', 'qbank_statistics');
    }

    /**
     * Column name.
     *
     * @return string column name
     */
    public function get_name(): string {
        return 'facility_index';
    }

    /**
     * Output the contents of this column.
     * @param object $question the row from the $question table, augmented with extra information.
     * @param string $rowclasses CSS class names that should be applied to this row of output.
     */
    protected function display_content($question, $rowclasses) {
        global $PAGE;
        // Average facility index per quiz.
        $facility = helper::calculate_average_question_facility($question->id);
        echo $PAGE->get_renderer('qbank_statistics')->render_facility_index($facility);
    }

    public function get_extra_classes(): array {
        return ['pr-3'];
    }

}
