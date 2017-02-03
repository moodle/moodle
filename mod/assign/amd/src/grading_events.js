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
 * Events for the grading interface.
 *
 * @module     mod_assign/grading_events
 * @package    mod_assign
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(function() {
    return {
        COLLAPSE_REVIEW_PANEL: 'grading:collapse-review-panel',
        EXPAND_REVIEW_PANEL: 'grading:expand-review-panel',
        COLLAPSE_GRADE_PANEL: 'grading:collapse-grade-panel',
        EXPAND_GRADE_PANEL: 'grading:expand-grade-panel',
    };
});
