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
 * Move competency frameworks via ajax.
 *
 * @module     tool_lp/frameworkmove
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/str',
        'core/notification',
        'core/ajax',
        'tool_lp/dragdrop-reorder'],
       function($, str, notification, ajax, dragdrop) {
    // Private variables and functions.

    /**
     * Handle a drop on a node.
     *
     * @method handleDrop
     * @param {DOMNode} drag
     * @param {DOMNode} drop
     */
    var handleDrop = function(drag, drop) {
        var from = $(drag).data('frameworkid');
        var to = $(drop).data('frameworkid');

        var requests = ajax.call([{
            methodname: 'tool_lp_reorder_competency_framework',
            args: { from: from, to: to }
        }]);
        requests[0].fail(notification.exception);

    };

    return /** @alias module:tool_lp/frameworkmove */ {
        // Public variables and functions.
        /**
         * Initialise this plugin. It loads some strings, then adds the drag/drop functions.
         * @method init
         */
        init: function() {
            // Init this module.
            str.get_string('movecompetencyframework', 'tool_lp').done(
                function(movestring) {
                    dragdrop.dragdrop('movecompetencyframework',
                                      movestring,
                                      { identifier: 'movecompetencyframework', component: 'tool_lp'},
                                      { identifier: 'movecompetencyframeworkafter', component: 'tool_lp'},
                                      'drag-samenode',
                                      'drag-parentnode',
                                      'drag-handlecontainer',
                                      handleDrop);
                }
            ).fail(notification.exception);
        }

    };
});
