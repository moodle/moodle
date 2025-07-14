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
 * A javascript module that handles the change of the user's visibility in the
 * online users block.
 *
 * @module     block_online_users/change_user_visibility
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import Notification from 'core/notification';
import {setUserPreference} from 'core_user/repository';

/**
 * Selectors.
 *
 * @access private
 * @type {Object}
 */
const SELECTORS = {
    CHANGE_VISIBILITY_LINK: '#change-user-visibility',
    CHANGE_VISIBILITY_ICON: '#change-user-visibility .icon',
};

/**
 * Change user visibility in the online users block.
 *
 * @method changeVisibility
 * @param {String} action
 * @param {String} userid
 * @returns {Promise}
 * @private
 */
const changeVisibility = (action, userid) => setUserPreference(
    'block_online_users_uservisibility',
    action == "show" ? 1 : 0,
    userid,
)
.then((data) => {
    if (data.saved) {
        const newAction = oppositeAction(action);
        changeVisibilityLinkAttr(newAction);
        changeVisibilityIconAttr(newAction);
    }
    return data;
}).catch(Notification.exception);

/**
 * Get the opposite action.
 *
 * @method oppositeAction
 * @param {String} action
 * @return {String}
 * @private
 */
const oppositeAction = (action) => action == 'show' ? 'hide' : 'show';

/**
 * Change the attribute values of the user visibility link in the online users block.
 *
 * @method changeVisibilityLinkAttr
 * @param {String} action
 * @returns {Promise}
 * @private
 */
const changeVisibilityLinkAttr = (action) => getTitle(action)
    .then((title) => {
        const link = document.querySelector(SELECTORS.CHANGE_VISIBILITY_LINK);
        link.dataset.action = action;
        link.title = title;
        return link;
    });

/**
 * Change the attribute values of the user visibility icon in the online users block.
 *
 * @method changeVisibilityIconAttr
 * @param {String} action
 * @returns {Promise}
 * @private
 */
const changeVisibilityIconAttr = (action) => getTitle(action)
    .then((title) => {
        const icon = document.querySelector(SELECTORS.CHANGE_VISIBILITY_ICON);

        // Add the proper title to the icon.
        icon.setAttribute('title', title);
        icon.setAttribute('aria-label', title);

        if (icon.closest("img")) {
            // If the icon is an image.
            icon.setAttribute('src', M.util.image_url(`t/${action}`));
            icon.setAttribute('alt', title);
        } else {
            // Add the new icon class and remove the old one.
            icon.classList.add(getIconClass(action));
            icon.classList.remove(getIconClass(oppositeAction(action)));
        }
        return title;
    });

/**
 * Get the proper class for the user visibility icon in the online users block.
 *
 * @method getIconClass
 * @param {String} action
 * @return {String}
 * @private
 */
const getIconClass = (action) => (action == 'show') ? 'fa-eye-slash' : 'fa-eye';

/**
 * Get the title description of the user visibility link in the online users block.
 *
 * @method getTitle
 * @param {String} action
 * @return {object} jQuery promise
 * @private
 */
const getTitle = (action) => getString(`online_status:${action}`, 'block_online_users');

/**
 * Initialise change user visibility function.
 *
 * @method init
 */
export const init = () => {
    document.addEventListener('click', (e) => {
        const link = e.target.closest(SELECTORS.CHANGE_VISIBILITY_LINK);
        if (!link) {
            return;
        }
        e.preventDefault();
        changeVisibility(
            link.dataset.action,
            link.dataset.userid,
        );
    });
};
