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
 * This module is responsible for the calendar filter.
 *
 * @module     core_calendar/calendar_filter
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core_calendar/selectors',
    'core_calendar/events',
    'core/str',
    'core/templates',
],
function(
    $,
    CalendarSelectors,
    CalendarEvents,
    Str,
    Templates
) {

    var registerEventListeners = function(root) {
        root.on('click', CalendarSelectors.eventFilterItem, function(e) {
            var target = $(e.currentTarget);

            toggleFilter(target);

            e.preventDefault();
        });

        $('body').on(CalendarEvents.viewUpdated, function() {
            var filters = root.find(CalendarSelectors.eventFilterItem);

            filters.each(function(i, filter) {
                filter = $(filter);
                if (filter.data('eventtype-hidden')) {
                    var data = getFilterData(filter);
                    fireFilterChangedEvent(data);
                }
            });
        });
    };

    var toggleFilter = function(target) {
        var data = getFilterData(target);

        // Toggle the hidden. We need to render the template before we change the value.
        data.hidden = !data.hidden;

        return Str.get_string('eventtype' + data.eventtype, 'calendar')
        .then(function(nameStr) {
            data.name = nameStr;

            return data;
        })
        .then(function(context) {
            return Templates.render('core_calendar/event_filter_key', context);
        })
        .then(function(html, js) {
            return Templates.replaceNode(target, html, js);
        })
        .then(function() {
            fireFilterChangedEvent(data);
            return;
        });
    };

    /**
     * Fire the filterChanged event for the specified data.
     *
     * @param   {object} data The data to include
     */
    var fireFilterChangedEvent = function(data) {
        M.util.js_pending("month-mini-filterChanged");
        $('body').trigger(CalendarEvents.filterChanged, {
            type: data.eventtype,
            hidden: data.hidden,
        });
        M.util.js_complete("month-mini-filterChanged");
    };

    /**
     * Get the filter data for the specified target.
     *
     * @param   {jQuery} target The target node
     * @return  {Object}
     */
    var getFilterData = function(target) {
        return {
            eventtype: target.data('eventtype'),
            hidden: target.data('eventtype-hidden'),
        };
    };

    return {
        init: function(root) {
            root = $(root);

            registerEventListeners(root);
        }
    };
});
