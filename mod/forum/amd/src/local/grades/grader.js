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
 * @module     mod_forum/local/grades/grader
 * @package    mod_forum
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Templates from 'core/templates';
// TODO import Notification from 'core/notification';
import Selectors from './local/grader/selectors';
import * as UserPicker from './local/grader/user_picker';
import {createLayout as createFullScreenWindow} from 'mod_forum/local/layout/fullscreen';

const templateNames = {
    grader: {
        app: 'mod_forum/local/grades/grader',
    },
};

const displayUserPicker = (root, html) => {
    const pickerRegion = root.querySelector(Selectors.regions.pickerRegion);
    Templates.replaceNodeContents(pickerRegion, html, '');
};

const getUpdateUserContentFunction = (root, getContentForUser) => {
    return async(user) => {
        const [
            {html, js},
        ] = await Promise.all([
            getContentForUser(user.id).then((html, js) => {
                return {html, js};
            }),
        ]);
        Templates.replaceNodeContents(root.querySelector(Selectors.regions.moduleReplace), html, js);
    };
};

const registerEventListeners = (graderLayout) => {
    const graderContainer = graderLayout.getContainer();
    graderContainer.addEventListener('click', (e) => {
        if (e.target.closest(Selectors.buttons.toggleFullscreen)) {
            e.stopImmediatePropagation();
            e.preventDefault();
            graderLayout.toggleFullscreen();
        } else if (e.target.closest(Selectors.buttons.closeGrader)) {
            e.stopImmediatePropagation();
            e.preventDefault();

            graderLayout.close();
        }
    });
};

// Make this explicit rather than object
export const launch = async(getListOfUsers, getContentForUser, {
    initialUserId = 0,
} = {}) => {

    const [
        graderLayout,
        graderHTML,
        userList,
    ] = await Promise.all([
        createFullScreenWindow({fullscreen: false, showLoader: false}),
        Templates.render(templateNames.grader.app, {}),
        getListOfUsers(),
    ]);
    const graderContainer = graderLayout.getContainer();

    Templates.replaceNodeContents(graderContainer, graderHTML, '');
    registerEventListeners(graderLayout);
    const updateUserContent = getUpdateUserContentFunction(graderContainer, getContentForUser);

    const pickerHTML = await UserPicker.buildPicker(userList, initialUserId, updateUserContent);
    displayUserPicker(graderContainer, pickerHTML);
};
