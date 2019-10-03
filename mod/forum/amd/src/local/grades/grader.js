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
import getGradingPanelFunctions from './local/grader/gradingpanel';
import {add as addToast} from 'core/toast';
import {get_string as getString} from 'core/str';

const templateNames = {
    grader: {
        app: 'mod_forum/local/grades/grader',
    },
};

const displayUserPicker = (root, html) => {
    const pickerRegion = root.querySelector(Selectors.regions.pickerRegion);
    Templates.replaceNodeContents(pickerRegion, html, '');
};

const fetchContentFromRender = (html, js) => {
    return [html, js];
};

const getUpdateUserContentFunction = (root, getContentForUser, getGradeForUser) => {
    return async(user) => {
        const [
            [html, js],
            userGrade,
        ] = await Promise.all([
            getContentForUser(user.id).then(fetchContentFromRender),
            getGradeForUser(user.id),
        ]);
        Templates.replaceNodeContents(root.querySelector(Selectors.regions.moduleReplace), html, js);

        const [
            gradingPanelHtml,
            gradingPanelJS
        ] = await Templates.render(userGrade.templatename, userGrade.grade).then(fetchContentFromRender);
        Templates.replaceNodeContents(root.querySelector(Selectors.regions.gradingPanel), gradingPanelHtml, gradingPanelJS);
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

/**
 * Get the function used to save a user grade.
 *
 * @param {Element} root The contaienr
 * @param {Function} setGradeForUser The function that will be called.
 * @return {Function}
 */
const getSaveUserGradeFunction = (root, setGradeForUser) => {
    return async user => {
        try {
            const result = await setGradeForUser(user.id, root.querySelector(Selectors.regions.gradingPanel));
            addToast(await getString('grades:gradesavedfor', 'mod_forum', user));

            return result;
        } catch (error) {
            throw error;
        }
    };
};

// Make this explicit rather than object
export const launch = async(getListOfUsers, getContentForUser, getGradeForUser, setGradeForUser, {
    initialUserId = 0, moduleName
} = {}) => {

    const [
        graderLayout,
        graderHTML,
        userList,
    ] = await Promise.all([
        createFullScreenWindow({fullscreen: false, showLoader: false}),
        Templates.render(templateNames.grader.app, {moduleName: moduleName}),
        getListOfUsers(),
    ]);
    const graderContainer = graderLayout.getContainer();

    Templates.replaceNodeContents(graderContainer, graderHTML, '');
    registerEventListeners(graderLayout);
    const updateUserContent = getUpdateUserContentFunction(graderContainer, getContentForUser, getGradeForUser);

    const pickerHTML = await UserPicker.buildPicker(
        userList,
        initialUserId,
        updateUserContent,
        getSaveUserGradeFunction(graderContainer, setGradeForUser)
    );

    displayUserPicker(graderContainer, pickerHTML);
};

export {getGradingPanelFunctions};
