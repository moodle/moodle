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
 * The purpose of this module is to centralize selectors related to question.
 *
 * @deprecated since Moodle 4.0
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 * @module     core_question/selectors
 * @copyright  2018 Simey Lameze <lameze@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    window.console.warn('warn: The core_question/selectors has been deprecated. Please use qbank_tagquestion/selectors instead.');
    return {
        actions: {
            save: '[data-action="save"]',
            edittags: '[data-action="edittags"]',
        },
        containers: {
            loadingIcon: '[data-region="overlay-icon-container"]',
        },
    };
});
