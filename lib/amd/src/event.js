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
 * Global registry of core events that can be triggered/listened for.
 *
 * @module     core/event
 * @package    core
 * @class      event
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.0
 */
define([ 'jquery', 'core/yui' ],
       function($, Y) {

    return /** @alias module:core/event */ {
        // Public variables and functions.
        /**
         * Trigger an event using both JQuery and YUI
         *
         * @method notifyFilterContentUpdated
         * @param {string}|{JQuery} nodes - Selector or list of elements that were inserted.
         */
        notifyFilterContentUpdated: function(nodes) {
            nodes = $(nodes);
            Y.use('event', 'moodle-core-event', function(Y) {
                // Trigger it the JQuery way.
                $('document').trigger(M.core.event.FILTER_CONTENT_UPDATED, nodes);

                // Create a YUI NodeList from our JQuery Object.
                var yuiNodes = new Y.NodeList(nodes.get());

                // And again for YUI.
                Y.fire(M.core.event.FILTER_CONTENT_UPDATED, { nodes: yuiNodes });
            });
        },

    };
});
