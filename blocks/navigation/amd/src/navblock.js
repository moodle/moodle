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
 * Load the navigation tree javascript.
 *
 * @module     block_navigation/navblock
 * @package    core
 * @copyright  2015 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/tree'], function($, Tree) {
    return {
        init: function(instanceid) {
            var navTree = new Tree(".block_navigation .block_tree");
            navTree.finishExpandingGroup = function(item) {
                Tree.prototype.finishExpandingGroup.call(this, item);
                Y.use('moodle-core-event', function() {
                    Y.Global.fire(M.core.globalEvents.BLOCK_CONTENT_UPDATED, {
                        instanceid: instanceid
                    });
                });
            };
            navTree.collapseGroup = function(item) {
                Tree.prototype.collapseGroup.call(this, item);
                Y.use('moodle-core-event', function() {
                    Y.Global.fire(M.core.globalEvents.BLOCK_CONTENT_UPDATED, {
                        instanceid: instanceid
                    });
                });
            };
        }
    };
});
