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
 * Load the settings block tree javscript
 *
 * @module     block_settings/settingsblock
 * @copyright  2015 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {notifyBlockContentUpdated} from 'core_block/events';
import Tree from 'core/tree';

export const init = (instanceId, siteAdminNodeId) => {
    const adminTree = new Tree(".block_settings .block_tree");
    const blockNode = document.querySelector(`[data-instance-id="${instanceId}"]`);

    if (siteAdminNodeId) {
        const siteAdminLink = adminTree.treeRoot.get(0).querySelector(`#${siteAdminNodeId} a`);
        const newContainer = document.createElement('span');
        newContainer.setAttribute('tabindex', '0');
        siteAdminLink.childNodes.forEach(node => newContainer.appendChild(node));
        siteAdminLink.replaceWith(newContainer);
    }

    /**
     * The method to call when then the navtree finishes expanding a group.
     *
     * @method finishExpandingGroup
     * @param {Object} item
     * @fires event:blockContentUpdated
     */
    adminTree.finishExpandingGroup = function(item) {
        Tree.prototype.finishExpandingGroup.call(adminTree, item);
        notifyBlockContentUpdated(blockNode);
    };

    /**
     * The method to call whe then the navtree collapses a group
     *
     * @method collapseGroup
     * @param {Object} item
     * @fires event:blockContentUpdated
     */
    adminTree.collapseGroup = function(item) {
        Tree.prototype.collapseGroup.call(adminTree, item);
        notifyBlockContentUpdated(blockNode);
    };
};
