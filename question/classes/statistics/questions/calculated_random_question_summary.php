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
 * Question statistics calculations class. Used in the quiz statistics report.
 *
 * @package    core_question
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\questions;
defined('MOODLE_INTERNAL') || die();

/**
 * Class calculated_random_question_summary
 *
 * This class is used to indicate the statistics for a random question slot should
 * be rendered with a link to a summary of the displayed questions.
 *
 * It's used in the limited view of the statistics calculation in lieu of adding
 * the stats for each subquestion individually.
 *
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculated_random_question_summary extends calculated {

    /**
     * @var int only set immediately before display in the table. The order of display in the table.
     */
    public $subqdisplayorder;

    /**
     * This is a summary stat so never breakdown by variant.
     *
     * @return bool
     */
    public function break_down_by_variant() {
        return false;
    }
}
