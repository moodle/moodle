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
 * Manage the timeline view navigation for the timeline block.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
[
    'jquery',
    'core/custom_interaction_events',
    'block_timeline/view',
    'core/ajax',
    'core/notification'
],
function(
    $,
    CustomEvents,
    View,
    Ajax,
    Notification
) {

    var SELECTORS = {
        TIMELINE_DAY_FILTER: '[data-region="day-filter"]',
        TIMELINE_DAY_FILTER_OPTION: '[data-from]',
        TIMELINE_VIEW_SELECTOR: '[data-region="view-selector"]',
        DATA_DAYS_OFFSET: '[data-days-offset]',
        DATA_DAYS_LIMIT: '[data-days-limit]',
    };

    /**
     * Generic handler to persist user preferences
     *
     * @param {string} type The name of the attribute you're updating
     * @param {string} value The value of the attribute you're updating
     */
    var updateUserPreferences = function(type, value) {
        var request = {
            methodname: 'core_user_update_user_preferences',
            args: {
                preferences: [
                    {
                        type: type,
                        value: value
                    }
                ]
            }
        };

        Ajax.call([request])[0]
            .fail(Notification.exception);
    };

    /**
     * Event listener for the day selector ("Next 7 days", "Next 30 days", etc).
     *
     * @param {object} root The root element for the timeline block
     * @param {object} timelineViewRoot The root element for the timeline view
     */
    var registerTimelineDaySelector = function(root, timelineViewRoot) {
        var timelineDaySelectorContainer = root.find(SELECTORS.TIMELINE_DAY_FILTER);

        CustomEvents.define(timelineDaySelectorContainer, [CustomEvents.events.activate]);
        timelineDaySelectorContainer.on(
            CustomEvents.events.activate,
            SELECTORS.TIMELINE_DAY_FILTER_OPTION,
            function(e, data) {
                // Update the user preference
                var filtername = $(e.currentTarget).data('filtername');
                var type = 'block_timeline_user_filter_preference';
                updateUserPreferences(type, filtername);

                var option = $(e.target).closest(SELECTORS.TIMELINE_DAY_FILTER_OPTION);

                if (option.attr('aria-current') == 'true') {
                    // If it's already active then we don't need to do anything.
                    return;
                }

                var daysOffset = option.attr('data-from');
                var daysLimit = option.attr('data-to');
                var elementsWithDaysOffset = root.find(SELECTORS.DATA_DAYS_OFFSET);

                elementsWithDaysOffset.attr('data-days-offset', daysOffset);

                if (daysLimit != undefined) {
                    elementsWithDaysOffset.attr('data-days-limit', daysLimit);
                } else {
                    elementsWithDaysOffset.removeAttr('data-days-limit');
                }

                // Reset the views to reinitialise the event lists now that we've
                // updated the day limits.
                View.reset(timelineViewRoot);

                data.originalEvent.preventDefault();
            }
        );
    };

    /**
     * Event listener for the "sort" button in the timeline navigation that allows for
     * changing between the timeline dates and courses views.
     *
     * On a view change we tell the timeline view module that the view has been shown
     * so that it can handle how to display the appropriate view.
     *
     * @param {object} root The root element for the timeline block
     * @param {object} timelineViewRoot The root element for the timeline view
     */
    var registerViewSelector = function(root, timelineViewRoot) {
        var viewSelector = root.find(SELECTORS.TIMELINE_VIEW_SELECTOR);

        // Listen for when the user changes tab so that we can show the first set of courses
        // and load their events when they request the sort by courses view for the first time.
        viewSelector.on('shown shown.bs.tab', function(e) {
            View.shown(timelineViewRoot);
            $(e.target).removeClass('active');
        });


        // Event selector for user_sort
        CustomEvents.define(viewSelector, [CustomEvents.events.activate]);
        viewSelector.on(CustomEvents.events.activate, "[data-toggle='tab']", function(e) {
            var filtername = $(e.currentTarget).data('filtername');
            var type = 'block_timeline_user_sort_preference';
            updateUserPreferences(type, filtername);
        });
    };

    /**
     * Initialise the timeline view navigation by adding event listeners to
     * the navigation elements.
     *
     * @param {object} root The root element for the timeline block
     * @param {object} timelineViewRoot The root element for the timeline view
     */
    var init = function(root, timelineViewRoot) {
        root = $(root);
        registerTimelineDaySelector(root, timelineViewRoot);
        registerViewSelector(root, timelineViewRoot);
    };

    return {
        init: init
    };
});
