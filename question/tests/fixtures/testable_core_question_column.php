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
 * Helper class to to test column_base class.
 *
 * @package core_question
 * @copyright 2018 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class to to test column_base class.
 *
 * @package core_question
 * @copyright 2018 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_core_question_column extends \core_question\bank\column_base {

    /** @var array sortable columns. */
    private $sortable = [];

    /**
     * Output the column header cell.
     */
    public function is_sortable() {
        return $this->sortable;
    }

    /**
     * Set the sortable columns for testing.
     *
     * @param array $sortable
     */
    public function set_sortable(array $sortable) {
        $this->sortable = $sortable;
    }

    protected function display_content($question, $rowclasses) {
        echo 'Test Column';
    }

    public function get_name() {
        return 'test_column';
    }

    protected function get_title() {
        return 'Test Column';
    }
}
