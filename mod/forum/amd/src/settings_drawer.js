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
 * Controls the forum/discussion settings drawer.
 *
 * @module     mod_forum/settings_drawer
 * @copyright  2019 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import * as PubSub from 'core/pubsub';
import Drawer from 'core/drawer';
import Events from 'mod_forum/forum_events';

const registerEventListeners = (root) => {
    PubSub.subscribe(Events.TOGGLE_SETTINGS_DRAWER, function() {
        const drawerRoot = Drawer.getDrawerRoot(root);
        if (Drawer.isVisible(drawerRoot)) {
            Drawer.hide(drawerRoot);
        } else {
            Drawer.show(drawerRoot);
        }
    });
};

/**
 * Initialise the settings drawer.
 *
 * @param {Object} root The settings drawer container.
 */
export const init = (root) => {
    root = $(root);
    registerEventListeners(root);
};
