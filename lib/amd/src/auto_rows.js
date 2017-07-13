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
 * Enhance a textarea with auto growing rows to fit the content.
 *
 * @module     core/auto_rows
 * @class      auto_rows
 * @package    core
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery'], function($) {
    var SELECTORS = {
        ELEMENT: '[data-auto-rows]'
    };

    var EVENTS = {
        ROW_CHANGE: 'autorows:rowchange',
    };

    /**
     * Determine how many rows should be set for the given element.
     *
     * @method calculateRows
     * @param {jQuery} element The textarea element
     * @return {int} The number of rows for the element
     * @private
     */
    var calculateRows = function(element) {
        var currentRows = element.attr('rows');
        var maxRows = element.attr('data-max-rows');

        var height = element.height();
        var innerHeight = element.innerHeight();
        var padding = innerHeight - height;

        // Set height to 1ox to force scroll height to calculate correctly.
        element.height('1px');

        var scrollHeight = element[0].scrollHeight;
        var rows = (scrollHeight - padding) / (height / currentRows);

        // Remove the height styling to let the height be calculated automatically
        // based on the row attribute.
        element.css('height', '');

        if (maxRows && rows >= maxRows) {
            return maxRows;
        } else {
            return rows;
        }
    };

    /**
     * Add the event listeners for all text areas within the given element.
     *
     * @method init
     * @param {jQuery|selector} root The container element of all enhanced text areas
     * @public
     */
    var init = function(root) {
        $(root).on('input propertychange', SELECTORS.ELEMENT, function(e) {
            var element = $(e.target);
            var currentRows = element.attr('rows');
            var rows = calculateRows(element);

            if (rows != currentRows) {
                element.attr('rows', rows);
                $(root).trigger(EVENTS.ROW_CHANGE);
            }
        });
    };

    return /** @module core/auto_rows */ {
        init: init,
        events: EVENTS,
    };
});
