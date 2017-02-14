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
 * Javascript to load and render the list of calendar events grouping by course.
 *
 * @module     block_myoverview/events_by_course_list
 * @package    block_myoverview
 * @copyright  2016 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'block_myoverview/event_list'], function($, EventList) {

    var SELECTORS = {
        EVENTS_BY_COURSE_CONTAINER: '[data-region="course-events-container"]',
        EVENTS_LIST_CONTAINER: '[data-region="event-list-container"]'
    };

    /**
     * Loop through course events containers and load calendar events for that course.
     *
     * @method load
     * @param {Object} root The root element of sort by course list.
     */
    var load = function(root) {

        root.find(SELECTORS.EVENTS_BY_COURSE_CONTAINER).each(function(index, container) {
            container = $(container);
            var eventListContainer = container.find(SELECTORS.EVENTS_LIST_CONTAINER);
            EventList.load(eventListContainer);
        });
    };

    return {
        init: function(root) {
            root = $(root);
            load(root);
        }
    };
});
