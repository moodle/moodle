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
 * Init private files treeview
 *
 * @package    block_private_files
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.block_private_files = {};

M.block_private_files.init_tree = function(Y, expand_all, htmlid) {
    Y.use('yui2-treeview', 'node-event-simulate', function(Y) {
        var tree = new Y.YUI2.widget.TreeView(htmlid);

        tree.subscribe("clickEvent", function(node, event) {
            // we want normal clicking which redirects to url
            return false;
        });

        tree.subscribe("enterKeyPressed", function(node) {
            // We want keyboard activation to trigger a click on the first link.
            Y.one(node.getContentEl()).one('a').simulate('click');
            return false;
        });

        if (expand_all) {
            tree.expandAll();
        }

        tree.render();
    });
};
