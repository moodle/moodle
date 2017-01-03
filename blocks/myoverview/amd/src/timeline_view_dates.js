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
 * Enhance the timeline view dates templates to load the calendar events.
 *
 * @module     block_myoverview/timeline_view_dates
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/custom_interaction_events', 'block_myoverview/event_list'], function(CustomEvents, EventList) {

    var SELECTORS = {
        VIEW_MORE_BUTTON: '[data-action="view-more"]',
        EVENT_LIST_CONTAINER: '[data-region="event-list-container"]',
    };

    /**
     * Trigger the event list loading for the given containers.
     *
     * @private
     * @method loadFromContainers
     * @param {array} containers A list of container elements
     */
    var loadForContainers = function(containers) {
        containers.each(function() {
            EventList.load(this);
        });
    };

    return {
        /**
         * Initialise the javascript on the root element.
         *
         * @method init
         * @param {object} jQuery element
         */
        init: function(root) {
            var containers = root.find(SELECTORS.EVENT_LIST_CONTAINER);

            loadForContainers(containers);

            CustomEvents.define(root, [CustomEvents.events.activate]);
            root.on(CustomEvents.events.activate, SELECTORS.VIEW_MORE_BUTTON, function() {
                loadForContainers(containers);
            });
        }
    };
});
