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
    'block_timeline/event_list'
],
function(
    $,
    Str,
    EventList
) {

    var SELECTORS = {
        EVENT_LIST_CONTAINER: '[data-region="event-list-container"]',
    };

    /**
     * Initialise the event list and being loading the events.
     *
     * @param {object} root The root element for the timeline dates view.
     */
    var load = function(root) {
        var eventListContainer = root.find(SELECTORS.EVENT_LIST_CONTAINER);
        Str.get_string('ariaeventlistpaginationnavdates', 'block_timeline')
            .then(function(string) {
                EventList.init(eventListContainer, [5, 10, 25], {}, string);
                return string;
            })
            .catch(function() {
                // Ignore if we can't load the string. Still init the event list.
                EventList.init(eventListContainer, [5, 10, 25]);
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
            root.attr('data-seen', true);
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
            root.attr('data-seen', true);
        }
    };

    /**
     * Load the events if this is the first time the view is displayed.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var shown = function(root) {
        if (!root.attr('data-seen')) {
            load(root);
            root.attr('data-seen', true);
        }
    };

    return {
        init: init,
        reset: reset,
        shown: shown
    };
});
