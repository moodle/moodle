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
 * Javascript to initialise the selectors for the myoverview block.
 *
 * @copyright  2018 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default {
    courseView: {
        region: '[data-region="courses-view"]',
        regionContent: '[data-region="course-view-content"]'
    },
    FILTERS: '[data-region="filter"]',
    FILTER_OPTION: '[data-filter]',
    DISPLAY_OPTION: '[data-display-option]',
    ACTION_HIDE_COURSE: '[data-action="hide-course"]',
    ACTION_SHOW_COURSE: '[data-action="show-course"]',
    ACTION_ADD_FAVOURITE: '[data-action="add-favourite"]',
    ACTION_REMOVE_FAVOURITE: '[data-action="remove-favourite"]',
    FAVOURITE_ICON: '[data-region="favourite-icon"]',
    ICON_IS_FAVOURITE: '[data-region="is-favourite"]',
    ICON_NOT_FAVOURITE: '[data-region="not-favourite"]',
    region: {
        selectBlock: '[data-region="myoverview"]',
        clearIcon: '[data-action="clearsearch"]',
        searchInput: '[data-action="search"]',
    },
};
