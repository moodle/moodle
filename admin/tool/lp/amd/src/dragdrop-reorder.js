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
 * Drag and drop reorder via HTML5.
 *
 * @module     tool_lp/dragdrop-reorder
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/str', 'core/yui'], function(str, Y) {
    // Private variables and functions.

    /**
     * Store the current instance of the core drag drop.
     *
     * @property {object} dragDropInstance M.tool_lp.dragdrop_reorder
     */
    var dragDropInstance = null;

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

    return /** @alias module:tool_lp/dragdrop-reorder */ {
        // Public variables and functions.
        /**
         * Create an instance of M.tool_lp.dragdrop
         *
         * @param {String} group Unique string to identify this interaction.
         * @param {String} dragHandleText Alt text for the drag handle.
         * @param {String} sameNodeText Used in keyboard drag drop for the list of items target.
         * @param {String} parentNodeText Used in keyboard drag drop for the parent target.
         * @param {String} sameNodeClass class used to find the each of the list of items.
         * @param {String} parentNodeClass class used to find the container for the list of items.
         * @param {String} dragHandleInsertClass class used to find the location to insert the drag handles.
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
                {key: 'emptydragdropregion', component: 'moodle'},
                {key: 'movecontent', component: 'moodle'},
                {key: 'tocontent', component: 'moodle'},
            ]).done(function() {
                Y.use('moodle-tool_lp-dragdrop-reorder', function() {

                    var context = {
                        callback: callback
                    };
                    if (dragDropInstance) {
                        dragDropInstance.destroy();
                    }
                    dragDropInstance = M.tool_lp.dragdrop_reorder({
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
