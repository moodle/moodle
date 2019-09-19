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
 * Controls the drawer.
 *
 * @module     core/drawer
 * @copyright  2019 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import * as PubSub from 'core/pubsub';
import DrawerEvents from 'core/drawer_events';

/**
 * Show the drawer.
 *
 * @param {Object} root The drawer container.
 */
const show = (root) => {
    root.removeClass('hidden');
    root.attr('aria-expanded', true);
    root.attr('aria-hidden', false);
    PubSub.publish(DrawerEvents.DRAWER_SHOWN, root);
};

/**
 * Hide the drawer.
 *
 * @param {Object} root The drawer container.
 */
const hide = (root) => {
    root.addClass('hidden');
    root.attr('aria-expanded', false);
    root.attr('aria-hidden', true);
    PubSub.publish(DrawerEvents.DRAWER_HIDDEN, root);
};

/**
 * Check if the drawer is visible.
 *
 * @param {Object} root The drawer container.
 * @return {boolean}
 */
const isVisible = (root) => {
    let isHidden = root.hasClass('hidden');
    return !isHidden;
};

/**
 * Find the root element of the drawer based on the using the drawer content root's ID.
 *
 * @param {Object} contentRoot The drawer content's root element.
 * @returns {*|jQuery}
 */
const getDrawerRoot = (contentRoot) => {
    contentRoot = $(contentRoot);
    return contentRoot.closest('[data-region="right-hand-drawer"]');
};

export default {
    hide: hide,
    show: show,
    isVisible: isVisible,
    getDrawerRoot: getDrawerRoot
};
