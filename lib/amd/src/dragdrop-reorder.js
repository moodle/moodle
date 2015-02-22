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
 * Wrapper for the YUI M.core.dragdrop class. Allows us to
 * use the YUI version in AMD code until it is replaced.
 *
 * @module     core/dragdrop-reorder
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/str', 'core/yui'], function(str, Y) {

    // Private variables and functions.

    /**
     * Translate the drophit event from YUI
     * into simple drag and drop nodes.
     * @param {Y.Event} e The yui drop event.
     */
    var proxyCallback = function(e) {
        var dragNode = e.drag.get('node');
        var dropNode = e.drop.get('node');
        this.callback(dragNode.getDOMNode(), dropNode.getDOMNode());
    };

    return /** @alias module:core/dragdrop-reorder */ {
        // Public variables and functions.
        /**
         * Create an instance of M.core.dragdrop
         *
         * @param {string} group Unique string to identify this interaction.
         * @param {string} dragHandleText Alt text for the drag handle.
         * @param {string} sameNodeText Used in keyboard drag drop for the list of items target.
         * @param {string} parentNodeText Used in keyboard drag drop for the parent target.
         * @param {string} sameNodeClass class used to find the each of the list of items.
         * @param {string} parentNodeClass class used to find the container for the list of items.
         * @param {string} dragHandleInsertClass class used to find the location to insert the drag handles.
         * @param {function} callback Drop hit handler.
         */
        dragdrop: function(group,
                           dragHandleText,
                           sameNodeText,
                           parentNodeText,
                           sameNodeClass,
                           parentNodeClass,
                           dragHandleInsertClass,
                           callback) {
            // Here we are wrapping YUI. This allows us to start transitioning, but
            // wait for a good alternative without having inconsistent UIs.
            str.get_strings([
                { key: 'emptydragdropregion', component: 'moodle' },
                { key: 'movecontent', component: 'moodle' },
                { key: 'tocontent', component: 'moodle' },
            ]).done( function () {
                Y.use('moodle-core-dragdrop-reorder', function () {

                    var context = {
                        callback: callback
                    };
                    M.core.dragdrop_reorder({
                        group: group,
                        dragHandleText: dragHandleText,
                        sameNodeText: sameNodeText,
                        parentNodeText: parentNodeText,
                        sameNodeClass: sameNodeClass,
                        parentNodeClass: parentNodeClass,
                        dragHandleInsertClass: dragHandleInsertClass,
                        callback: Y.bind(proxyCallback, context)
                    });
                });
            });
        }

    };
});
