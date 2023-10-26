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
 * Selectors for the search widget.
 *
 * @module     core_grades/searchwidget/selectors
 * @copyright  2022 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default {
    regions: {
        searchResults: '[data-region="search-results-container-widget"]',
        unsearchableContent: '[data-region="unsearchable-content-container-widget"]',
    },
    actions: {
        search: '[data-action="search"]',
        clearSearch: '[data-action="clearsearch"]',
    },
    elements: {
        getSearchWidgetSelector: searchtype => `.search-widget[data-searchtype="${searchtype}"]`,
        getSearchWidgetDropdownSelector: searchtype => `.search-widget[data-searchtype="${searchtype}"] .dropdown-menu`,
    },
};
