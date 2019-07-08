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
 * @package    mod_forum
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Selectors from './user_picker/selectors';

const renderNavigator = () => {
    return Templates.render('mod_forum/local/grades/local/grader/user_picker', {});
};

const renderUserChange = (context) => {
    return Templates.render('mod_forum/local/grades/local/grader/user_picker/user', context);
};

const showUser = async(root, users, currentUserIndex, showUserCallback) => {
    const user = {
        ...users[currentUserIndex],
        total: users.length,
        displayIndex: currentUserIndex + 1,
    };
    const [html] = await Promise.all([renderUserChange(user), showUserCallback(user)]);
    const userRegion = root.querySelector(Selectors.regions.userRegion);
    Templates.replaceNodeContents(userRegion, html, '');
};

const bindEvents = (root, users, currentUserIndex, showUserCallback) => {
    root.addEventListener('click', (e) => {
        const button = e.target.closest(Selectors.actions.changeUser);
        if (button) {
            currentUserIndex += parseInt(button.dataset.direction);
            showUser(root, users, currentUserIndex, showUserCallback);
        }
    });
};

export const buildPicker = async(users, currentUserID, showUserCallback) => {
    let root = document.createElement('div');

    const [html] = await Promise.all([renderNavigator()]);
    Templates.replaceNodeContents(root, html, '');

    const currentUserIndex = users.findIndex((user) => {
        return user.id === parseInt(currentUserID);
    });

    await showUser(root, users, currentUserIndex, showUserCallback);

    bindEvents(root, users, currentUserIndex, showUserCallback);

    return root;
};
