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
 * Base class for 'columns' that are actually displayed as a row following the main question row.
 *
 * @package   core_question
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

/**
 * Base class for 'columns' that are actually displayed as a row following the main question row.
 *
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class row_base extends column_base {

    /**
     * Check if the column is an extra row of not.
     */
    public function is_extra_row(): bool {
        return true;
    }

    /**
     * Output the opening column tag.  If it is set as heading, it will use <th> tag instead of <td>
     *
     * @param \stdClass $question
     * @param string $rowclasses
     */
    protected function display_start($question, $rowclasses): void {
        if ($rowclasses) {
            echo \html_writer::start_tag('tr', ['class' => $rowclasses]);
        } else {
            echo \html_writer::start_tag('tr');
        }
        echo \html_writer::start_tag('td',
                ['colspan' => $this->qbank->get_column_count(), 'class' => $this->get_name()]);
    }

    /**
     * Output the closing column tag
     *
     * @param object $question
     * @param string $rowclasses
     */
    protected function display_end($question, $rowclasses): void {
        echo \html_writer::end_tag('td');
        echo \html_writer::end_tag('tr');
    }

}
