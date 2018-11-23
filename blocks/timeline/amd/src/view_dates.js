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
 * Manage the timeline dates view for the timeline block.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
[
    'jquery',
    'core/str',
    'block_timeline/event_list',
    'core/pubsub',
    'core/paged_content_events'
],
function(
    $,
    Str,
    EventList,
    PubSub,
    PagedContentEvents
) {

    var SELECTORS = {
        EVENT_LIST_CONTAINER: '[data-region="event-list-container"]',
    };

    var DEFAULT_PAGE_LIMIT = [5, 10, 25];

    /**
     * Generate a paged content array of limits taking into account user preferences
     *
     * @param {object} root The root element for the timeline dates view.
     * @return {array} Array of limit objects
     */
    var getPagingLimits = function(root) {
        var limitPref = parseInt(root.data('limit'), 10);
        var isDefaultSet = false;
        var limits = DEFAULT_PAGE_LIMIT.map(function(value) {
            if (limitPref == value) {
                isDefaultSet = true;
            }

            return {
                value: value,
                active: limitPref == value
            };
        });

        if (!isDefaultSet) {
            limits[0].active = true;
        }

        return limits;
    };

    /**
     * Setup the listeners for the timeline block
     *
     * @param {string} root view dates container
     * @param {string} namespace The namespace for the paged content
     */
    var registerEventListeners = function(root, namespace) {
        var event = namespace + PagedContentEvents.SET_ITEMS_PER_PAGE_LIMIT;
        PubSub.subscribe(event, function(limit) {
            $(root).data('limit', limit);
        });
    };

    /**
     * Initialise the event list and being loading the events.
     *
     * @param {object} root The root element for the timeline dates view.
     */
    var load = function(root) {
        var eventListContainer = root.find(SELECTORS.EVENT_LIST_CONTAINER);
        var namespace = $(eventListContainer).attr('id') + "user_block_timeline" + Math.random();
        registerEventListeners(root, namespace);

        var limits = getPagingLimits(root);
        var config = {
            persistentLimitKey: "block_timeline_user_limit_preference",
            eventNamespace: namespace
        };
        Str.get_string('ariaeventlistpaginationnavdates', 'block_timeline')
            .then(function(string) {
                EventList.init(eventListContainer, limits, {}, string, config);
                return string;
            })
            .catch(function() {
                // Ignore if we can't load the string. Still init the event list.
                EventList.init(eventListContainer, limits, {}, "", config);
            });
    };

    /**
     * Initialise the timeline dates view. Begin loading the events
     * if this view is active.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var init = function(root) {
        root = $(root);
        if (root.hasClass('active')) {
            load(root);
            root.data('seen', true);
        }
    };

    /**
     * Reset the view back to it's initial state. If this view is active then
     * beging loading the events.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var reset = function(root) {
        root.removeAttr('data-seen');
        if (root.hasClass('active')) {
            load(root);
            root.data('seen', true);
        }
    };

    /**
     * Load the events if this is the first time the view is displayed.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var shown = function(root) {
        if (!root.data('seen')) {
            load(root);
            root.data('seen', true);
        }
    };

    return {
        init: init,
        reset: reset,
        shown: shown
    };
});
