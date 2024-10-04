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
 * This module will tie together all of the different calls the gradable module will make.
 *
 * @module     mod_forum/local/grades/local/grader/user_picker
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Selectors from './user_picker/selectors';
import {getString} from 'core/str';

const templatePath = 'mod_forum/local/grades/local/grader';

/**
 * The Grader User Picker.
 *
 * @class mod_forum/local/grades/local/grader/user_picker
 */
class UserPicker {

    /**
     * Constructor for the User Picker.
     *
     * @constructor mod_forum/local/grades/local/grader/user_picker
     * @param {Array} userList List of users
     * @param {Function} showUserCallback The callback used to display the user
     * @param {Function} preChangeUserCallback The callback to use before changing user
     */
    constructor(userList, showUserCallback, preChangeUserCallback) {
        this.userList = userList;
        this.showUserCallback = showUserCallback;
        this.preChangeUserCallback = preChangeUserCallback;
        this.currentUserIndex = 0;

        // Ensure that render is bound correctly.
        this.render = this.render.bind(this);
        this.setUserId = this.setUserId.bind(this);
    }

    /**
     * Set the current userid without rendering the change.
     * To show the user, call showUser too.
     *
     * @param {Number} userId
     */
    setUserId(userId) {
        // Determine the current index based on the user ID.
        const userIndex = this.userList.findIndex(user => {
            return user.id === parseInt(userId);
        });

        if (userIndex === -1) {
            throw Error(`User with id ${userId} not found`);
        }

        this.currentUserIndex = userIndex;
    }

    /**
     * Render the user picker.
     */
    async render() {
        // Create the root node.
        this.root = document.createElement('div');

        const {html, js} = await this.renderNavigator();
        Templates.replaceNodeContents(this.root, html, js);

        // Call the showUser function to show the first user immediately.
        await this.showUser(this.currentUser);

        // Ensure that the event listeners are all bound.
        this.registerEventListeners();
    }

    /**
     * Render the navigator itself.
     *
     * @returns {Promise}
     */
    renderNavigator() {
        return Templates.renderForPromise(`${templatePath}/user_picker`, {});
    }

    /**
     * Render the current user details for the picker.
     *
     * @param {Object} context The data used to render the user picker.
     * @returns {Promise}
     */
    renderUserChange(context) {
        return Templates.renderForPromise(`${templatePath}/user_picker/user`, context);
    }

    /**
     * Show the specified user in the picker.
     *
     * @param {Object} user
     */
    async showUser(user) {
        const [{html, js}] = await Promise.all([this.renderUserChange(user), this.showUserCallback(user)]);
        const userRegion = this.root.querySelector(Selectors.regions.userRegion);
        Templates.replaceNodeContents(userRegion, html, js);

        // Update the hidden now-grading region so screen readers can announce the user that's currently being graded.
        const currentUserRegion = this.root.querySelector(Selectors.regions.currentUser);
        currentUserRegion.textContent = await getString('nowgradinguser', 'mod_forum', user.fullname);
    }

    /**
     * Register the event listeners for the user picker.
     */
    registerEventListeners() {
        this.root.addEventListener('click', async(e) => {
            const button = e.target.closest(Selectors.actions.changeUser);

            if (button) {
                const result = await this.preChangeUserCallback(this.currentUser);

                if (!result.failed) {
                    this.updateIndex(parseInt(button.dataset.direction));
                    await this.showUser(this.currentUser);
                }
            }
        });
    }

    /**
     * Update the current user index.
     *
     * @param {Number} direction
     * @returns {Number}}
     */
    updateIndex(direction) {
        this.currentUserIndex += direction;

        // Loop around the edges.
        if (this.currentUserIndex < 0) {
            this.currentUserIndex = this.userList.length - 1;
        } else if (this.currentUserIndex > this.userList.length - 1) {
            this.currentUserIndex = 0;
        }

        return this.currentUserIndex;
    }

    /**
     * Get the details of the user currently shown with the total number of users, and the 1-indexed count of the
     * current user.
     *
     * @returns {Object}
     */
    get currentUser() {
        return {
            ...this.userList[this.currentUserIndex],
            total: this.userList.length,
            displayIndex: this.currentUserIndex + 1,
        };
    }

    /**
     * Get the root node for the User Picker.
     *
     * @returns {HTMLElement}
     */
    get rootNode() {
        return this.root;
    }
}

/**
 * Create a new user picker.
 *
 * @param {Array} users The list of users
 * @param {Function} showUserCallback The function to call to show a specific user
 * @param {Function} preChangeUserCallback The fucntion to call to save the grade for the current user
 * @param {Number} [currentUserID] The userid of the current user
 * @returns {UserPicker}
 */
export default async(
    users,
    showUserCallback,
    preChangeUserCallback,
    {
        initialUserId = null,
    } = {}
) => {
    const userPicker = new UserPicker(users, showUserCallback, preChangeUserCallback);
    if (initialUserId) {
        userPicker.setUserId(initialUserId);
    }
    await userPicker.render();

    return userPicker;
};
