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
 * Javascript helper function for Folder module
 *
 * @package    mod
 * @subpackage folder
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_folder = {};

M.mod_folder.init_tree = function(Y, id, expand_all) {
    Y.use('yui2-treeview', 'node-event-simulate', function(Y) {
        var tree = new Y.YUI2.widget.TreeView(id);

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
        } else {
            // Else just expand the top node.
            tree.getRoot().children[0].expand();
        }

        tree.render();
    });
}
